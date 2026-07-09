<?php

declare(strict_types=1);

use App\Http\Controllers\HealthzController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Liveness + readiness. Ver HealthzController: 503 quando o banco não responde.
Route::get('/healthz', HealthzController::class)->name('healthz');
