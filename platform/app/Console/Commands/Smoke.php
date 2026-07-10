<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Combate\BatalhaEmMemoria;
use App\Dominio\Combate\CorretorDeRespostas;
use App\Dominio\Combate\MotorDeBatalha;
use App\Dominio\Combate\SorteioAntiRepeticao;
use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Dominio\Relatorios\RelatorioDeTurma;
use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\Fase;
use App\Models\Item;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\Turma;
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
 *
 * **Etapa E.3: ele passou a percorrer o caminho do aluno até o relatório.** A batalha
 * sozinha exercitava o motor em memória e não tocava nada do que a instituição escreve:
 * um `tenant_id` sem `DEFAULT`, uma policy de RLS mal escrita em `progresso_fases`, ou uma
 * agregação de relatório quebrada passariam por este comando sem um arranhão — e apareceriam
 * na primeira aula, para o professor.
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

        // Antes de qualquer instituição: o host do `APP_URL` tem de resolver uma delas.
        // É uma checagem da instalação, e não de uma escola, e por isso roda uma vez só.
        $this->falhas = [];
        $this->verificar('O host do APP_URL resolve uma instituição ativa', $this->appUrlResolve(...));

        if ($this->falhas !== []) {
            $this->error('O site responderá 404 com o banco cheio. Ver RUNBOOK §10.1.');

            return self::FAILURE;
        }

        $codigo = self::SUCCESS;

        foreach ($tenants as $tenant) {
            if (count($tenants) > 1) {
                $this->newLine();
                $this->line("<fg=cyan>── instituição: {$tenant->slug}</>");
            }

            $this->falhas = [];

            if ($contexto->usar($tenant->id, fn (): int => $this->verificacoes((int) $tenant->id)) !== self::SUCCESS) {
                $codigo = self::FAILURE;
            }
        }

        return $codigo;
    }

    /** `null` quando a tenancy está desligada: não há instituição a que pertencer. */
    private function verificacoes(?int $tenantId = null): int
    {
        // Estrutural: vale mesmo num banco recém-migrado, sem uma linha de conteúdo.
        $this->verificar('Banco responde', fn (): bool => DB::connection()->select('SELECT 1') !== []);
        $this->verificar('As 14 tabelas existem', $this->tabelasExistem(...));
        $this->verificar('Nenhuma fase com requisito órfão', $this->semRequisitoOrfao(...));
        $this->verificar('Sessão em banco (o gabarito não cabe num cookie)', fn (): bool => config('session.driver') === 'database');
        $this->verificar('APP_DEBUG desligado', fn (): bool => config('app.debug') === false);

        if ($tenantId !== null) {
            $this->verificar('A instituição tem domínio primário', fn (): bool => $this->temDominio($tenantId));
        }

        if (! $this->option('sem-conteudo')) {
            $this->verificar('Conteúdo importado', $this->temConteudo(...));
            $this->verificar('Fases secundárias presentes (arquivista_do_vazio alcançável)', $this->temSecundarias(...));
            $this->verificar('Fragmento da IA no catálogo', fn (): bool => Item::fragmentoDaIa() !== null);
            $this->verificar('Confronto final alcançável', fn (): bool => Fase::confrontoFinal() !== null);

            if (! $this->option('rasa')) {
                $this->verificar('O motor joga uma fase real', $this->motorJoga(...));
                $this->verificar('O progresso do aluno persiste', fn (): bool => $this->progressoPersiste($tenantId));
                $this->verificar('O relatório de turma responde', $this->relatorioResponde(...));
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
            (bool) $this->option('rasa') => 'Smoke raso OK — o conteúdo está lá. Motor, progresso e relatório NÃO foram exercitados.',
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

    /**
     * Sem fases secundárias, a conquista `arquivista_do_vazio` é inalcançável — em silêncio.
     *
     * A verificação era sobre os **ids** 8, 14, 20 e 32, porque a conquista os referenciava.
     * Ela não os referencia mais: são as fases de `tipo = 'secundaria'` da instituição. E
     * tinha de deixar de ser: uma escola semeada por `algorithmia:tenant:semear` recebe ids
     * novos, e reprovaria o smoke tendo exatamente o conteúdo certo.
     *
     * Que os ids do LEGADO cheguem intactos no dia do corte continua sendo verificado — pelo
     * `algorithmia:importar`, que é quem os preserva, e pelo `ImportacaoDoLegadoTest`.
     */
    private function temSecundarias(): bool
    {
        if (Fase::query()->where('tipo', 'secundaria')->doesntExist()) {
            throw new RuntimeException('nenhuma fase secundária — arquivista_do_vazio seria inalcançável');
        }

        return true;
    }

    private function semRequisitoOrfao(): bool
    {
        return Fase::query()
            ->whereNotNull('requisito_fase_id')
            ->whereNotIn('requisito_fase_id', Fase::query()->select('id'))
            ->doesntExist();
    }

    // ----------------------------------------------------------- resolução de tenant

    /**
     * O pior sintoma que esta instalação sabe produzir: **404 com o banco cheio e o
     * healthcheck verde.**
     *
     * A migration cria a instituição padrão com o host tirado do `APP_URL`, e é esse host
     * que o `ResolverTenant` procura em `tenant_dominios` a cada requisição. Um `APP_URL`
     * errado no dia do corte não quebra nada que um health check saiba ver — o `/healthz`
     * fica fora do resolvedor de propósito. Só o jogador percebe. Ver `RUNBOOK §10.1`.
     */
    private function appUrlResolve(): bool
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            throw new RuntimeException('APP_URL não tem host');
        }

        // `tenants` e `tenant_dominios` são catálogo global, sem RLS — esta consulta roda
        // antes de haver contexto, como a do próprio resolvedor.
        $atendido = DB::table('tenant_dominios as d')
            ->join('tenants as t', 't.id', '=', 'd.tenant_id')
            ->whereRaw('lower(d.host) = ?', [mb_strtolower($host)])
            ->where('t.ativo', true)
            ->exists();

        if (! $atendido) {
            throw new RuntimeException(sprintf('nenhuma instituição ativa atende "%s"', $host));
        }

        return true;
    }

    /** Uma instituição sem domínio é inalcançável — e o resolvedor não diz por quê. */
    private function temDominio(int $tenantId): bool
    {
        $tem = DB::table('tenant_dominios')
            ->where('tenant_id', $tenantId)
            ->where('primario', true)
            ->exists();

        if (! $tem) {
            throw new RuntimeException('sem domínio primário: nenhum host chega nesta instituição');
        }

        return true;
    }

    // --------------------------------------------------------- o caminho do aluno

    /**
     * Joga uma fase real, com um herói descartável, dentro de uma transação que é
     * sempre desfeita. Nada sobra no banco.
     */
    private function motorJoga(): bool
    {
        $fase = $this->licaoJogavel();

        DB::beginTransaction();

        try {
            $heroi = $this->heroiDescartavel();

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

    /**
     * A batalha acontece em memória. Isto aqui **escreve**, e é outra prova.
     *
     * `ProgressoFase::registrar` não menciona `tenant_id`: quem o preenche é o `DEFAULT` da
     * coluna, que lê `current_setting('app.tenant_id')`. Se esse `DEFAULT` cair — numa
     * migration futura, num `pg_dump`/restore que o perca — a inserção morre no `NOT NULL`,
     * e um progresso gravado com o tenant errado seria pior ainda. Por isso a linha lida de
     * volta é comparada com a instituição em que estamos.
     */
    private function progressoPersiste(?int $tenantId): bool
    {
        $fase = $this->licaoJogavel();

        DB::beginTransaction();

        try {
            $heroi = $this->heroiDescartavel();

            ProgressoFase::registrar($heroi->id, $fase->id, estrelas: 2, acertos: 3, erros: 1, usouIa: false);

            $linha = DB::table('progresso_fases')
                ->where('personagem_id', $heroi->id)
                ->where('fase_id', $fase->id)
                ->first();

            if ($linha === null) {
                throw new RuntimeException('o progresso não foi gravado');
            }

            if ((int) $linha->estrelas !== 2) {
                throw new RuntimeException('as estrelas não sobreviveram à gravação');
            }

            if ($tenantId !== null && (int) $linha->tenant_id !== $tenantId) {
                throw new RuntimeException('o progresso nasceu fora da instituição que o criou');
            }

            return true;
        } finally {
            DB::rollBack();
        }
    }

    /**
     * O relatório é a tela que o professor abre, e ela agrega três consultas sobre tabelas
     * tenant-scoped. Um `tenant_id` sem `DEFAULT` em `matriculas`, uma policy de RLS mal
     * escrita, ou uma agregação quebrada não aparecem em nenhuma verificação acima — e
     * aparecem na primeira aula.
     *
     * Roda mesmo com a flag `turmas` desligada: a flag esconde uma rota, não um schema.
     */
    private function relatorioResponde(): bool
    {
        $fase = $this->licaoJogavel();

        DB::beginTransaction();

        try {
            $heroi = $this->heroiDescartavel();

            ProgressoFase::registrar($heroi->id, $fase->id, estrelas: 3, acertos: 5, erros: 0, usouIa: false);

            // `codigo` é único por instituição e cabe em 20 caracteres.
            $turma = Turma::create(['nome' => 'Turma do Smoke', 'codigo' => 'smoke-'.substr(uniqid(), -8)]);
            $turma->alunos()->attach($heroi->usuario_id);

            $relatorio = (new RelatorioDeTurma)->gerar($turma);

            if ($relatorio['turma']['alunos'] !== 1) {
                throw new RuntimeException('o relatório perdeu o aluno matriculado');
            }

            if (($relatorio['alunos'][0]['fases_concluidas'] ?? null) !== 1) {
                throw new RuntimeException('o relatório não viu a fase concluída');
            }

            return true;
        } finally {
            DB::rollBack();
        }
    }

    private function licaoJogavel(): Fase
    {
        $fase = Fase::query()
            ->where('tipo', 'licao')
            ->whereHas('desafios')
            ->orderBy('ordem_global')
            ->first();

        if ($fase === null) {
            throw new RuntimeException('nenhuma lição com desafios');
        }

        return $fase;
    }

    /**
     * Vive dentro de uma transação que será desfeita. O HP absurdo é para que a batalha do
     * smoke termine em vitória mesmo contra o chefe mais duro que o conteúdo tiver.
     *
     * Todos os campos explícitos: `create()` não reidrata os defaults do banco, e um
     * `nivel` nulo estoura no construtor de `EstadoDeBatalha`.
     */
    private function heroiDescartavel(): Personagem
    {
        $usuario = Usuario::create([
            'nome' => 'Sonda do Smoke',
            'email' => 'smoke+'.uniqid().'@algorithmia.invalid',
            'senha_hash' => 'nao-usado',
        ]);

        return Personagem::create([
            'usuario_id' => $usuario->id, 'nome' => 'Sonda', 'classe' => 'mago',
            'nivel' => 1, 'xp' => 0,
            'hp_max' => 9999, 'hp_atual' => 9999, 'mp_max' => 99, 'mp_atual' => 99,
            'ouro' => 0, 'reputacao' => 0, 'capitulo' => 0,
        ]);
    }
}
