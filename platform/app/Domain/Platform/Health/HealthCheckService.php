<?php
declare(strict_types=1);

namespace App\Domain\Platform\Health;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

final class HealthCheckService
{
    public function payload(): array
    {
        $databaseOk = $this->databaseOk();
        $redisOk = $this->redisOk();

        return [
            'status' => $databaseOk && $redisOk ? 'ok' : 'degraded',
            'checks' => [
                'app' => 'ok',
                'database' => $databaseOk ? 'ok' : 'down',
                'redis' => $redisOk ? 'ok' : 'down',
            ],
        ];
    }

    public function statusCode(array $payload): int
    {
        return ($payload['status'] ?? 'degraded') === 'ok' ? 200 : 503;
    }

    private function databaseOk(): bool
    {
        try {
            DB::connection()->selectOne('select 1');
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function redisOk(): bool
    {
        try {
            Redis::connection()->ping();
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
