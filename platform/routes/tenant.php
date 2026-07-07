<?php

declare(strict_types=1);

use App\Domain\Identity\Http\Controllers\TenantAuthContextController;
use App\Domain\Identity\Http\Controllers\TenantAuthenticationController;
use App\Domain\Identity\Http\Middleware\EnsureActiveTenantMembership;
use App\Domain\Tenancy\Http\Controllers\TenantContextController;
use Illuminate\Support\Facades\Route;

Route::middleware(['resolve.tenant'])->group(static function (): void {
    Route::get('/login', [TenantAuthenticationController::class, 'create'])->name('tenant.login');
    Route::post('/login', [TenantAuthenticationController::class, 'store'])
        ->middleware('throttle:tenant-login')
        ->name('tenant.login.store');
    Route::post('/logout', [TenantAuthenticationController::class, 'destroy'])
        ->middleware(['auth', EnsureActiveTenantMembership::class])
        ->name('tenant.logout');

    Route::get('/__tenant/context', TenantContextController::class);
    Route::get('/__tenant/auth-context', TenantAuthContextController::class)
        ->middleware(['auth', EnsureActiveTenantMembership::class])
        ->name('tenant.auth-context');
});
