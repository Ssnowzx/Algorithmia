<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeProgressao;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PerfilController extends Controller
{
    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $progresso = ProgressoFase::mapaDoPersonagem($heroi->id);

        return view('perfil.index', [
            'heroi' => $heroi,
            'fasesConcluidas' => count($progresso),
            'estrelas' => array_sum(array_map(fn (ProgressoFase $p): int => $p->estrelas, $progresso)),
            'xpProximoNivel' => ServicoDeProgressao::xpParaNivel($heroi->nivel + 1),
            'porAssunto' => $this->desempenhoPorAssunto($heroi),
        ]);
    }

    /**
     * Acertos e precisão por matéria, derivados de `respostas_log`.
     *
     * `usou_ia` é contado à parte: um acerto comprado com o Fragmento não mede
     * conhecimento, e somá-lo à precisão inflaria a estatística que o jogador usa
     * para decidir o que estudar.
     *
     * @return list<array{assunto:string,rotulo:string,respostas:int,acertos:int,precisao:int,com_ia:int}>
     */
    private function desempenhoPorAssunto(Personagem $heroi): array
    {
        /** @var array<string,string> $assuntos */
        $assuntos = [
            'php' => 'PHP', 'mvc' => 'Arquitetura MVC', 'sql' => 'Banco de Dados / SQL',
            'poo' => 'Orientação a Objetos', 'estruturas' => 'Estruturas de Dados',
            'redes' => 'Redes de Computadores', 'logica' => 'Lógica e Algoritmos',
            'calculo' => 'Cálculo',
        ];

        $linhas = DB::table('respostas_log')
            ->join('desafios', 'desafios.id', '=', 'respostas_log.desafio_id')
            ->where('respostas_log.personagem_id', $heroi->id)
            ->groupBy('desafios.assunto')
            ->select([
                'desafios.assunto',
                DB::raw('COUNT(*) AS respostas'),
                DB::raw('COUNT(*) FILTER (WHERE respostas_log.correta) AS acertos'),
                DB::raw('COUNT(*) FILTER (WHERE respostas_log.usou_ia) AS com_ia'),
            ])
            ->get()
            ->keyBy('assunto');

        $resumo = [];
        foreach ($assuntos as $chave => $rotulo) {
            $linha = $linhas->get($chave);
            $respostas = (int) ($linha->respostas ?? 0);
            $acertos = (int) ($linha->acertos ?? 0);

            $resumo[] = [
                'assunto' => $chave,
                'rotulo' => $rotulo,
                'respostas' => $respostas,
                'acertos' => $acertos,
                'precisao' => $respostas > 0 ? (int) round($acertos / $respostas * 100) : 0,
                'com_ia' => (int) ($linha->com_ia ?? 0),
            ];
        }

        return $resumo;
    }
}
