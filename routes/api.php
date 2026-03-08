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

// Rutas públicas (sin autenticación)
Route::apiResource('zonas', ZonaController::class);
Route::apiResource('estados-visita', EstadoVisitaController::class);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('edificios', EdificioController::class);
    Route::apiResource('visitas', VisitaController::class);
});
