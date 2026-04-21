<?php

namespace tests\Fixture;

class ZonaData
{
    const ZONA_POST = [
        "nombre" => "Cordoba-rellena",
        "area" => [
            ["lat" => 37.90, "lng" => -4.80],
            ["lat" => 37.90, "lng" => -4.75],
            ["lat" => 37.87, "lng" => -4.75],
            ["lat" => 37.87, "lng" => -4.80]
        ]
    ];

    const ZONA_PUT = [
        "nombre" => "Cordoba",
        "area" => [
            ["lat" => 37.90, "lng" => -4.81],
            ["lat" => 37.90, "lng" => -4.76],
            ["lat" => 37.87, "lng" => -4.76],
            ["lat" => 37.87, "lng" => -4.81]
        ]
    ];
}
