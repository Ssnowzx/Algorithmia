<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use InvalidArgumentException;

/**
 * Quais funcionalidades esta instituição enxerga. Etapa E.1.
 *
 * O catálogo — quais flags existem e o que valem por padrão — está em `config/flags.php`.
 * O banco guarda só as exceções, em `tenants.flags`.
 *
 * **Uma chave desconhecida levanta exceção, e não devolve `false`.** É a decisão de
 * projeto que faz este serviço valer alguma coisa: `ativa('turmsa')` devolvendo `false`
 * desligaria as turmas em produção, para sempre, e nada apontaria o erro de digitação.
 * Falhar alto num nome errado é o único jeito de o teste pegar o que o revisor não pegou.
 *
 * Registrado como singleton: o `ResolverTenant` entrega o `Tenant` que já carregou, e
 * nenhuma consulta a mais acontece por requisição.
 */
final class Flags
{
    private ?Tenant $tenant = null;

    /** Distingue "ainda não procurei o tenant" de "procurei e não há nenhum". */
    private bool $resolvido = false;

    public function __construct(private readonly ContextoDoTenant $contexto) {}

    /** O `ResolverTenant` entrega aqui o tenant que já carregou para resolver o `Host`. */
    public function definir(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
        $this->resolvido = true;
    }

    public function ativa(string $chave): bool
    {
        $padrao = $this->padraoDe($chave);

        // Sem tenancy não há instituição de quem herdar exceção: vale o código.
        if (! config('tenancy.ativo')) {
            return $padrao;
        }

        $tenant = $this->tenant();

        if ($tenant === null) {
            return $padrao;
        }

        $flags = $tenant->flags;

        return array_key_exists($chave, $flags) ? (bool) $flags[$chave] : $padrao;
    }

    /**
     * O estado de todas as flags declaradas, para esta instituição.
     *
     * @return array<string,array{ativa:bool,padrao:bool,sobrescrita:bool,descricao:string}>
     */
    public function todas(?Tenant $tenant = null): array
    {
        $tenant ??= $this->tenant();
        $sobrescritas = $tenant === null ? [] : $tenant->flags;

        $estado = [];

        foreach ($this->catalogo() as $chave => $declaracao) {
            $padrao = (bool) $declaracao['padrao'];
            $sobrescrita = array_key_exists($chave, $sobrescritas);

            $estado[$chave] = [
                'ativa' => $sobrescrita ? (bool) $sobrescritas[$chave] : $padrao,
                'padrao' => $padrao,
                'sobrescrita' => $sobrescrita,
                'descricao' => (string) $declaracao['descricao'],
            ];
        }

        return $estado;
    }

    /**
     * Liga, desliga ou devolve a flag ao padrão do código.
     *
     * `null` **remove a chave** em vez de gravar o valor do padrão. A diferença aparece no
     * dia em que o padrão muda: uma instituição que nunca opinou acompanha a mudança; uma
     * que gravou `false` explicitamente continua desligada, como pediu.
     */
    public function definirNoTenant(Tenant $tenant, string $chave, ?bool $valor): void
    {
        $this->padraoDe($chave);

        $flags = $tenant->flags;

        if ($valor === null) {
            unset($flags[$chave]);
        } else {
            $flags[$chave] = $valor;
        }

        $tenant->flags = $flags;
        $tenant->save();

        // O tenant em memória pode ser outra instância que a do pedido corrente.
        if ($this->tenant?->is($tenant) === true) {
            $this->tenant->flags = $flags;
        }
    }

    /** @return array<string,array{padrao:bool,descricao:string}> */
    public function catalogo(): array
    {
        /** @var array<string,array{padrao:bool,descricao:string}> $catalogo */
        $catalogo = config('flags', []);

        return $catalogo;
    }

    private function padraoDe(string $chave): bool
    {
        $catalogo = $this->catalogo();

        if (! array_key_exists($chave, $catalogo)) {
            throw new InvalidArgumentException(sprintf(
                'Flag desconhecida: "%s". As declaradas em config/flags.php são: %s.',
                $chave,
                implode(', ', array_keys($catalogo)) ?: '(nenhuma)'
            ));
        }

        return (bool) $catalogo[$chave]['padrao'];
    }

    /**
     * Em HTTP, o `ResolverTenant` já entregou o tenant. Fora dele — comandos de console,
     * testes — o contexto existe no PostgreSQL, e daí sai o id.
     */
    private function tenant(): ?Tenant
    {
        if ($this->resolvido) {
            return $this->tenant;
        }

        $id = $this->contexto->atual();

        $this->tenant = $id === null ? null : Tenant::query()->find($id);
        $this->resolvido = true;

        return $this->tenant;
    }
}
