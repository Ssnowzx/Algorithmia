<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Copia o **conteúdo do jogo** de uma instituição para outra, gerando ids novos.
 *
 * ## Por que não é o `algorithmia:importar`
 *
 * O importador é a ferramenta do **corte**: ele traz o MySQL do legado inteiro — contas,
 * heróis, progresso, inventário — e **preserva os ids**, porque o progresso importado
 * aponta para eles. Preservar ids só funciona uma vez: `fases.id` é chave primária global, e
 * a segunda instituição colidiria em `fases_pkey`.
 *
 * Uma segunda escola não quer os alunos da primeira. Ela quer o **conteúdo**: os mestres, as
 * fases, os desafios, os diálogos, os itens e o catálogo de conquistas. Nada de pessoas.
 * Este serviço copia exatamente isso, deixando o banco escolher ids novos e reescrevendo as
 * chaves estrangeiras ao longo do caminho.
 *
 * ## O que tornou isto possível
 *
 * Duas amarras foram cortadas antes:
 *
 * 1. **`arquivista_do_vazio` deixou de referenciar as fases 8, 14, 20 e 32 por id.** Agora
 *    são as fases de `tipo = 'secundaria'` da instituição. Com ids novos, a conquista
 *    continua alcançável — antes ela morria em silêncio.
 * 2. **`conquistas.codigo` deixou de ser único global.** O índice virou `(tenant_id, codigo)`.
 *    Sem isso, `arquivista_do_vazio` só podia existir em uma escola no banco inteiro.
 *
 * ## Como o contexto é manejado
 *
 * Lê no contexto da origem, escreve no contexto do destino, **dentro de uma transação só**.
 * `SET LOCAL` pode ser trocado no meio de uma transação, e é o que se faz aqui: qualquer
 * falha desfaz tudo, e o destino não fica pela metade. O RLS permanece de pé nas duas
 * pontas — não há conexão do dono, e cada leitura e cada escrita acontece dentro de um
 * contexto declarado.
 */
final class SemeadorDeConteudo
{
    /**
     * As tabelas de conteúdo, em ordem de chave estrangeira, e as FKs a reescrever.
     *
     * `usuarios`, `personagens`, `progresso_fases`, `inventario`, `respostas_log`,
     * `conquistas_personagem` e `escolhas` **não entram**: são pessoas e progresso, e uma
     * escola nova não herda os alunos de outra.
     *
     * @var array<string,array<string,string>>
     */
    private const CONTEUDO = [
        'mestres' => [],
        'itens' => [],
        'conquistas' => [],
        'fases' => ['mestre_id' => 'mestres', 'item_drop_id' => 'itens'],
        'desafios' => ['fase_id' => 'fases'],
        'dialogos' => ['fase_id' => 'fases'],
    ];

    /** As tabelas cujos ids precisam de mapa, porque alguém aponta para elas. */
    private const MAPEADAS = ['mestres', 'itens', 'fases'];

    public function __construct(private readonly ContextoDoTenant $contexto) {}

    /**
     * @return array<string,int> linhas copiadas por tabela
     *
     * @throws RuntimeException
     */
    public function semear(Tenant $de, Tenant $para): array
    {
        if ($de->is($para)) {
            throw new RuntimeException('A origem e o destino são a mesma instituição.');
        }

        /** @var array<string,int> $copiadas */
        $copiadas = DB::transaction(function () use ($de, $para): array {
            $anterior = $this->contexto->atual();

            $this->contexto->definirNaTransacao($de->id);
            $fonte = $this->ler();

            if ($fonte['fases'] === []) {
                throw new RuntimeException(sprintf('A instituição "%s" não tem conteúdo a copiar.', $de->slug));
            }

            $this->contexto->definirNaTransacao($para->id);

            if (DB::table('fases')->exists()) {
                throw new RuntimeException(sprintf(
                    'A instituição "%s" já tem conteúdo. Semear por cima criaria um mapa com duas cópias de tudo.',
                    $para->slug,
                ));
            }

            $copiadas = $this->escrever($fonte);

            $this->conferir($fonte, $para);

            // O contexto não sobrevive a este método, mesmo que a transação continue —
            // uma transação aninhada (um teste, por exemplo) seguiria cega ou, pior,
            // enxergando a escola errada.
            $anterior === null
                ? $this->contexto->limparNaTransacao()
                : $this->contexto->definirNaTransacao($anterior);

            return $copiadas;
        });

        return $copiadas;
    }

    /**
     * Lê tudo para a memória antes de trocar de contexto. São algumas centenas de linhas —
     * 955 desafios no conteúdo real —, e a alternativa seria alternar `SET LOCAL` a cada
     * lote, o que multiplica as chances de escrever no lugar errado.
     *
     * @return array<string,list<array<string,mixed>>>
     */
    private function ler(): array
    {
        $fonte = [];

        foreach (array_keys(self::CONTEUDO) as $tabela) {
            $fonte[$tabela] = DB::table($tabela)
                ->orderBy('id')
                ->get()
                ->map(fn (object $linha): array => (array) $linha)
                ->all();
        }

        return $fonte;
    }

    /**
     * @param  array<string,list<array<string,mixed>>>  $fonte
     * @return array<string,int>
     */
    private function escrever(array $fonte): array
    {
        /** @var array<string,array<int,int>> $mapa */
        $mapa = [];
        $copiadas = [];

        foreach (self::CONTEUDO as $tabela => $fks) {
            $colunas = $this->colunasCopiaveis($tabela);

            if (in_array($tabela, self::MAPEADAS, true)) {
                $mapa[$tabela] = [];

                foreach ($fonte[$tabela] as $linha) {
                    $novo = $this->preparar($linha, $colunas, $fks, $mapa);

                    // `insertGetId` para saber o id que a sequência deu. São dezenas de
                    // linhas nestas três tabelas; os 955 desafios vão em lote, abaixo.
                    $mapa[$tabela][(int) $linha['id']] = (int) DB::table($tabela)->insertGetId($novo);
                }
            } else {
                foreach (array_chunk($fonte[$tabela], 500) as $lote) {
                    DB::table($tabela)->insert(
                        array_map(fn (array $l): array => $this->preparar($l, $colunas, $fks, $mapa), $lote)
                    );
                }
            }

            $copiadas[$tabela] = count($fonte[$tabela]);
        }

        // `fases.requisito_fase_id` aponta para `fases`: o alvo pode ainda não existir na
        // primeira passada. Ele é reescrito depois, com o mapa completo — a mesma solução
        // que `ImportarDoLegado::copiarFases()` usa, e pelo mesmo motivo.
        $this->religarRequisitos($fonte['fases'], $mapa['fases']);

        return $copiadas;
    }

    /**
     * @param  array<string,mixed>  $linha
     * @param  list<string>  $colunas
     * @param  array<string,string>  $fks
     * @param  array<string,array<int,int>>  $mapa
     * @return array<string,mixed>
     */
    private function preparar(array $linha, array $colunas, array $fks, array $mapa): array
    {
        $novo = [];

        foreach ($colunas as $coluna) {
            $valor = $linha[$coluna] ?? null;

            if (isset($fks[$coluna]) && $valor !== null) {
                $alvo = $fks[$coluna];
                $valor = $mapa[$alvo][(int) $valor]
                    ?? throw new RuntimeException("A linha aponta para {$alvo} #{$valor}, que não foi copiada.");
            }

            $novo[$coluna] = $valor;
        }

        // `requisito_fase_id` é auto-referência: nasce nulo e é religado no fim.
        if (array_key_exists('requisito_fase_id', $novo)) {
            $novo['requisito_fase_id'] = null;
        }

        return $novo;
    }

    /**
     * @param  list<array<string,mixed>>  $fasesOriginais
     * @param  array<int,int>  $mapa
     */
    private function religarRequisitos(array $fasesOriginais, array $mapa): void
    {
        foreach ($fasesOriginais as $fase) {
            if ($fase['requisito_fase_id'] === null) {
                continue;
            }

            $requisito = (int) $fase['requisito_fase_id'];

            $afetadas = DB::table('fases')
                ->where('id', $mapa[(int) $fase['id']])
                ->update(['requisito_fase_id' => $mapa[$requisito]
                    ?? throw new RuntimeException("A fase #{$fase['id']} exige a fase #{$requisito}, que não foi copiada."),
                ]);

            if ($afetadas !== 1) {
                throw new RuntimeException('O RLS recusou religar o requisito de uma fase copiada.');
            }
        }
    }

    /**
     * Uma cópia que não confere não é uma cópia. Sem isto, um `insert` que o RLS recusasse
     * em silêncio deixaria a escola com metade do conteúdo — e o `tenant:ativar` a aprovaria,
     * porque o smoke só exige que exista **alguma** coisa.
     *
     * @param  array<string,list<array<string,mixed>>>  $fonte
     */
    private function conferir(array $fonte, Tenant $para): void
    {
        foreach (array_keys(self::CONTEUDO) as $tabela) {
            $noDestino = DB::table($tabela)->count();

            if ($noDestino !== count($fonte[$tabela])) {
                throw new RuntimeException(sprintf(
                    'Reconciliação falhou em "%s": a origem tem %d linhas, "%s" ficou com %d.',
                    $tabela, count($fonte[$tabela]), $para->slug, $noDestino,
                ));
            }
        }

        // As secundárias são o que torna `arquivista_do_vazio` alcançável. Elas mudaram de id
        // na cópia — é `tipo` que as identifica agora, e é `tipo` que se confere.
        $secundarias = DB::table('fases')->where('tipo', 'secundaria')->count();
        $naOrigem = count(array_filter($fonte['fases'], fn (array $f): bool => $f['tipo'] === 'secundaria'));

        if ($secundarias !== $naOrigem) {
            throw new RuntimeException('As fases secundárias não sobreviveram à cópia — arquivista_do_vazio ficaria inalcançável.');
        }
    }

    /**
     * Todas as colunas, menos `id` (a sequência dá o novo) e `tenant_id` (o `DEFAULT` lê o
     * contexto). Lida do schema, e não de uma lista escrita à mão: uma coluna nova numa
     * migration futura entra sozinha, em vez de ser esquecida em silêncio na cópia.
     *
     * @return list<string>
     */
    private function colunasCopiaveis(string $tabela): array
    {
        return array_values(array_diff(Schema::getColumnListing($tabela), ['id', 'tenant_id']));
    }
}
