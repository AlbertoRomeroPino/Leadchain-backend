<?php

namespace tests\Fixture;

class ClienteData
{
    const CLIENTE_POST = [
        "nombre" => "Antonio",
        "apellidos" => "Pérez García",
        "telefono" => "612345678",
        "email" => "antonio@ejemplo.com"
    ];

    const CLIENTE_PUT = [
        "nombre" => "Maria",
        "apellidos" => "Castillo García",
        "telefono" => "612345678",
        "email" => "antonio@ejemplo.com"
    ];

    const CLIENTE_PATCH = [
        "nomrbe" => "Francisco",
        "telefono" => "987887848"
    ];
}
