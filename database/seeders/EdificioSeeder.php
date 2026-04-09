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
            ['direccion' => 'Calle Cruz Conde 15, Córdoba', 'planta' => '2', 'puerta' => 'A', 'lng' => -4.7794, 'lat' => 37.8882, 'zona' => 1, 'tipo' => 'residencial', 'cliente' => 1],
            ['direccion' => 'Avenida Medina Azahara 50, Córdoba', 'planta' => '5', 'puerta' => 'D', 'lng' => -4.7810, 'lat' => 37.8890, 'zona' => 1, 'tipo' => 'residencial', 'cliente' => 4],
            ['direccion' => 'Calle Mármoles 18, Córdoba', 'planta' => '3', 'puerta' => 'E', 'lng' => -4.7828, 'lat' => 37.8865, 'zona' => 1, 'tipo' => 'residencial', 'cliente' => 5],
            ['direccion' => 'Plaza de las Tendillas 1, Córdoba', 'planta' => '1', 'puerta' => null, 'lng' => -4.7789, 'lat' => 37.8880, 'zona' => 1, 'tipo' => 'comercial', 'cliente' => 9],

            // ZONA 2 - La Judería (lat 37.8760-37.8830, lng -4.7870 a -4.7780)
            ['direccion' => 'Calle Judería 3, Córdoba', 'planta' => '1', 'puerta' => 'B', 'lng' => -4.7822, 'lat' => 37.8794, 'zona' => 2, 'tipo' => 'residencial', 'cliente' => 2],
            ['direccion' => 'Calle Blanco Belmonte 12, Córdoba', 'planta' => '2', 'puerta' => 'F', 'lng' => -4.7805, 'lat' => 37.8790, 'zona' => 2, 'tipo' => 'residencial', 'cliente' => 6],
            ['direccion' => 'Calle Buen Pastor 7, Córdoba', 'planta' => '3', 'puerta' => 'G', 'lng' => -4.7835, 'lat' => 37.8776, 'zona' => 2, 'tipo' => 'residencial', 'cliente' => 7],
            ['direccion' => 'Calle Herrera 9, Córdoba', 'planta' => '4', 'puerta' => 'J', 'lng' => -4.7810, 'lat' => 37.8800, 'zona' => 2, 'tipo' => 'residencial', 'cliente' => 10],

            // ZONA 3 - San Basilio (lat 37.8720-37.8790, lng -4.7900 a -4.7810)
            ['direccion' => 'Calle San Basilio 22, Córdoba', 'planta' => '3', 'puerta' => 'C', 'lng' => -4.7856, 'lat' => 37.8756, 'zona' => 3, 'tipo' => 'residencial', 'cliente' => 3],
            ['direccion' => 'Avenida Ocho de Marzo 25, Córdoba', 'planta' => '1', 'puerta' => 'H', 'lng' => -4.7840, 'lat' => 37.8745, 'zona' => 3, 'tipo' => 'residencial', 'cliente' => 8],
            ['direccion' => 'Avenida Alsino 20, Córdoba', 'planta' => '1', 'puerta' => 'K', 'lng' => -4.7875, 'lat' => 37.8730, 'zona' => 3, 'tipo' => 'residencial', 'cliente' => 11],
            ['direccion' => 'Calle Orfila 14, Córdoba', 'planta' => '2', 'puerta' => 'I', 'lng' => -4.7890, 'lat' => 37.8768, 'zona' => 3, 'tipo' => 'residencial', 'cliente' => 15],

            // ZONA 4 - La Ribera (lat 37.8820-37.8900, lng -4.7780 a -4.7680)
            ['direccion' => 'Avenida del Brillante 8, Córdoba', 'planta' => '1', 'puerta' => 'M', 'lng' => -4.7730, 'lat' => 37.8860, 'zona' => 4, 'tipo' => 'residencial', 'cliente' => 12],
            ['direccion' => 'Calle Corregidor Luis de la Cerda 5, Córdoba', 'planta' => '2', 'puerta' => 'N', 'lng' => -4.7750, 'lat' => 37.8845, 'zona' => 4, 'tipo' => 'residencial', 'cliente' => 13],
            ['direccion' => 'Paseo de la Ribera 12, Córdoba', 'planta' => '3', 'puerta' => 'O', 'lng' => -4.7710, 'lat' => 37.8835, 'zona' => 4, 'tipo' => 'comercial', 'cliente' => 14],
            ['direccion' => 'Calle San Fernando 20, Córdoba', 'planta' => '4', 'puerta' => 'P', 'lng' => -4.7770, 'lat' => 37.8875, 'zona' => 4, 'tipo' => 'residencial', 'cliente' => 16],
        ];

        // Almacenar datos de planta/puerta para ClienteEdificioSeeder
        $plantaPuertaData = [];
        foreach ($edificios as $index => $edificio) {
            $plantaPuertaData[$index] = [
                'cliente_id' => $edificio['cliente'],
                'planta' => $edificio['planta'],
                'puerta' => $edificio['puerta'],
            ];
        }
        \Illuminate\Support\Facades\Cache::put('edificios_planta_puerta', $plantaPuertaData, now()->addHour());

        foreach ($edificios as $edificio) {
            DB::table('edificios')->insert([
                'direccion_completa' => $edificio['direccion'],
                'ubicacion' => DB::raw("ST_GeomFromText('POINT({$edificio['lng']} {$edificio['lat']})', 4326)"),
                'id_zona' => $edificio['zona'],
                'tipo' => $edificio['tipo'],
                'id_cliente' => $edificio['cliente'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

