<?php

declare(strict_types=1);

namespace App\Dominio\Operacao;

use App\Models\Operador;
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
 *
 * Há duas origens de autor, e elas não se misturam: o `Usuario` de uma escola e o
 * `Operador` da plataforma. Os ids das duas tabelas colidem — o aluno 3 e o operador 3
 * são pessoas diferentes —, e por isso a linha guarda também o **tipo**.
 */
final class ServicoDeAuditoria
{
    public function __construct(private readonly Request $requisicao) {}

    /**
     * @param  array<string,mixed>  $resumo
     */
    public function registrar(string $acao, string $alvoTipo, ?int $alvoId, array $resumo = []): RegistroDeAuditoria
    {
        $autor = $this->autor();

        return RegistroDeAuditoria::create([
            'autor_id' => $autor?->getAuthIdentifier(),
            'autor_tipo' => $this->tipoDe($autor),
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
     * O jogo autentica no guard padrão; o console, no `operador`. Uma requisição nunca tem
     * os dois — os hosts são diferentes, e o `ResolverTenant` só roda num deles.
     */
    private function autor(): ?Authenticatable
    {
        return $this->requisicao->user() ?? $this->requisicao->user('operador');
    }

    private function tipoDe(?Authenticatable $autor): ?string
    {
        return match (true) {
            $autor instanceof Usuario => 'usuario',
            $autor instanceof Operador => 'operador',
            default => null,
        };
    }

    /**
     * O e-mail é copiado, não referenciado: apagar a conta de quem agiu não pode
     * apagar o registro de que agiu.
     */
    private function emailDe(?Authenticatable $autor): ?string
    {
        return match (true) {
            $autor instanceof Usuario => $autor->email,
            $autor instanceof Operador => $autor->email,
            default => null,
        };
    }
}
