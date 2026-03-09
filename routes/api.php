<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\EstadoVisitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\VisitaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Route::apiResource('estados-visita', EstadoVisitaController::class);

    Route::get('visitas', [VisitaController::class, 'index']);
    Route::get('visitas/{visita}', [VisitaController::class, 'show']);

    Route::get('clientes', [ClienteController::class, 'index']);
    Route::get('clientes/{cliente}', [ClienteController::class, 'show']);

    //ToDo: Los edificios que se ubican en tu zona
    Route::get('edificios', [EdificioController::class, 'index']);
    Route::get('edificios/{edificio}', [EdificioController::class, 'show']);

    /**
     * Rutas protegidas solo para usuarios con rol 'admin'
     */
    Route::middleware('login.admin')->group(function () {
        Route::apiResource('zonas', ZonaController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('clientes', ClienteController::class);
        Route::apiResource('edificios', EdificioController::class);
        Route::apiResource('visitas', VisitaController::class);
    });

});
