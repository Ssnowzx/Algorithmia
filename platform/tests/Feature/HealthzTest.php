<?php
declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

it('returns a healthy payload when database and redis respond', function (): void {
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
});

it('returns 503 when a dependency is unavailable', function (): void {
    DB::shouldReceive('connection')->andThrow(new \RuntimeException('SQLSTATE[HY000] [2002] secret host info'));
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
});

it('does not leak sensitive error details in the health response', function (): void {
    DB::shouldReceive('connection')->andThrow(new \RuntimeException('SQLSTATE[HY000] [1045] Access denied for user secret_user'));
    Redis::shouldReceive('connection')->andReturnSelf();
    Redis::shouldReceive('ping')->once()->andReturn('PONG');

    $response = $this->getJson('/healthz');

    $response->assertStatus(503);
    $response->assertDontSee('SQLSTATE');
    $response->assertDontSee('Access denied');
    $response->assertDontSee('secret_user');
    $response->assertDontSee('password');
    $response->assertDontSee('DB_HOST');
});
