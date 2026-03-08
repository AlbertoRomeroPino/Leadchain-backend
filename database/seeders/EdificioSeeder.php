<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdificioSeeder extends Seeder
{
    public function run(): void
    {
        $edificios = [
            [
                'direccion_completa' => 'Calle Cruz Conde 15, Córdoba',
                'planta' => '2',
                'puerta' => 'A',
                'lng' => -4.7794,
                'lat' => 37.8882,
                'id_zona' => 1,
                'tipo' => 'residencial',
                'id_cliente' => 1,
            ],
            [
                'direccion_completa' => 'Avenida Gran Capitán 8, Córdoba',
                'planta' => 'Bajo',
                'puerta' => null,
                'lng' => -4.7731,
                'lat' => 37.8900,
                'id_zona' => 1,
                'tipo' => 'comercial',
                'id_cliente' => null,
            ],
            [
                'direccion_completa' => 'Calle Judería 3, Córdoba',
                'planta' => '1',
                'puerta' => 'B',
                'lng' => -4.7822,
                'lat' => 37.8794,
                'id_zona' => 2,
                'tipo' => 'residencial',
                'id_cliente' => 2,
            ],
            [
                'direccion_completa' => 'Calle San Basilio 22, Córdoba',
                'planta' => '3',
                'puerta' => 'C',
                'lng' => -4.7856,
                'lat' => 37.8756,
                'id_zona' => 3,
                'tipo' => 'residencial',
                'id_cliente' => 3,
            ],
            [
                'direccion_completa' => 'Plaza de las Tendillas 1, Córdoba',
                'planta' => '1',
                'puerta' => null,
                'lng' => -4.7789,
                'lat' => 37.8847,
                'id_zona' => 1,
                'tipo' => 'comercial',
                'id_cliente' => null,
            ],
            [
                'direccion_completa' => 'Calle Cruz Conde 15, Córdoba',
                'planta' => '5',
                'puerta' => 'D',
                'lng' => -4.7794,
                'lat' => 37.8882,
                'id_zona' => 1,
                'tipo' => 'residencial',
                'id_cliente' => 4,
            ],
        ];

        foreach ($edificios as $edificio) {
            DB::table('edificios')->insert([
                'direccion_completa' => $edificio['direccion_completa'],
                'planta' => $edificio['planta'],
                'puerta' => $edificio['puerta'],
                'ubicacion' => DB::raw("ST_GeomFromText('POINT({$edificio['lng']} {$edificio['lat']})', 4326)"),
                'id_zona' => $edificio['id_zona'],
                'tipo' => $edificio['tipo'],
                'id_cliente' => $edificio['id_cliente'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
