<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Combate\BatalhaEmMemoria;
use App\Dominio\Combate\CorretorDeRespostas;
use App\Dominio\Combate\MotorDeBatalha;
use App\Dominio\Combate\SorteioAntiRepeticao;
use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\Fase;
use App\Models\Item;
use App\Models\Personagem;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

/**
 * Verificação pós-deploy: o app subiu, mas o jogo funciona?
 *
 * Um health check diz que o PHP respondeu e o banco aceitou um `SELECT 1`. Não diz
 * que o conteúdo está lá, que os IDs sobreviveram à importação, nem que o motor
 * consegue jogar. Este comando diz.
 *
 * A verificação profunda joga uma batalha de verdade dentro de uma transação e a
 * desfaz. Um smoke que não exercita o motor não prova nada — foi assim que o
 * `--dry-run` da importação pegou o que pegaria só no dia do corte.
 */
final class Smoke extends Command
{
    protected $signature = 'algorithmia:smoke
        {--rasa : Pula a batalha de verdade (mais rápido, prova menos)}
        {--sem-conteudo : Pula tudo o que depende da importação (para a CI, com banco vazio)}
        {--tenant= : Verifica só esta instituição (slug). Sem a opção, verifica todas.}';

    protected $description = 'Verifica, após o deploy, que o jogo está jogável';

    /** @var list<string> */
    private array $falhas = [];

    public function handle(): int
    {
        // Um comando de console não tem `Host` de onde deduzir o tenant, e sem contexto
        // toda consulta às tabelas do jogo devolve zero linhas — o smoke reprovaria
        // dizendo "conteúdo não importado" contra um banco cheio.
        if (! config('tenancy.ativo')) {
            return $this->verificacoes();
        }

        $contexto = app(ContextoDoTenant::class);

        // Sem `--tenant`, varre TODAS as instituições ativas. Pedir um `tenantUnico()`
        // aqui faria o `bin/deploy.sh` quebrar no dia em que a segunda escola entrasse:
        // o smoke é o portão do deploy, e um portão que só abre para um cliente não é um
        // portão. Um deploy que deixa uma escola injogável não pode ser promovido.
        // Um slug errado precisa virar mensagem, não stack trace: este comando roda dentro
        // do `bin/deploy.sh`, e quem o lê está no meio de um deploy.
        try {
            $tenants = $this->option('tenant') !== null
                ? [(object) ['id' => $contexto->tenantPorSlug((string) $this->option('tenant')), 'slug' => (string) $this->option('tenant')]]
                : $contexto->tenantsAtivos();
        } catch (RuntimeException $erro) {
            $this->error($erro->getMessage());

            return self::FAILURE;
        }

        if ($tenants === []) {
            $this->error('Nenhuma instituição ativa. Rode as migrations.');

            return self::FAILURE;
        }

        $codigo = self::SUCCESS;

        foreach ($tenants as $tenant) {
            if (count($tenants) > 1) {
                $this->newLine();
                $this->line("<fg=cyan>── instituição: {$tenant->slug}</>");
            }

            $this->falhas = [];

            if ($contexto->usar($tenant->id, $this->verificacoes(...)) !== self::SUCCESS) {
                $codigo = self::FAILURE;
            }
        }

        return $codigo;
    }

    private function verificacoes(): int
    {
        // Estrutural: vale mesmo num banco recém-migrado, sem uma linha de conteúdo.
        $this->verificar('Banco responde', fn (): bool => DB::connection()->select('SELECT 1') !== []);
        $this->verificar('As 14 tabelas existem', $this->tabelasExistem(...));
        $this->verificar('Nenhuma fase com requisito órfão', $this->semRequisitoOrfao(...));
        $this->verificar('Sessão em banco (o gabarito não cabe num cookie)', fn (): bool => config('session.driver') === 'database');
        $this->verificar('APP_DEBUG desligado', fn (): bool => config('app.debug') === false);

        if (! $this->option('sem-conteudo')) {
            $this->verificar('Conteúdo importado', $this->temConteudo(...));
            $this->verificar('IDs das fases secundárias preservados', $this->idsSecundariasPreservados(...));
            $this->verificar('Fragmento da IA no catálogo', fn (): bool => Item::fragmentoDaIa() !== null);
            $this->verificar('Confronto final alcançável', fn (): bool => Fase::confrontoFinal() !== null);

            if (! $this->option('rasa')) {
                $this->verificar('O motor joga uma fase real', $this->motorJoga(...));
            }
        }

        $this->newLine();

        if ($this->falhas !== []) {
            $this->error(count($this->falhas).' verificação(ões) falharam. NÃO promova este deploy.');

            return self::FAILURE;
        }

        // A mensagem tem de dizer o que foi provado, não o que seria bom ter provado.
        $this->info(match (true) {
            (bool) $this->option('sem-conteudo') => 'Smoke estrutural OK — schema e configuração. O conteúdo NÃO foi verificado.',
            (bool) $this->option('rasa') => 'Smoke raso OK — o conteúdo está lá. O motor NÃO foi exercitado.',
            default => 'Smoke OK — o jogo está jogável.',
        });

        return self::SUCCESS;
    }

    /** @param  callable():bool  $checagem */
    private function verificar(string $nome, callable $checagem): void
    {
        try {
            $ok = $checagem();
        } catch (Throwable $e) {
            $ok = false;
            $this->line(sprintf('  <fg=red>✗</> %-48s %s', $nome, $e->getMessage()));
            $this->falhas[] = $nome;

            return;
        }

        $this->line(sprintf('  %s %s', $ok ? '<fg=green>✓</>' : '<fg=red>✗</>', $nome));

        if (! $ok) {
            $this->falhas[] = $nome;
        }
    }

    private function tabelasExistem(): bool
    {
        $tabelas = [
            'usuarios', 'mestres', 'itens', 'personagens', 'fases', 'desafios',
            'inventario', 'progresso_fases', 'conquistas', 'conquistas_personagem',
            'dialogos', 'escolhas', 'respostas_log', 'recompensas_batalha',
        ];

        foreach ($tabelas as $tabela) {
            if (! Schema::hasTable($tabela)) {
                throw new RuntimeException("faltou a tabela {$tabela}");
            }
        }

        return true;
    }

    private function temConteudo(): bool
    {
        foreach (['mestres' => 1, 'fases' => 1, 'desafios' => 1, 'itens' => 1, 'conquistas' => 1] as $tabela => $minimo) {
            $total = DB::table($tabela)->count();
            if ($total < $minimo) {
                throw new RuntimeException("{$tabela} está vazia — rodou algorithmia:importar?");
            }
        }

        return true;
    }

    /** Sem os IDs originais, a conquista `arquivista_do_vazio` fica inalcançável em silêncio. */
    private function idsSecundariasPreservados(): bool
    {
        /** @var list<int> $secundarias */
        $secundarias = config('jogo.fases_secundarias');

        $encontradas = Fase::query()->whereIn('id', $secundarias)->where('tipo', 'secundaria')->count();

        return $encontradas === count($secundarias);
    }

    private function semRequisitoOrfao(): bool
    {
        return Fase::query()
            ->whereNotNull('requisito_fase_id')
            ->whereNotIn('requisito_fase_id', Fase::query()->select('id'))
            ->doesntExist();
    }

    /**
     * Joga uma fase real, com um herói descartável, dentro de uma transação que é
     * sempre desfeita. Nada sobra no banco.
     */
    private function motorJoga(): bool
    {
        $fase = Fase::query()
            ->where('tipo', 'licao')
            ->whereHas('desafios')
            ->orderBy('ordem_global')
            ->first();

        if ($fase === null) {
            throw new RuntimeException('nenhuma lição com desafios');
        }

        DB::beginTransaction();

        try {
            $usuario = Usuario::create([
                'nome' => 'Sonda do Smoke',
                'email' => 'smoke+'.uniqid().'@algorithmia.invalid',
                'senha_hash' => 'nao-usado',
            ]);

            // Todos os campos explícitos: `create()` não reidrata os defaults do
            // banco, e um `nivel` nulo estoura no construtor de EstadoDeBatalha.
            $heroi = Personagem::create([
                'usuario_id' => $usuario->id, 'nome' => 'Sonda', 'classe' => 'mago',
                'nivel' => 1, 'xp' => 0,
                'hp_max' => 9999, 'hp_atual' => 9999, 'mp_max' => 99, 'mp_atual' => 99,
                'ouro' => 0, 'reputacao' => 0, 'capitulo' => 0,
            ]);

            $motor = new MotorDeBatalha(
                new BatalhaEmMemoria,
                new SorteioAntiRepeticao,
                new CorretorDeRespostas,
                new ServicoDeReputacao,
                new ServicoDeConquistas,
            );

            $estado = $motor->iniciar($heroi, $fase);
            if ($estado->desafios === []) {
                throw new RuntimeException('o sorteio não devolveu desafios');
            }

            // Responde com o gabarito do servidor até a vitória.
            $turnos = 0;
            while (($atual = $motor->estado()) !== null && ! $atual->finalizada && $turnos < 40) {
                $desafio = $atual->desafioAtual();
                if ($desafio === null) {
                    throw new RuntimeException('a batalha ficou sem desafio');
                }
                $motor->responder($desafio['resposta']);
                $turnos++;
            }

            $fim = $motor->estado();
            if ($fim?->resultado !== 'vitoria') {
                throw new RuntimeException('respondendo tudo certo, o herói não venceu');
            }

            // O gabarito não pode atravessar `paraCliente()`.
            if (str_contains((string) json_encode($fim->paraCliente()), 'resposta')) {
                throw new RuntimeException('o gabarito vazou para o cliente');
            }

            return true;
        } finally {
            DB::rollBack();
        }
    }
}
