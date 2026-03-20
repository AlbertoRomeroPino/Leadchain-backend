<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Redirección inmediata al entrar a la URL principal
Route::get('/', function () {
    return redirect('/api/documentation');
})->withoutMiddleware([VerifyCsrfToken::class, StartSession::class, ShareErrorsFromSession::class]);
