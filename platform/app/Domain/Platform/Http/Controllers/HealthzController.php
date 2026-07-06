<?php
declare(strict_types=1);

namespace App\Domain\Platform\Http\Controllers;

use App\Domain\Platform\Health\HealthCheckService;
use Illuminate\Http\JsonResponse;

final class HealthzController
{
    public function __invoke(HealthCheckService $healthCheckService): JsonResponse
    {
        $payload = $healthCheckService->payload();

        return response()->json($payload, $healthCheckService->statusCode($payload));
    }
}
