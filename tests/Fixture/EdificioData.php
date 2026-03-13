<?php

namespace tests\Fixture;

class EdificioData
{
    const EDIFICIO_POST = [
        "direccion_completa" => "Calle Mayor 123",
        "planta" => "2",
        "puerta" => "B",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial",
        "id_cliente" => 1
    ];

    const EDIFICIO_PUT = [
        "direccion_completa" => "Calle Mayor 123",
        "planta" => "7",
        "puerta" => "H",
        "ubicacion" => ["lat" => 40.4168, "lng" => -3.7038],
        "id_zona" => 1,
        "tipo" => "Residencial",
        "id_cliente" => 1
    ];

    const EDIFICIO_PATCH = [
        "planta" => "8",
        "puerta" => "H",
    ];
}
