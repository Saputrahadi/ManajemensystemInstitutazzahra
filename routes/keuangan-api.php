<?php

use App\Http\Controllers\Keuangan\Api\BsiCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Keuangan — API routes
|--------------------------------------------------------------------------
| Webhook BSI di luar prefix /v1 (bukan bagian API internal ber-Sanctum di
| blueprint #api) — otentikasi lewat token statis, lihat
| App\Http\Middleware\VerifyBsiCallbackToken.
*/

Route::post('/bsi/callback', BsiCallbackController::class)
    ->middleware('bsi.callback')
    ->name('keuangan.bsi.callback');
