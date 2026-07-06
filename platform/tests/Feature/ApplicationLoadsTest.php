<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ApplicationLoadsTest extends TestCase
{
    public function test_it_loads_the_application_root(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'service' => 'Algorithmia Platform',
            ]);
    }
}
