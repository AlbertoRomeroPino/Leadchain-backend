<?php

namespace tests\Fixture;

class InvalidData
{
    /**
     * CLIENTES - Datos inválidos
     */
    
    // Nombre vacío
    const CLIENTE_NOMBRE_EMPTY = [
        "nombre" => "",
        "apellidos" => "Pérez García",
        "telefono" => "612345678",
        "email" => "test@example.com"
    ];

    // Nombre excede maxLength (50)
    const CLIENTE_NOMBRE_TOO_LONG = [
        "nombre" => "Juan Carlos Alberto Fernández López García Martínez Ruiz",
        "apellidos" => "Pérez García",
        "telefono" => "612345678",
        "email" => "test@example.com"
    ];

    // Apellidos excede maxLength (100)
    const CLIENTE_APELLIDOS_TOO_LONG = [
        "nombre" => "Juan",
        "apellidos" => "Pérez García López Martínez Ruiz Díaz Hernández Fernández Castro Navarro Sánchez Villanueva Carrillo González Pérez Martínez Rodríguez Fernández",
        "telefono" => "612345678",
        "email" => "test@example.com"
    ];

    // Email inválido
    const CLIENTE_EMAIL_INVALID = [
        "nombre" => "Juan",
        "apellidos" => "Pérez García",
        "telefono" => "612345678",
        "email" => "not-an-email"
    ];

    // Email excede maxLength (100)
    const CLIENTE_EMAIL_TOO_LONG = [
        "nombre" => "Juan",
        "apellidos" => "Pérez García",
        "telefono" => "612345678",
        "email" => "verylongemailaddressthatshouldneverbevalid@verylongdomainnamethatshouldneverexistanywhere.verylongextension"
    ];

    // Teléfono excede maxLength (15)
    const CLIENTE_TELEFONO_TOO_LONG = [
        "nombre" => "Juan",
        "apellidos" => "Pérez García",
        "telefono" => "612345678901234567890",
        "email" => "test@example.com"
    ];

    /**
     * EDIFICIOS - Datos inválidos
     */

    // Dirección vacía
    const EDIFICIO_DIRECCION_EMPTY = [
        "direccion_completa" => "",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial"
    ];

    // Dirección excede maxLength (40)
    const EDIFICIO_DIRECCION_TOO_LONG = [
        "direccion_completa" => "Calle Principal del Barrio Antiguo Número 123 Piso 4 Puerta B",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial"
    ];

    // Tipo excede maxLength (25)
    const EDIFICIO_TIPO_TOO_LONG = [
        "direccion_completa" => "Calle Mayor 123",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial Administrativo Comercial Mixto"
    ];

    // Ubicación con latitud fuera de rango (-90 a 90)
    const EDIFICIO_LAT_OUT_OF_RANGE = [
        "direccion_completa" => "Calle Mayor 123",
        "ubicacion" => ["lat" => 95.0, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial"
    ];

    // Ubicación con longitud fuera de rango (-180 a 180)
    const EDIFICIO_LNG_OUT_OF_RANGE = [
        "direccion_completa" => "Calle Mayor 123",
        "ubicacion" => ["lat" => 40.4168, "lng" => -190.0],
        "id_zona" => 1,
        "tipo" => "Residencial"
    ];

    // Ubicación con estructura incorrecta
    const EDIFICIO_UBICACION_INVALID = [
        "direccion_completa" => "Calle Mayor 123",
        "ubicacion" => ["latitude" => 40.4168, "longitude" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial"
    ];

    // Zona inexistente
    const EDIFICIO_ZONA_NONEXISTENT = [
        "direccion_completa" => "Calle Mayor 123",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 9999,
        "tipo" => "Residencial"
    ];

    /**
     * USUARIOS - Datos inválidos
     */

    // Nombre vacío
    const USER_NOMBRE_EMPTY = [
        "nombre" => "",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Nombre excede maxLength (50)
    const USER_NOMBRE_TOO_LONG = [
        "nombre" => "Juan Carlos Alberto Fernández López García Martínez Ruiz",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Apellidos excede maxLength (100)
    const USER_APELLIDOS_TOO_LONG = [
        "nombre" => "Juan",
        "apellidos" => "García López Martínez Ruiz Díaz Hernández Fernández Castro Navarro Sánchez Villanueva Carrillo González Pérez Martínez Rodríguez Fernández Guerrero",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Email vacío
    const USER_EMAIL_EMPTY = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Email inválido
    const USER_EMAIL_INVALID = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "not-an-email",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Email duplicado
    const USER_EMAIL_DUPLICATE = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "root@leadchain.com", // Ya existe en seeders
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Password vacío
    const USER_PASSWORD_EMPTY = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Rol inválido
    const USER_ROL_INVALID = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "superadmin", // No existe
        "id_responsable" => 1,
        "id_zona" => 1
    ];

    // Responsable inexistente
    const USER_RESPONSABLE_NONEXISTENT = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 9999,
        "id_zona" => 1
    ];

    // Zona inexistente
    const USER_ZONA_NONEXISTENT = [
        "nombre" => "Juan",
        "apellidos" => "García Martínez",
        "email" => "user@example.com",
        "password" => "password1234",
        "rol" => "comercial",
        "id_responsable" => 1,
        "id_zona" => 9999
    ];

    /**
     * VISITAS - Datos inválidos
     */

    // Fecha vacía
    const VISITA_FECHA_EMPTY = [
        "id_usuario" => 1,
        "id_cliente" => 1,
        "fecha_hora" => "",
        "id_estado" => 1,
        "observaciones" => "Test"
    ];

    // Estado inexistente
    const VISITA_ESTADO_NONEXISTENT = [
        "id_usuario" => 1,
        "id_cliente" => 1,
        "fecha_hora" => "2026-04-27 10:00:00",
        "id_estado" => 9999,
        "observaciones" => "Test"
    ];

    // Usuario inexistente
    const VISITA_USUARIO_NONEXISTENT = [
        "id_usuario" => 9999,
        "id_cliente" => 1,
        "fecha_hora" => "2026-04-27 10:00:00",
        "id_estado" => 1,
        "observaciones" => "Test"
    ];

    // Cliente inexistente
    const VISITA_CLIENTE_NONEXISTENT = [
        "id_usuario" => 1,
        "id_cliente" => 9999,
        "fecha_hora" => "2026-04-27 10:00:00",
        "id_estado" => 1,
        "observaciones" => "Test"
    ];

    /**
     * LOGIN - Datos inválidos
     */

    // Email vacío
    const LOGIN_EMAIL_EMPTY = [
        "email" => "",
        "password" => "12345678"
    ];

    // Password vacío
    const LOGIN_PASSWORD_EMPTY = [
        "email" => "root@leadchain.com",
        "password" => ""
    ];

    // Email inválido
    const LOGIN_EMAIL_INVALID = [
        "email" => "not-an-email",
        "password" => "12345678"
    ];

    // Email no existe
    const LOGIN_EMAIL_NONEXISTENT = [
        "email" => "noexiste@leadchain.com",
        "password" => "12345678"
    ];

    // Password incorrecto
    const LOGIN_PASSWORD_WRONG = [
        "email" => "root@leadchain.com",
        "password" => "wrongpassword"
    ];
}
