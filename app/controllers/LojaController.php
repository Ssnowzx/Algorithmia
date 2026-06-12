<?php
/**
 * Loja: compra e venda de itens com ouro.
 */
class LojaController extends Controller
{
    /** Itens são revendidos por metade do preço. */
    private const FATOR_VENDA = 0.5;

    public function index(): void
    {
        $heroi = Auth::exigirPersonagem();
        $itens = (new Item())->compraveis();
        $inventario = (new Inventario())->doPersonagem((int) $heroi['id']);

        $this->view('loja/index', [
            'pageTitle'   => 'Loja do Reino',
            'heroi'       => $heroi,
            'itens'       => $itens,
            'inventario'  => $inventario,
        ]);
    }

    public function comprar(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();

        $item = (new Item())->findById((int) $itemId);
        if (!$item || !$item['compravel']) {
            $this->flash('erro', 'Item indisponível para compra.');
            $this->redirect('loja');
        }
        if ((int) $heroi['ouro'] < (int) $item['preco']) {
            $this->flash('erro', 'Ouro insuficiente para comprar ' . e($item['nome']) . '.');
            $this->redirect('loja');
        }

        (new Personagem())->update((int) $heroi['id'], ['ouro' => (int) $heroi['ouro'] - (int) $item['preco']]);
        (new Inventario())->adicionar((int) $heroi['id'], (int) $itemId);
        $this->flash('sucesso', e($item['nome']) . ' comprado!');
        $this->redirect('loja');
    }

    public function vender(string $itemId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();

        $inv = new Inventario();
        $item = (new Item())->findById((int) $itemId);
        if (!$item || $inv->quantidade((int) $heroi['id'], (int) $itemId) < 1) {
            $this->flash('erro', 'Você não possui esse item.');
            $this->redirect('loja');
        }
        if ($item['svg_slug'] === 'item-fragmento-ia') {
            $this->flash('erro', 'O Fragmento da IA Ancestral não pode ser vendido.');
            $this->redirect('loja');
        }

        $valor = (int) round((int) $item['preco'] * self::FATOR_VENDA);
        (new Personagem())->update((int) $heroi['id'], ['ouro' => (int) $heroi['ouro'] + $valor]);
        $inv->remover((int) $heroi['id'], (int) $itemId);
        $this->flash('sucesso', e($item['nome']) . ' vendido por ' . $valor . ' de ouro.');
        $this->redirect('loja');
    }
}
