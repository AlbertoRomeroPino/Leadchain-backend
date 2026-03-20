<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;

// Redirección inmediata al entrar a la URL principal
Route::get('/', function () {
    return redirect('/api/documentation');
})->withoutMiddleware([StartSession::class]);
