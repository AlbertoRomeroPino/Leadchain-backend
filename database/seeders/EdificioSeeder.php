<?php

namespace Database\Seeders;

use App\Models\Edificio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdificioSeeder extends Seeder
{
    public function run(): void
    {
        $edificios = [
            // ZONA 1 - Centro (lat 37.8850-37.8920, lng -4.7850 a -4.7740)
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
                'lng' => -4.7761,
                'lat' => 37.8868,
                'id_zona' => 1,
                'tipo' => 'comercial',
                'id_cliente' => 13,
            ],
            [
                'direccion_completa' => 'Plaza de las Tendillas 1, Córdoba',
                'planta' => '1',
                'puerta' => null,
                'lng' => -4.7789,
                'lat' => 37.8880,
                'id_zona' => 1,
                'tipo' => 'comercial',
                'id_cliente' => 14,
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
            [
                'direccion_completa' => 'Avenida Medina Azahara 50, Córdoba',
                'planta' => '4',
                'puerta' => 'E',
                'lng' => -4.7810,
                'lat' => 37.8890,
                'id_zona' => 1,
                'tipo' => 'residencial',
                'id_cliente' => 5,
            ],
            [
                'direccion_completa' => 'Calle Mármoles 18, Córdoba',
                'planta' => '3',
                'puerta' => 'L',
                'lng' => -4.7828,
                'lat' => 37.8865,
                'id_zona' => 1,
                'tipo' => 'residencial',
                'id_cliente' => 12,
            ],

            // ZONA 2 - La Judería (lat 37.8760-37.8830, lng -4.7870 a -4.7780)
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
                'direccion_completa' => 'Calle Blanco Belmonte 12, Córdoba',
                'planta' => '2',
                'puerta' => 'F',
                'lng' => -4.7805,
                'lat' => 37.8790,
                'id_zona' => 2,
                'tipo' => 'residencial',
                'id_cliente' => 6,
            ],
            [
                'direccion_completa' => 'Calle Buen Pastor 7, Córdoba',
                'planta' => '3',
                'puerta' => 'G',
                'lng' => -4.7835,
                'lat' => 37.8776,
                'id_zona' => 2,
                'tipo' => 'residencial',
                'id_cliente' => 7,
            ],
            [
                'direccion_completa' => 'Calle Herrera 9, Córdoba',
                'planta' => '4',
                'puerta' => 'J',
                'lng' => -4.7810,
                'lat' => 37.8800,
                'id_zona' => 2,
                'tipo' => 'residencial',
                'id_cliente' => 10,
            ],

            // ZONA 3 - San Basilio (lat 37.8720-37.8790, lng -4.7900 a -4.7810)
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
                'direccion_completa' => 'Avenida Ocho de Marzo 25, Córdoba',
                'planta' => '1',
                'puerta' => 'H',
                'lng' => -4.7840,
                'lat' => 37.8745,
                'id_zona' => 3,
                'tipo' => 'residencial',
                'id_cliente' => 8,
            ],
            [
                'direccion_completa' => 'Avenida Alsino 20, Córdoba',
                'planta' => '1',
                'puerta' => 'K',
                'lng' => -4.7875,
                'lat' => 37.8730,
                'id_zona' => 3,
                'tipo' => 'residencial',
                'id_cliente' => 11,
            ],
            [
                'direccion_completa' => 'Calle Orfila 14, Córdoba',
                'planta' => '2',
                'puerta' => 'I',
                'lng' => -4.7890,
                'lat' => 37.8768,
                'id_zona' => 3,
                'tipo' => 'residencial',
                'id_cliente' => 9,
            ],
        ];

        // Almacenar datos de planta/puerta para ClienteEdificioSeeder
        $plantaPuertaData = [];
        foreach ($edificios as $index => $edificio) {
            $plantaPuertaData[$index] = [
                'cliente_id' => $edificio['id_cliente'],
                'planta' => $edificio['planta'],
                'puerta' => $edificio['puerta'],
            ];
        }
        \Illuminate\Support\Facades\Cache::put('edificios_planta_puerta', $plantaPuertaData, now()->addHour());

        foreach ($edificios as $edificio) {
            DB::table('edificios')->insert([
                'direccion_completa' => $edificio['direccion_completa'],
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

