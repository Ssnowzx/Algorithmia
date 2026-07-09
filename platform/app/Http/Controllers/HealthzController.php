<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Health check de verdade: além de responder, confirma que o banco atende.
 *
 * O `/up` que o Laravel monta sozinho prova apenas que o PHP está de pé. Um
 * balanceador que só olhe para ele mandaria tráfego a uma instância que perdeu
 * o PostgreSQL. Responde 503 quando alguma dependência está fora, para que o
 * deploy e o monitoramento possam confiar no código de status.
 */
final class HealthzController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $bancoOk = $this->bancoResponde();

        return response()->json([
            'status' => $bancoOk ? 'ok' : 'degradado',
            'servicos' => [
                'app' => 'ok',
                'banco' => $bancoOk ? 'ok' : 'fora',
            ],
        ], $bancoOk ? 200 : 503);
    }

    private function bancoResponde(): bool
    {
        try {
            DB::connection()->select('SELECT 1');

            return true;
        } catch (Throwable) {
            // O detalhe vai para o log; a resposta não revela host nem credencial.
            return false;
        }
    }
}
