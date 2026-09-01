<?php

use App\Http\Controllers\Core\Api\AuditLogController;
use App\Http\Controllers\Core\Api\MeController;
use App\Http\Controllers\Core\Api\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — Fase 0 (Core System)
|--------------------------------------------------------------------------
| Mengikuti standar di blueprint #api: versioning /api/v1, auth Sanctum,
| response format seragam { success, data, meta } / { success, message, errors }.
| Domain lain (Akademik, Keuangan, dst.) ditambahkan bertahap di file terpisah
| per domain (mis. routes/akademik-api.php) begitu modulnya dibangun.
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/me', MeController::class);

    Route::get('/units', [UnitController::class, 'index'])->middleware('permission:core.unit.view');
    Route::get('/units/{unit}', [UnitController::class, 'show'])->middleware('permission:core.unit.view');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('permission:core.audit_log.view');
});
