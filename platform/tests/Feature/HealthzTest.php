<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Tests\TestCase;

final class HealthzTest extends TestCase
{
    public function test_it_returns_a_healthy_payload_when_database_and_redis_respond(): void
    {
        DB::shouldReceive('connection')->andReturnSelf();
        DB::shouldReceive('selectOne')->once()->andReturn((object) ['1' => 1]);
        Redis::shouldReceive('connection')->andReturnSelf();
        Redis::shouldReceive('ping')->once()->andReturn('PONG');

        $this->getJson('/healthz')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'checks' => [
                    'app' => 'ok',
                    'database' => 'ok',
                    'redis' => 'ok',
                ],
            ]);
    }

    public function test_it_returns_503_when_a_dependency_is_unavailable(): void
    {
        DB::shouldReceive('connection')->andThrow(new RuntimeException('SQLSTATE[HY000] [2002] secret host info'));
        Redis::shouldReceive('connection')->andReturnSelf();
        Redis::shouldReceive('ping')->once()->andReturn('PONG');

        $this->getJson('/healthz')
            ->assertStatus(503)
            ->assertJson([
                'status' => 'degraded',
                'checks' => [
                    'app' => 'ok',
                    'database' => 'down',
                    'redis' => 'ok',
                ],
            ]);
    }

    public function test_it_does_not_leak_sensitive_error_details_in_the_health_response(): void
    {
        DB::shouldReceive('connection')->andThrow(new RuntimeException('SQLSTATE[HY000] [1045] Access denied for user secret_user'));
        Redis::shouldReceive('connection')->andReturnSelf();
        Redis::shouldReceive('ping')->once()->andReturn('PONG');

        $response = $this->getJson('/healthz');

        $response->assertStatus(503);
        $response->assertDontSee('SQLSTATE');
        $response->assertDontSee('Access denied');
        $response->assertDontSee('secret_user');
        $response->assertDontSee('password');
        $response->assertDontSee('DB_HOST');
    }
}
