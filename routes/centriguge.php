<?php

use App\Http\Controllers\CentrifugoProxyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('centrifuge')->group(function () {
        Route::post('connection_token', [CentrifugoProxyController::class, 'genConnectionToken'])->name('centrifuge.genConnectionToken');
    });
});
