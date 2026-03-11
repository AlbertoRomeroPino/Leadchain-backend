<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\EstadoVisitaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\VisitaController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (públicas)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas con JWT
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Rutas de autenticación (requieren token)
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas compartidas (lectura para admin y comercial)
    |--------------------------------------------------------------------------
    */
    // R clientes
    Route::get('clientes', [ClienteController::class, 'index']);
    Route::get('clientes/{cliente}', [ClienteController::class, 'show']);

    // R zonas
    Route::get('zonas', [ZonaController::class, 'index']);
    Route::get('zonas/{zona}', [ZonaController::class, 'show']);

    // R edificios
    Route::get('edificios', [EdificioController::class, 'index']);
    Route::get('edificios/{edificio}', [EdificioController::class, 'show']);

    // R visitas (ambos roles pueden ver)
    Route::get('visitas', [VisitaController::class, 'index']);
    Route::get('visitas/{visita}', [VisitaController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Rutas para Anunciantes (CRU visitas)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:comercial,admin')->group(function () {
        Route::post('visitas', [VisitaController::class, 'store']);
        Route::put('visitas/{visita}', [VisitaController::class, 'update']);
        Route::patch('visitas/{visita}', [VisitaController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas solo para ADMINISTRADORES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // CRUD zonas (excepto index y show que ya están arriba)
        Route::post('zonas', [ZonaController::class, 'store']);
        Route::put('zonas/{zona}', [ZonaController::class, 'update']);
        Route::patch('zonas/{zona}', [ZonaController::class, 'update']);
        Route::delete('zonas/{zona}', [ZonaController::class, 'destroy']);

        // CRUD usuarios (comerciales)
        Route::apiResource('users', UserController::class);

        // CRUD clientes (excepto index y show)
        Route::post('clientes', [ClienteController::class, 'store']);
        Route::put('clientes/{cliente}', [ClienteController::class, 'update']);
        Route::patch('clientes/{cliente}', [ClienteController::class, 'update']);
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy']);

        // CRUD edificios (excepto index y show)
        Route::post('edificios', [EdificioController::class, 'store']);
        Route::put('edificios/{edificio}', [EdificioController::class, 'update']);
        Route::patch('edificios/{edificio}', [EdificioController::class, 'update']);
        Route::delete('edificios/{edificio}', [EdificioController::class, 'destroy']);

        // Delete visitas (solo admin puede eliminar)
        Route::delete('visitas/{visita}', [VisitaController::class, 'destroy']);
    });

});
