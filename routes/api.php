<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\ZonaController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren autenticación JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Sesión
    Route::prefix('auth')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me',       [AuthController::class, 'me']);
    });

    // Lectura compartida (admin y comercial)
    Route::apiResource('clientes',  ClienteController::class)->only(['index', 'show']);
    Route::apiResource('edificios', EdificioController::class)->only(['index', 'show']);
    Route::apiResource('visitas',   VisitaController::class)->only(['index', 'show']);
    Route::apiResource('zonas',     ZonaController::class)->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Comercial — gestión de visitas
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:comercial')->group(function () {
        Route::apiResource('visitas', VisitaController::class)->only(['store', 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Administrador — gestión completa
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('clientes',  ClienteController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('edificios', EdificioController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('visitas',   VisitaController::class)->only(['destroy']);
        Route::apiResource('zonas',     ZonaController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('users',     UserController::class);
    });
});
