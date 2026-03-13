<?php

namespace tests\Fixture;

class UserData
{
    const USER_POST = [
    "nombre" => "Alejandro",
    "apellidos" => "García Martínez",
    "email" => "ale.garcia@email.com",
    "password" => "password1234",
    "rol" => "comercial",
    "id_responsable" => 1,
    "id_zona" => 2
    ];

    const USER_PUT = [
    "nombre" => "Alex",
    "apellidos" => "García Martínez",
    "email" => "ale.garcia@email.com",
    "password" => "password1234",
    "rol" => "comercial",
    "id_responsable" => 1,
    "id_zona" => 2

    ];

    const USER_PATCH = [
    "nombre" => "Paco",
    "email" => "alex.garcia@email.com"
    ];
}