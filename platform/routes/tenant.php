<?php

declare(strict_types=1);

use App\Domain\Tenancy\Http\Controllers\TenantContextController;
use Illuminate\Support\Facades\Route;

Route::middleware(['resolve.tenant'])->group(static function (): void {
    Route::get('/__tenant/context', TenantContextController::class);
});
