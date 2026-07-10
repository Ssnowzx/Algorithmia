<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'personagem' => App\Http\Middleware\ExigirPersonagem::class,
            'mestre' => App\Http\Middleware\ExigirMestre::class,
        ]);

        // No grupo `web`, e não global: o `/healthz` não devolve HTML e não precisa
        // gastar 16 bytes de entropia por checagem, a cada dez segundos.
        //
        // `ContextoDoPedido` vem antes: a auditoria e o log precisam do `request_id`,
        // e ele tem de existir mesmo que o CSP falhe.
        // A ordem aqui é sugestão, não decreto: o Laravel **reordena** o pipeline pela
        // lista de prioridade, e `Authenticate` tem lugar fixo nela. Um `ResolverTenant`
        // apenas apendado ao grupo `web` acabava DEPOIS do `Authenticate` — que consulta
        // `usuarios`, tenant-scoped, sem contexto. O RLS devolvia vazio, e o jogador
        // logava para cair na tela de login de novo. Em produção, e em silêncio.
        //
        // Por isso ele entra na lista de prioridade, antes do `Authenticate`. Saber de
        // quem são os dados é a preocupação mais externa de todas: nada pode tocar o banco
        // antes de o contexto do tenant existir.
        $middleware->web(append: [
            App\Http\Middleware\ResolverTenant::class,
            App\Http\Middleware\ContextoDoPedido::class,
            App\Http\Middleware\AplicarPoliticaDeConteudo::class,
        ]);

        // O `before` é a INTERFACE, e não a classe: a lista de prioridade do Laravel
        // registra `AuthenticatesRequests`, não `Authenticate`. Passando a classe, o
        // `in_array` da framework não a encontra, e o middleware vai calado para o fim da
        // lista — que é exatamente onde ele não pode estar.
        $middleware->prependToPriorityList(
            before: Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            prepend: App\Http\Middleware\ResolverTenant::class,
        );

        // Durante a coexistência, o `httpd` do jogo antigo termina o TLS e repassa a
        // requisição ao nginx do port. Sem confiar nesse proxy, o Laravel acha que a
        // conexão é `http`: gera URLs `http://` e nunca envia o cookie `secure` —
        // login em laço infinito.
        //
        // A lista vem do ambiente e é VAZIA por padrão. Confiar em `*` sem um proxy
        // na frente deixaria qualquer cliente forjar `X-Forwarded-Proto`.
        $proxies = env('TRUSTED_PROXIES');
        if (is_string($proxies) && $proxies !== '') {
            $middleware->trustProxies(
                at: $proxies === '*' ? '*' : array_map(trim(...), explode(',', $proxies)),
                headers: Request::HEADER_X_FORWARDED_FOR
                    | Request::HEADER_X_FORWARDED_HOST
                    | Request::HEADER_X_FORWARDED_PORT
                    | Request::HEADER_X_FORWARDED_PROTO,
            );
        }

        $middleware->redirectGuestsTo(fn (): string => route('login'));
        $middleware->redirectUsersTo(fn (): string => route('mapa'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // O padrão do skeleton é `$request->is('api/*')`, e os endpoints de turno
        // vivem sob /batalha. Com aquela regra, uma sessão expirada devolveria uma
        // página HTML de erro no meio de um fetch(), e o batalha.js quebraria ao
        // tentar interpretá-la como JSON. Quem pede JSON recebe JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
