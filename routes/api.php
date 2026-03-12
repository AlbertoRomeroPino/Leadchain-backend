<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\VisitaController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (públicas)
|--------------------------------------------------------------------------
*/
// localhost:8000/api/auth/login
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
        // http://127.0.0.1:8000/api/auth/logout
        Route::post('logout', [AuthController::class, 'logout']);
        // http://127.0.0.1:8000/api/auth/refresh
        Route::post('refresh', [AuthController::class, 'refresh']);
        // http://127.0.0.1:8000/api/auth/me
        Route::get('me', [AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas compartidas (lectura para admin y comercial)
    |--------------------------------------------------------------------------
    */
    // R clientes
    // http://127.0.0.1:8000/api/clientes
    Route::get('clientes', [ClienteController::class, 'index']);
    // http://127.0.0.1:8000/api/clientes/{id_cliente}
    Route::get('clientes/{cliente}', [ClienteController::class, 'show']);

    // R zonas
    // http://127.0.0.1:8000/api/zonas
    Route::get('zonas', [ZonaController::class, 'index']);
    // http://127.0.0.1:8000/api/zonas/{id_zona}
    Route::get('zonas/{zona}', [ZonaController::class, 'show']);

    // R edificios
    // http://127.0.0.1:8000/api/edificios
    Route::get('edificios', [EdificioController::class, 'index']);
    // http://127.0.0.1:8000/api/edificios/{id_edificio}
    Route::get('edificios/{edificio}', [EdificioController::class, 'show']);

    // R visitas
    // http://127.0.0.1:8000/api/visitas
    Route::get('visitas', [VisitaController::class, 'index']);
    // http://127.0.0.1:8000/api/visitas/{id_visita}
    Route::get('visitas/{visita}', [VisitaController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Rutas para Anunciantes (CRU visitas)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:comercial,admin')->group(function () {
        // http://127.0.0.1:8000/api/visitas
        Route::post('visitas', [VisitaController::class, 'store']);
        // http://127.0.0.1:8000/api/visitas/{id_visita}
        Route::put('visitas/{visita}', [VisitaController::class, 'update']);
        // http://127.0.0.1:8000/api/visitas/{id_visita}
        Route::patch('visitas/{visita}', [VisitaController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas solo para ADMINISTRADORES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // CRUD zonas (excepto index y show que ya están arriba)
        // http://127.0.0.1:8000/api/zonas
        Route::post('zonas', [ZonaController::class, 'store']);
        // http://127.0.0.1:8000/api/zonas/{id_zona}
        Route::put('zonas/{zona}', [ZonaController::class, 'update']);
        // http://127.0.0.1:8000/api/zonas/{id_zona}
        Route::patch('zonas/{zona}', [ZonaController::class, 'update']);
        // http://127.0.0.1:8000/api/zonas/{id_zona}
        Route::delete('zonas/{zona}', [ZonaController::class, 'destroy']);

        // CRUD usuarios (comerciales)
        // http://127.0.0.1:8000/api/users
        Route::apiResource('users', UserController::class);

        // CRUD clientes (excepto index y show)
        // http://127.0.0.1:8000/api/clientes
        Route::post('clientes', [ClienteController::class, 'store']);
        // http://127.0.0.1:8000/api/clientes/{id_cliente}
        Route::put('clientes/{cliente}', [ClienteController::class, 'update']);
        // http://127.0.0.1:8000/api/clientes/{id_cliente}
        Route::patch('clientes/{cliente}', [ClienteController::class, 'update']);
        // http://127.0.0.1:8000/api/clientes/{id_cliente}
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy']);

        // CRUD edificios (excepto index y show)
        // http://127.0.0.1:8000/api/edificios
        Route::post('edificios', [EdificioController::class, 'store']);
        // http://127.0.0.1:8000/api/edificios/{id_edificio}
        Route::put('edificios/{edificio}', [EdificioController::class, 'update']);
        // http://127.0.0.1:8000/api/edificios/{id_edificio}
        Route::patch('edificios/{edificio}', [EdificioController::class, 'update']);
        // http://127.0.0.1:8000/api/edificios/{id_edificio}
        Route::delete('edificios/{edificio}', [EdificioController::class, 'destroy']);

        // Delete visitas (solo admin puede eliminar)
        // http://127.0.0.1:8000/api/visitas/{id_visita}
        Route::delete('visitas/{visita}', [VisitaController::class, 'destroy']);
    });
});
