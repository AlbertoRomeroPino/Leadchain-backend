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

// Rutas de sesión - refresh necesita permitir tokens expirados
Route::prefix('auth')->group(function () {
    Route::post('logout',  [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('allow_expired_token');
    Route::get('me',       [AuthController::class, 'me'])->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {

    // Lectura compartida (admin y comercial)
    Route::get('cliente/detalles/{cliente}', [ClienteController::class, 'detalle'])->whereNumber('cliente');
    Route::apiResource('clientes',  ClienteController::class)->only(['index', 'show'])->whereNumber('cliente');

    Route::apiResource('edificios', EdificioController::class)->only(['index', 'show']);
    Route::get('edificios/{edificio}/detalle', [EdificioController::class, 'detalle'])->whereNumber('edificio');
    Route::get('edificios/{edificio}/panel', [EdificioController::class, 'panel'])->whereNumber('edificio');
    
    Route::apiResource('estados-visita', EstadoVisitaController::class)->only(['index', 'show']);

    

    Route::apiResource('visitas',   VisitaController::class)->only(['index', 'show']);
// Endpoint consolidado para página de Visitas (una sola consulta en lugar de 3)
    Route::get('visitas/pagina/datos-consolidados', [VisitaController::class, 'paraVisitasPage']);


    Route::get('zonas/mapa', [ZonaController::class, 'mapa']);
    Route::apiResource('zonas',     ZonaController::class)->only(['index', 'show']);
    // Endpoint consolidado para página de Zonas (una sola consulta con edificios y clientes)
    Route::get('zonas/pagina/datos', [ZonaController::class, 'pageData']);


    /*
    |--------------------------------------------------------------------------
    | Comercial — gestión de visitas
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:comercial')->group(function () {
        // Endpoint para datos de inicio del comercial
        Route::get('inicio/comercial', [InicioController::class, 'comercial']);
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
        Route::post('edificios/{edificio}/clientes/attach-bulk', [EdificioController::class, 'attachMultipleClientes'])->whereNumber('edificio');
        Route::post('edificios/{edificio}/clientes/{clienteId}', [EdificioController::class, 'attachCliente'])->whereNumber(['edificio', 'clienteId']);
        Route::delete('edificios/{edificio}/clientes/{clienteId}', [EdificioController::class, 'detachCliente'])->whereNumber(['edificio', 'clienteId']);

        Route::apiResource('visitas',   VisitaController::class)->only(['destroy']);
        Route::apiResource('zonas',     ZonaController::class)->only(['store', 'update', 'destroy']);
        
        // Endpoint para obtener comerciales a cargo del admin (DEBE IR ANTES del apiResource de users)
        Route::get('users/comerciales-a-cargo', [UserController::class, 'comercialesAMiCargo']);
        
        Route::apiResource('users',     UserController::class);
        
        // Endpoint para datos de inicio del admin
        Route::get('inicio/admin', [InicioController::class, 'admin']);
    });
});
