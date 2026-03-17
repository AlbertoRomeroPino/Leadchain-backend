<?php

use Illuminate\Support\Facades\Route;

// Redirección inmediata al entrar a la URL principal
Route::get('/', function () {
    return redirect('/api/documentation');
});
