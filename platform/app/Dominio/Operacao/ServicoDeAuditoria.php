<?php

declare(strict_types=1);

namespace App\Dominio\Operacao;

use App\Models\RegistroDeAuditoria;
use App\Models\Usuario;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * Grava quem fez o quê, quando, e o que havia ali antes.
 *
 * O `resumo` é a razão de existir do serviço. Numa exclusão, ele é a única cópia do
 * que sumiu — nome da fase, quantos desafios foram junto na cascata. Guardar só o id
 * de uma linha apagada não responde a nenhuma pergunta útil às 3 da manhã.
 */
final class ServicoDeAuditoria
{
    public function __construct(private readonly Request $requisicao) {}

    /**
     * @param  array<string,mixed>  $resumo
     */
    public function registrar(string $acao, string $alvoTipo, ?int $alvoId, array $resumo = []): RegistroDeAuditoria
    {
        $autor = $this->requisicao->user();

        return RegistroDeAuditoria::create([
            'autor_id' => $autor?->getAuthIdentifier(),
            'autor_email' => $this->emailDe($autor),
            'acao' => $acao,
            'alvo_tipo' => $alvoTipo,
            'alvo_id' => $alvoId,
            'resumo' => $resumo === [] ? null : $resumo,
            'ip' => $this->requisicao->ip(),
            'request_id' => app()->bound('pedido.id') ? app('pedido.id') : null,
        ]);
    }

    /**
     * O e-mail é copiado, não referenciado: apagar a conta de quem agiu não pode
     * apagar o registro de que agiu.
     */
    private function emailDe(?Authenticatable $autor): ?string
    {
        return $autor instanceof Usuario ? $autor->email : null;
    }
}
