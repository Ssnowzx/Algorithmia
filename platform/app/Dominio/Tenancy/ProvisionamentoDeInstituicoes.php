<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDominio;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Criar, ligar e desligar uma instituição. Etapa E.2.
 *
 * **Provisionar e semear são dois atos, e entre eles a escola é injogável.** Nesse
 * intervalo ela não tem fase, desafio nem mestre: um aluno veria um mapa vazio, e o
 * `algorithmia:smoke` — que varre as instituições ativas e é o portão do `bin/deploy.sh` —
 * reprovaria **todo deploy**, até alguém desconfiar de uma escola pela metade. Foi assim
 * que o ensaio do corte (C.6) reprovou um build correto.
 *
 * O `DEFAULT false` da coluna `ativo` impede o primeiro erro. Este serviço impede o
 * segundo: **`ativar()` roda o smoke da instituição antes de ligá-la**, e recusa uma
 * escola injogável. A ordem deixa de ser uma regra escrita na prosa do runbook e passa a
 * ser uma regra que o código não deixa quebrar.
 *
 * Não há `--forcar`. Um portão com botão de contornar é um portão que ninguém fecha. Se um
 * dia for mesmo necessário, o escape é um `UPDATE tenants SET ativo = true` escrito à mão,
 * por alguém que sabe o que está fazendo — e não uma opção de linha de comando que a
 * próxima pessoa vai copiar do histórico do shell.
 */
final class ProvisionamentoDeInstituicoes
{
    public function __construct(
        private readonly Flags $flags,
        private readonly ConteudoPorInstituicao $conteudo,
    ) {}

    /**
     * Uma instituição nova, **desligada**, com o seu domínio primário.
     *
     * @throws ProvisionamentoInvalido
     */
    public function provisionar(string $nome, ?string $slug, string $host): Tenant
    {
        $nome = trim($nome);
        $slug = Str::slug($slug ?? $nome);
        $host = mb_strtolower(trim($host));

        $this->validar($nome, $slug, $host);

        // As duas linhas nascem juntas ou não nascem: uma instituição sem domínio é
        // inalcançável, e o `ResolverTenant` a esconderia sem dizer por quê.
        return DB::transaction(function () use ($nome, $slug, $host): Tenant {
            // `ativo` NÃO é passado: o DEFAULT da coluna é `false`, e é ele que manda.
            $tenant = Tenant::create(['nome' => $nome, 'slug' => $slug]);

            TenantDominio::create(['tenant_id' => $tenant->id, 'host' => $host, 'primario' => true]);

            return $tenant->refresh();
        });
    }

    /**
     * Liga a instituição — se ela estiver jogável.
     *
     * O veredito é do `algorithmia:smoke`, e não de uma checagem paralela escrita aqui:
     * duas definições de "jogável" divergem no dia em que alguém mexer numa delas.
     *
     * @throws InstituicaoInjogavel
     */
    public function ativar(Tenant $tenant): void
    {
        if ($tenant->ativo) {
            return;
        }

        $codigo = Artisan::call('algorithmia:smoke', ['--tenant' => $tenant->slug]);

        if ($codigo !== 0) {
            throw new InstituicaoInjogavel($tenant, Artisan::output(), $this->comoConsertar($tenant));
        }

        $tenant->update(['ativo' => true]);
    }

    /**
     * O que dizer a quem acabou de ver o smoke reprovar.
     *
     * Mandar rodar `algorithmia:importar --tenant=X` só funciona enquanto nenhuma outra
     * instituição tem conteúdo: os ids do jogo são globais, e o importador recusa a segunda
     * escola. Instruir o operador a rodar um comando que não pode funcionar é pior do que
     * não instruir nada.
     */
    private function comoConsertar(Tenant $tenant): string
    {
        $dona = $this->conteudo->instituicaoComConteudo(exceto: $tenant->id);

        if ($dona !== null) {
            return sprintf(
                'E ela NÃO pode receber conteúdo: os ids do jogo são globais, e eles pertencem à '
                .'instituição "%s". Hoje o port serve uma instituição com conteúdo — '
                .'ver RUNBOOK §11.5.',
                $dona->slug,
            );
        }

        return sprintf('Semeie o conteúdo dela: php artisan algorithmia:importar --tenant=%s', $tenant->slug);
    }

    /**
     * Desliga a instituição. Nunca roda o smoke: apagar o domínio do ar é sempre seguro,
     * e exigir que a escola esteja jogável para poder desligá-la seria trancar por dentro
     * exatamente a escola quebrada que se quer tirar do ar.
     */
    public function desativar(Tenant $tenant): void
    {
        $tenant->update(['ativo' => false]);
    }

    /**
     * Uma chave fora de `config/flags.php` levanta `InvalidArgumentException` — que é uma
     * `LogicException`, e não um erro de uso. É de propósito: no código da aplicação, uma
     * flag inexistente é bug do programador. Quem a recebe de um humano — a linha de
     * comando, a tela do console — a apara e a transforma em frase.
     *
     * @throws InvalidArgumentException
     */
    public function definirFlag(Tenant $tenant, string $chave, ?bool $valor): void
    {
        $this->flags->definirNoTenant($tenant, $chave, $valor);
    }

    /** @throws ProvisionamentoInvalido */
    private function validar(string $nome, string $slug, string $host): void
    {
        if ($nome === '') {
            throw new ProvisionamentoInvalido('O nome da instituição não pode ser vazio.');
        }

        if ($slug === '') {
            throw new ProvisionamentoInvalido('O slug não pode ser vazio. Passe `--slug`.');
        }

        // `tenant_dominios.host` guarda o host, e só ele: sem esquema, sem porta, sem
        // caminho. `parse_url($APP_URL, PHP_URL_HOST)` — que é de onde vem o tenant padrão
        // — devolve exatamente isso, e um `https://` colado aqui nunca casaria com o
        // `$request->getHost()` do `ResolverTenant`. O sintoma seria 404 com o banco cheio.
        if ($host === '' || preg_match('#^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$#', $host) !== 1) {
            throw new ProvisionamentoInvalido(sprintf(
                'Host inválido: "%s". Passe só o host — sem `https://`, sem porta, sem barra.',
                $host
            ));
        }

        if (Tenant::query()->where('slug', $slug)->exists()) {
            throw new ProvisionamentoInvalido(sprintf('Já existe uma instituição com o slug "%s".', $slug));
        }

        // O índice único do banco é sobre `lower(host)` e é a garantia de verdade. Esta
        // consulta existe para o erro chegar como frase, e não como violação de constraint
        // no meio de um provisionamento.
        if (TenantDominio::query()->comHost($host)->exists()) {
            throw new ProvisionamentoInvalido(sprintf('O host "%s" já pertence a outra instituição.', $host));
        }
    }
}
