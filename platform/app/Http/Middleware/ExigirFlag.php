<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Dominio\Tenancy\Flags;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uma funcionalidade que a instituição não habilitou não existe para ela.
 *
 * **404, e não 403.** Um 403 diria "isto existe aqui, e você não pode" — e não é verdade:
 * a funcionalidade não foi liberada para esta escola, então a página não existe. É a mesma
 * escolha da `TurmaPolicy` para recursos de outra instituição, e pela mesma razão: um
 * código de status também é informação.
 *
 * **Este middleware é obrigatório.** Esconder o link no menu com `@flag` e deixar a rota
 * aberta é a versão de apresentação do erro que a Etapa A encontrou no banco: uma barreira
 * que dá a sensação de existir. O `FlagsTest` bate na URL sem passar pelo menu.
 */
final class ExigirFlag
{
    public function __construct(private readonly Flags $flags) {}

    public function handle(Request $requisicao, Closure $proximo, string $chave): Response
    {
        // Uma chave desconhecida levanta `InvalidArgumentException` dentro de `ativa()` —
        // 500, e não uma rota calada. Um `flag:turmsa` numa rota é um bug do programador,
        // e ele não pode se manifestar como "a página sumiu".
        abort_unless($this->flags->ativa($chave), 404);

        return $proximo($requisicao);
    }
}
