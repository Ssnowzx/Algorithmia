<?php
/**
 * Inventário do herói: equipar, usar e descartar itens.
 */
class InventarioController extends Controller
{
    public function index(): void
    {
        $heroi = Auth::exigirPersonagem();
        $itens = (new Inventario())->doPersonagem((int) $heroi['id']);

        $this->view('inventario/index', [
            'pageTitle' => 'Inventário',
            'heroi'     => $heroi,
            'itens'     => $itens,
        ]);
    }

    /**
     * Equipa um item, desequipando qualquer outro do mesmo tipo.
     */
    public function equipar(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();

        $inv = new Inventario();
        $linha = $inv->pegar((int) $heroi['id'], (int) $itemId);
        $item = (new Item())->findById((int) $itemId);
        if (!$linha || !$item || !in_array($item['tipo'], ['arma', 'escudo', 'acessorio'], true)) {
            $this->flash('erro', 'Este item não pode ser equipado.');
            $this->redirect('inventario');
        }

        // Desequipa os demais itens do mesmo tipo.
        foreach ($inv->doPersonagem((int) $heroi['id']) as $i) {
            if ($i['tipo'] === $item['tipo'] && (int) $i['equipado'] === 1) {
                $inv->update((int) $i['id'], ['equipado' => 0]);
            }
        }
        $inv->update((int) $linha['id'], ['equipado' => 1]);
        $this->flash('sucesso', e($item['nome']) . ' equipado!');
        $this->redirect('inventario');
    }

    public function desequipar(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();
        $inv = new Inventario();
        $linha = $inv->pegar((int) $heroi['id'], (int) $itemId);
        if ($linha) {
            $inv->update((int) $linha['id'], ['equipado' => 0]);
        }
        $this->redirect('inventario');
    }

    /**
     * Usa uma poção fora de batalha (recupera HP/MP imediatamente).
     */
    public function usar(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();

        $inv = new Inventario();
        $item = (new Item())->findById((int) $itemId);
        if (!$item || $item['tipo'] !== 'pocao' || $inv->quantidade((int) $heroi['id'], (int) $itemId) < 1) {
            $this->flash('erro', 'Item indisponível.');
            $this->redirect('inventario');
        }

        $efeito = Item::efeito($item);
        $novoHp = min((int) $heroi['hp_max'], (int) $heroi['hp_atual'] + (int) ($efeito['cura_hp'] ?? 0));
        $novoMp = min((int) $heroi['mp_max'], (int) $heroi['mp_atual'] + (int) ($efeito['cura_mp'] ?? 0));
        (new Personagem())->update((int) $heroi['id'], ['hp_atual' => $novoHp, 'mp_atual' => $novoMp]);
        $inv->remover((int) $heroi['id'], (int) $itemId);

        $this->flash('sucesso', e($item['nome']) . ' usada. Você se sente revigorado!');
        $this->redirect('inventario');
    }

    public function descartar(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();
        (new Inventario())->remover((int) $heroi['id'], (int) $itemId);
        $this->flash('info', 'Item descartado.');
        $this->redirect('inventario');
    }
}
