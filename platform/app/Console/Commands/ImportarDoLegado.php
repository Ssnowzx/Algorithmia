<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\ContextoDoTenant;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Copia o banco MySQL do jogo em PHP puro para o PostgreSQL do port.
 *
 * Preserva os IDs. Isso não é conveniência: `ConquistaService` referencia as
 * fases secundárias por ID fixo (`config('jogo.fases_secundarias')`), e com IDs
 * novos a conquista `arquivista_do_vazio` morreria em silêncio — o jogador nunca
 * a receberia, e nada acusaria o erro.
 *
 * Nunca escreve no legado: o jogo antigo precisa seguir de pé durante a janela de
 * coexistência, e um rollback operacional depende disso.
 *
 * O `--dry-run` executa a importação inteira dentro de uma transação e a desfaz.
 * Um ensaio que não escreve mas também não exercita as chaves estrangeiras e a
 * conversão de tipos não prova nada.
 */
final class ImportarDoLegado extends Command
{
    protected $signature = 'algorithmia:importar
        {--dry-run : Executa tudo e desfaz ao final, apenas relatando}
        {--truncar : Esvazia as tabelas de destino antes de copiar}
        {--lote=500 : Quantas linhas por lote de leitura}';

    protected $description = 'Importa conteúdo, contas e progresso do MySQL legado para o PostgreSQL';

    /**
     * Ordem de cópia — respeita as chaves estrangeiras. `fases` tem auto-referência
     * (`requisito_fase_id`) e por isso é copiada em duas passadas.
     *
     * bools: colunas TINYINT(1) que viraram boolean no PostgreSQL.
     * jsons: colunas JSON que viraram jsonb.
     *
     * @var array<string,array{chave:list<string>,bools:list<string>,jsons:list<string>}>
     */
    private const TABELAS = [
        'usuarios' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'mestres' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'itens' => ['chave' => ['id'], 'bools' => ['compravel'], 'jsons' => ['efeito']],
        'conquistas' => ['chave' => ['id'], 'bools' => ['secreta'], 'jsons' => []],
        'personagens' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'fases' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'desafios' => ['chave' => ['id'], 'bools' => [], 'jsons' => ['opcoes', 'resposta']],
        'dialogos' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'inventario' => ['chave' => ['id'], 'bools' => ['equipado'], 'jsons' => []],
        'progresso_fases' => ['chave' => ['id'], 'bools' => ['usou_ia'], 'jsons' => []],
        'conquistas_personagem' => ['chave' => ['personagem_id', 'conquista_id'], 'bools' => [], 'jsons' => []],
        'escolhas' => ['chave' => ['id'], 'bools' => [], 'jsons' => []],
        'respostas_log' => ['chave' => ['id'], 'bools' => ['correta', 'usou_ia'], 'jsons' => []],
    ];

    public function handle(): int
    {
        // Um comando de console não tem `Host` de onde deduzir o tenant. Sem contexto,
        // `destinoEstaPronto()` contaria zero linhas num banco cheio — e a importação
        // sobrescreveria em silêncio o que julgasse inexistente.
        if (! config('tenancy.ativo')) {
            return $this->importar();
        }

        $contexto = app(ContextoDoTenant::class);

        return $contexto->usar($contexto->tenantUnico(), $this->importar(...));
    }

    private function importar(): int
    {
        $ensaio = (bool) $this->option('dry-run');
        $legado = DB::connection('legado');
        $destino = DB::connection('pgsql');

        try {
            $legado->getPdo();
        } catch (Throwable $e) {
            $this->error('Não consegui abrir o banco legado. Confira as variáveis LEGADO_DB_* no .env.');

            return self::FAILURE;
        }

        if (! $this->destinoEstaPronto($destino)) {
            return self::FAILURE;
        }

        $this->info($ensaio ? '→ ENSAIO: nada será gravado ao final.' : '→ Importação real.');

        $destino->beginTransaction();

        try {
            if ($this->option('truncar')) {
                $this->esvaziar($destino);
            }

            foreach (array_keys(self::TABELAS) as $tabela) {
                $copiadas = $tabela === 'fases'
                    ? $this->copiarFases($legado, $destino)
                    : $this->copiar($legado, $destino, $tabela);

                $this->line(sprintf('  %-24s %6d linhas', $tabela, $copiadas));
            }

            $relatorio = $this->reconciliar($legado, $destino);
            $sanidade = $this->verificarSanidade($destino);

            if ($ensaio) {
                $destino->rollBack();
            } else {
                $this->sincronizarSequencias($destino);
                $destino->commit();
            }
        } catch (Throwable $e) {
            $destino->rollBack();
            $this->error('Importação abortada e desfeita: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->table(['Tabela', 'Legado', 'Destino', 'Situação'], $relatorio);
        $this->newLine();

        foreach ($sanidade as $linha) {
            $this->line($linha);
        }

        $divergiu = collect($relatorio)->contains(fn (array $l): bool => $l[3] !== 'ok');
        if ($divergiu) {
            $this->error('Reconciliação divergente. Nada foi mantido.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info($ensaio
            ? 'Ensaio concluído. Nenhuma linha gravada. Rode sem --dry-run para valer.'
            : 'Importação concluída e confirmada.');

        return self::SUCCESS;
    }

    /** Recusa sobrescrever dados existentes sem que alguém tenha pedido. */
    private function destinoEstaPronto(ConnectionInterface $destino): bool
    {
        if ($this->option('truncar')) {
            return true;
        }

        foreach (array_keys(self::TABELAS) as $tabela) {
            if ($destino->table($tabela)->exists()) {
                $this->error("A tabela '{$tabela}' do destino já tem dados. Use --truncar para substituí-los.");

                return false;
            }
        }

        return true;
    }

    private function esvaziar(ConnectionInterface $destino): void
    {
        // `TRUNCATE` **ignora** o RLS — ele é uma operação de tabela, não de linha. Com
        // duas instituições no banco, `--truncar` apagaria as duas. A policy não
        // protegeria: ela nem é consultada.
        $tenants = DB::connection('pgsql_dono')->table('tenants')->count();

        if ($tenants > 1) {
            throw new RuntimeException(sprintf(
                '--truncar apagaria as %d instituições do banco: TRUNCATE ignora RLS. Recusado.',
                $tenants
            ));
        }

        // CASCADE alcança as tabelas que referenciam estas. `recompensas_batalha` entra
        // junto de propósito: as batalhas recompensadas pertencem ao progresso que está
        // sendo trocado.
        //
        // Sem `RESTART IDENTITY`: reiniciar a identidade exige **ser dono da sequência**,
        // e a aplicação não é dona de nada — é o que a torna sujeita ao RLS. A cláusula
        // era redundante de todo modo: `sincronizarSequencias()` roda logo adiante e põe
        // cada sequência no `MAX(id)` importado, que é o valor que de fato importa.
        $tabelas = implode(', ', [...array_keys(self::TABELAS), 'recompensas_batalha']);
        $destino->statement("TRUNCATE TABLE {$tabelas} CASCADE");
    }

    private function copiar(ConnectionInterface $legado, ConnectionInterface $destino, string $tabela): int
    {
        $spec = self::TABELAS[$tabela];
        $copiadas = 0;

        $legado->table($tabela)
            ->orderBy($spec['chave'][0])
            ->chunk((int) $this->option('lote'), function ($linhas) use ($destino, $tabela, $spec, &$copiadas): void {
                $lote = array_map(fn (object $l): array => $this->converter((array) $l, $spec), $linhas->all());
                $destino->table($tabela)->insert($lote);
                $copiadas += count($lote);
            });

        return $copiadas;
    }

    /**
     * `fases.requisito_fase_id` aponta para outra fase. Hoje sempre para uma de ID
     * menor, mas depender disso é apostar na ordem dos dados: a primeira passada
     * copia sem o requisito, a segunda o preenche.
     */
    private function copiarFases(ConnectionInterface $legado, ConnectionInterface $destino): int
    {
        $requisitos = [];
        $copiadas = 0;

        $legado->table('fases')->orderBy('id')->chunk((int) $this->option('lote'),
            function ($linhas) use ($destino, &$requisitos, &$copiadas): void {
                $lote = [];
                foreach ($linhas as $linha) {
                    $fase = (array) $linha;
                    if ($fase['requisito_fase_id'] !== null) {
                        $requisitos[(int) $fase['id']] = (int) $fase['requisito_fase_id'];
                    }
                    $fase['requisito_fase_id'] = null;
                    $lote[] = $fase;
                }
                $destino->table('fases')->insert($lote);
                $copiadas += count($lote);
            });

        foreach ($requisitos as $faseId => $requisitoId) {
            $destino->table('fases')->where('id', $faseId)->update(['requisito_fase_id' => $requisitoId]);
        }

        return $copiadas;
    }

    /**
     * @param  array<string,mixed>  $linha
     * @param  array{chave:list<string>,bools:list<string>,jsons:list<string>}  $spec
     * @return array<string,mixed>
     */
    private function converter(array $linha, array $spec): array
    {
        // TINYINT(1) chega como 0/1; o PostgreSQL recusa inteiro numa coluna boolean.
        foreach ($spec['bools'] as $coluna) {
            $linha[$coluna] = (bool) $linha[$coluna];
        }

        // O JSON do MySQL chega como texto; o PostgreSQL o converte ao gravar em jsonb.
        foreach ($spec['jsons'] as $coluna) {
            if ($linha[$coluna] !== null && ! is_string($linha[$coluna])) {
                $linha[$coluna] = json_encode($linha[$coluna], JSON_THROW_ON_ERROR);
            }
        }

        return $linha;
    }

    /**
     * Os IDs vieram explícitos, então as sequências continuam em 1 e o próximo
     * INSERT colidiria. Isto as reposiciona.
     */
    private function sincronizarSequencias(ConnectionInterface $destino): void
    {
        foreach (self::TABELAS as $tabela => $spec) {
            if ($spec['chave'] !== ['id']) {
                continue; // chave composta: não há sequência
            }
            $destino->statement(
                "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE((SELECT MAX(id) FROM {$tabela}), 1))",
                [$tabela]
            );
        }
    }

    /** @return list<array{0:string,1:int,2:int,3:string}> */
    private function reconciliar(ConnectionInterface $legado, ConnectionInterface $destino): array
    {
        $relatorio = [];
        foreach (array_keys(self::TABELAS) as $tabela) {
            $origem = $legado->table($tabela)->count();
            $chegada = $destino->table($tabela)->count();
            $relatorio[] = [$tabela, $origem, $chegada, $origem === $chegada ? 'ok' : 'DIVERGENTE'];
        }

        return $relatorio;
    }

    /**
     * Verificações que uma contagem igual não pegaria.
     *
     * @return list<string>
     */
    private function verificarSanidade(ConnectionInterface $destino): array
    {
        $linhas = [];

        /** @var list<int> $secundarias */
        $secundarias = config('jogo.fases_secundarias');
        $encontradas = $destino->table('fases')
            ->whereIn('id', $secundarias)
            ->where('tipo', 'secundaria')
            ->count();

        $linhas[] = $encontradas === count($secundarias)
            ? '  ✓ IDs das fases secundárias preservados — arquivista_do_vazio continua alcançável'
            : '  ✗ IDs das fases secundárias NÃO preservados — arquivista_do_vazio ficaria inalcançável';

        $fragmento = $destino->table('itens')->where('svg_slug', config('jogo.item_fragmento_ia'))->exists();
        $linhas[] = $fragmento
            ? '  ✓ Fragmento da IA Ancestral presente no catálogo'
            : '  ✗ Fragmento da IA Ancestral ausente — o motor não conseguiria resolvê-lo';

        $orfas = $destino->table('fases')
            ->whereNotNull('requisito_fase_id')
            ->whereNotIn('requisito_fase_id', fn ($q) => $q->select('id')->from('fases'))
            ->count();
        $linhas[] = $orfas === 0
            ? '  ✓ Nenhuma fase com requisito órfão'
            : "  ✗ {$orfas} fase(s) com requisito apontando para fase inexistente";

        return $linhas;
    }
}
