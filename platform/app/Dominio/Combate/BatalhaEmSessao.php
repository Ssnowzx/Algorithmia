<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

use Illuminate\Contracts\Session\Session;

/**
 * Implementação de produção: a batalha vive na sessão do jogador.
 *
 * O driver de sessão é `database` — o estado carrega os desafios completos, com
 * gabarito, e não caberia num cookie (nem poderia, pois entregaria as respostas).
 */
final readonly class BatalhaEmSessao implements RepositorioDeBatalha
{
    private const CHAVE = 'batalha';

    public function __construct(private Session $sessao) {}

    public function carregar(): ?EstadoDeBatalha
    {
        $dados = $this->sessao->get(self::CHAVE);

        return is_array($dados) ? EstadoDeBatalha::deArray($dados) : null;
    }

    public function salvar(EstadoDeBatalha $estado): void
    {
        $this->sessao->put(self::CHAVE, $estado->paraArray());
    }

    public function limpar(): void
    {
        $this->sessao->forget(self::CHAVE);
    }
}
