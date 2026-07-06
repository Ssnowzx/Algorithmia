<?php

declare(strict_types=1);

use App\Domain\Platform\Http\Controllers\HealthzController;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', HealthzController::class);
