<?php

declare(strict_types=1);

namespace App\Dominio\Relatorios;

use App\Models\Turma;
use Illuminate\Support\Facades\DB;

/**
 * O que um professor precisa saber sobre a turma dele.
 *
 * Três agregações, e não uma consulta por aluno: numa turma de trinta, o N+1 custa
 * noventa idas ao banco para desenhar uma tabela. As tabelas lidas são tenant-scoped, e
 * portanto o RLS já limita tudo à instituição da requisição — este serviço não precisa
 * (nem deve) repetir esse filtro. O que ele **não** decide é se este professor pode ver
 * esta turma: isso é a `TurmaPolicy`, e é checado antes de chegar aqui.
 *
 * `usou_ia` não é uma métrica de vergonha. O Fragmento da IA é uma escolha de jogo, e a
 * queda de reputação já a pune. Aqui ele serve para o professor ver **onde** a turma
 * pediu ajuda — que é onde o conteúdo está difícil.
 */
final class RelatorioDeTurma
{
    /**
     * @return array{
     *   alunos: list<array<string,mixed>>,
     *   por_assunto: list<array<string,mixed>>,
     *   turma: array{nome:string,codigo:string,alunos:int}
     * }
     */
    public function gerar(Turma $turma): array
    {
        $alunos = $this->alunos($turma);

        /** @var list<int> $personagens */
        $personagens = array_values(array_filter(array_column($alunos, 'personagem_id')));

        $progresso = $this->progressoPorPersonagem($personagens);
        $respostas = $this->respostasPorPersonagem($personagens);

        foreach ($alunos as $i => $aluno) {
            $id = $aluno['personagem_id'];
            $p = $progresso[$id] ?? null;
            $r = $respostas[$id] ?? null;

            $alunos[$i] = [
                ...$aluno,
                'fases_concluidas' => (int) ($p->fases ?? 0),
                'estrelas' => (int) ($p->estrelas ?? 0),
                'tentativas' => (int) ($r->total ?? 0),
                'precisao' => $this->percentual((int) ($r->corretas ?? 0), (int) ($r->total ?? 0)),
                'respostas_com_ia' => (int) ($r->com_ia ?? 0),
            ];
        }

        return [
            'turma' => ['nome' => $turma->nome, 'codigo' => $turma->codigo, 'alunos' => count($alunos)],
            'alunos' => $alunos,
            'por_assunto' => $this->precisaoPorAssunto($personagens),
        ];
    }

    /** @return list<array<string,mixed>> */
    private function alunos(Turma $turma): array
    {
        return DB::table('matriculas as m')
            ->join('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->leftJoin('personagens as p', 'p.usuario_id', '=', 'u.id')
            ->where('m.turma_id', $turma->id)
            ->orderBy('u.nome')
            ->get(['u.id as usuario_id', 'u.nome', 'p.id as personagem_id', 'p.nivel', 'p.reputacao'])
            ->map(fn (object $l): array => [
                'usuario_id' => (int) $l->usuario_id,
                'nome' => (string) $l->nome,
                // Um aluno matriculado que nunca criou herói é um aluno que nunca jogou.
                // Ele aparece no relatório, zerado — sumir dele seria esconder o problema.
                'personagem_id' => $l->personagem_id === null ? null : (int) $l->personagem_id,
                'nivel' => (int) ($l->nivel ?? 0),
                'reputacao' => (int) ($l->reputacao ?? 0),
            ])
            ->all();
    }

    /**
     * @param  list<int>  $personagens
     * @return array<int,object>
     */
    private function progressoPorPersonagem(array $personagens): array
    {
        if ($personagens === []) {
            return [];
        }

        return DB::table('progresso_fases')
            ->whereIn('personagem_id', $personagens)
            ->groupBy('personagem_id')
            ->get([
                'personagem_id',
                DB::raw('count(*) as fases'),
                DB::raw('coalesce(sum(estrelas), 0) as estrelas'),
            ])
            ->keyBy('personagem_id')
            ->all();
    }

    /**
     * @param  list<int>  $personagens
     * @return array<int,object>
     */
    private function respostasPorPersonagem(array $personagens): array
    {
        if ($personagens === []) {
            return [];
        }

        return DB::table('respostas_log')
            ->whereIn('personagem_id', $personagens)
            ->groupBy('personagem_id')
            ->get([
                'personagem_id',
                DB::raw('count(*) as total'),
                DB::raw('count(*) filter (where correta) as corretas'),
                DB::raw('count(*) filter (where usou_ia) as com_ia'),
            ])
            ->keyBy('personagem_id')
            ->all();
    }

    /**
     * Onde a turma acerta e onde pede ajuda. É a leitura pedagógica do relatório.
     *
     * @param  list<int>  $personagens
     * @return list<array<string,mixed>>
     */
    private function precisaoPorAssunto(array $personagens): array
    {
        if ($personagens === []) {
            return [];
        }

        return DB::table('respostas_log as r')
            ->join('desafios as d', 'd.id', '=', 'r.desafio_id')
            ->whereIn('r.personagem_id', $personagens)
            ->groupBy('d.assunto')
            ->orderBy('d.assunto')
            ->get([
                'd.assunto',
                DB::raw('count(*) as total'),
                DB::raw('count(*) filter (where r.correta) as corretas'),
                DB::raw('count(*) filter (where r.usou_ia) as com_ia'),
            ])
            ->map(fn (object $l): array => [
                'assunto' => (string) $l->assunto,
                'total' => (int) $l->total,
                'precisao' => $this->percentual((int) $l->corretas, (int) $l->total),
                'com_ia' => (int) $l->com_ia,
            ])
            ->all();
    }

    /** Sem tentativas não há precisão — e 0% mentiria sobre um aluno que nunca jogou. */
    private function percentual(int $parte, int $total): ?int
    {
        return $total === 0 ? null : (int) round($parte / $total * 100);
    }
}
