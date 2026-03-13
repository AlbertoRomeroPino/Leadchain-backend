<?php

namespace tests\Fixture;

class ZonaData
{
    const ZONA_POST = [
        "nombre_zona" => "Cordoba-rellena",
        "esquina_noroeste" => ["lat" => 38.90, "lng" => -4.50],
        "esquina_noreste"  => ["lat" => 37.90, "lng" => -4.75],
        "esquina_suroeste" => ["lat" => 37.87, "lng" => -4.80],
        "esquina_sureste"  => ["lat" => 37.87, "lng" => -4.75]
    ];
    const ZONA_PUT = [
        "nombre_zona" => "Cordoba",
        "esquina_noroeste" => ["lat" => 37.90, "lng" => -4.80],
        "esquina_noreste"  => ["lat" => 37.90, "lng" => -4.75],
        "esquina_suroeste" => ["lat" => 37.87, "lng" => -4.80],
        "esquina_sureste"  => ["lat" => 37.87, "lng" => -4.75]
    ];
}
