<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\EdificioController;
use App\Http\Controllers\EstadoVisitaController;
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

    // Endpoints consolidados de Inicio (traen múltiples recursos en una sola petición)
    Route::get('inicio/comercial', [InicioController::class, 'comercial'])->middleware('role:comercial');
    Route::get('inicio/admin', [InicioController::class, 'admin'])->middleware('role:admin');
    Route::get('users/comerciales-a-cargo', [UserController::class, 'comercialesAMiCargo'])->middleware('role:admin');

    // Lectura compartida (admin y comercial)
    Route::get('cliente/detalles/{cliente}', [ClienteController::class, 'detalle'])->whereNumber('cliente');
    Route::apiResource('clientes',  ClienteController::class)->only(['index', 'show'])->whereNumber('cliente');
    Route::apiResource('edificios', EdificioController::class)->only(['index', 'show']);
    Route::get('edificios/{edificio}/detalle', [EdificioController::class, 'detalle'])->whereNumber('edificio');
    Route::apiResource('estados-visita', EstadoVisitaController::class)->only(['index', 'show']);
    
    // Endpoint consolidado para página de Visitas (una sola consulta en lugar de 3)
    Route::get('visitas/pagina/datos-consolidados', [VisitaController::class, 'paraVisitasPage']);
    
    Route::apiResource('visitas',   VisitaController::class)->only(['index', 'show']);
    
    // Endpoint consolidado para página de Zonas (una sola consulta con edificios y clientes)
    Route::get('zonas/pagina/datos', [ZonaController::class, 'pageData']);
    
    Route::get('zonas/mapa', [ZonaController::class, 'mapa']);
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
        Route::get('clientes/sin-edificio', [ClienteController::class, 'sinEdificio']);
        Route::apiResource('clientes',  ClienteController::class)->only(['store', 'update', 'destroy'])->whereNumber('cliente');
        Route::apiResource('edificios', EdificioController::class)->only(['store', 'update', 'destroy']);
        
        // Gestión de clientes en edificios
        Route::post('edificios/{edificio}/clientes/{clienteId}', [EdificioController::class, 'attachCliente'])->whereNumber(['edificio', 'clienteId']);
        Route::delete('edificios/{edificio}/clientes/{clienteId}', [EdificioController::class, 'detachCliente'])->whereNumber(['edificio', 'clienteId']);
        
        Route::apiResource('visitas',   VisitaController::class)->only(['destroy']);
        Route::apiResource('zonas',     ZonaController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('users',     UserController::class);
    });
});
