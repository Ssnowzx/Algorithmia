<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Conquista;
use App\Models\Fase;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\ProgressoFase;

/**
 * Avalia e concede conquistas. Porte de `app/services/ConquistaService.php`.
 *
 * `conceder()` é idempotente por chave primária composta, então cada regra pode
 * ser reavaliada à vontade: só a primeira concessão devolve a conquista.
 */
final class ServicoDeConquistas
{
    /** Devolve a conquista se foi inédita; null se já a tinha (ou se o código não existe). */
    public function conceder(Personagem $personagem, string $codigo): ?Conquista
    {
        $conquista = Conquista::porCodigo($codigo);
        if ($conquista === null) {
            return null;
        }

        return $conquista->concederA($personagem->id) ? $conquista : null;
    }

    /**
     * Concede um objetivo da loja e credita o ouro — só na primeira vez, pois
     * `conceder()` é idempotente.
     *
     * @return array{conquista:Conquista,ouro:int}|null
     */
    public function concederObjetivo(Personagem $personagem, string $codigo): ?array
    {
        $conquista = $this->conceder($personagem, $codigo);
        if ($conquista === null) {
            return null;
        }

        $ouro = (int) (config("jogo.objetivos_ouro.{$codigo}") ?? 0);
        if ($ouro > 0) {
            $personagem->refresh();
            $personagem->update(['ouro' => $personagem->ouro + $ouro]);
        }

        return ['conquista' => $conquista, 'ouro' => $ouro];
    }

    /**
     * Conquistas dependentes do resultado de uma fase recém-concluída.
     *
     * @return list<Conquista> as recém-obtidas, para exibir ao jogador
     */
    public function avaliarAposFase(Personagem $personagem, Fase $fase, int $erros, bool $usouIa): array
    {
        $novas = [];

        $this->coletar($novas, $this->conceder($personagem, 'primeiro_passo'));

        if ($erros === 0 && ! $usouIa) {
            $this->coletar($novas, $this->conceder($personagem, 'sem_falhas'));
        }

        if ($fase->ehChefe()) {
            $this->coletar($novas, $this->conceder($personagem, 'cacador_de_chefes'));
        }

        if ($usouIa) {
            $this->coletar($novas, $this->conceder($personagem, 'tentacao'));
        }

        $this->coletar($novas, $this->avaliarArquivistaDoVazio($personagem, $fase));

        if ($personagem->nivel >= 5) {
            $this->coletar($novas, $this->conceder($personagem, 'aprendiz_veterano'));
        }
        if ($personagem->nivel >= 10) {
            $this->coletar($novas, $this->conceder($personagem, 'lenda_viva'));
        }

        $this->coletar($novas, $this->avaliarColecao($personagem));

        return $novas;
    }

    /** "Colecionador": 8 ou mais itens distintos. Vale para drop de fase e compra na loja. */
    public function avaliarColecao(Personagem $personagem): ?Conquista
    {
        return ItemDoInventario::itensDistintos($personagem->id) >= 8
            ? $this->conceder($personagem, 'colecionador')
            : null;
    }

    /** Conquista de "discípulo", ao derrotar o chefe de uma região. */
    public function concederConquistaDaRegiao(Personagem $personagem, Fase $fase): ?Conquista
    {
        if ($fase->tipo !== 'chefe' || $fase->mestre === null) {
            return null;
        }

        $codigo = $fase->mestre->conquistaDaRegiao();

        return $codigo === null ? null : $this->conceder($personagem, $codigo);
    }

    /**
     * "Puro de Coração": concluir uma região inteira sem nunca recorrer ao
     * Fragmento da IA. As fases secundárias, sendo opcionais, não contam.
     */
    public function concederPuroDeCoracao(Personagem $personagem, Fase $fase): ?Conquista
    {
        if ($fase->tipo !== 'chefe' || $fase->mestre_id === null) {
            return null;
        }

        $mapa = ProgressoFase::mapaDoPersonagem($personagem->id);

        foreach (Fase::doMestre($fase->mestre_id) as $daRegiao) {
            if (! in_array($daRegiao->tipo, ['licao', 'chefe'], true)) {
                continue;
            }
            $progresso = $mapa[$daRegiao->id] ?? null;
            if ($progresso === null || $progresso->usou_ia) {
                return null; // região incompleta, ou houve cola em alguma fase
            }
        }

        return $this->conceder($personagem, 'puro_de_coracao');
    }

    /** Segredo: recuperar todos os Logs do Zero, concluindo as 4 fases secundárias. */
    private function avaliarArquivistaDoVazio(Personagem $personagem, Fase $fase): ?Conquista
    {
        /** @var list<int> $secundarias */
        $secundarias = config('jogo.fases_secundarias');

        if (! in_array($fase->id, $secundarias, true)) {
            return null;
        }

        foreach ($secundarias as $faseId) {
            if (! ProgressoFase::concluiu($personagem->id, $faseId)) {
                return null;
            }
        }

        return $this->conceder($personagem, 'arquivista_do_vazio');
    }

    /** @param  list<Conquista>  $lista */
    private function coletar(array &$lista, ?Conquista $conquista): void
    {
        if ($conquista !== null) {
            $lista[] = $conquista;
        }
    }
}
