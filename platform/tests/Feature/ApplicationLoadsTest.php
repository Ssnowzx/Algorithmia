<?php
declare(strict_types=1);

it('loads the application root', function (): void {
    $this->getJson('/')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'service' => 'Algorithmia Platform',
        ]);
});
