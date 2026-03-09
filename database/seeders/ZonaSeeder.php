<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonaSeeder extends Seeder
{
    public function run(): void
    {
        // Zonas de Córdoba como cuadrículas con 4 esquinas
        $zonas = [
            [
                'nombre_zona' => 'Centro',
                'lat_noroeste' => 37.8920, 'lng_noroeste' => -4.7850,
                'lat_noreste' => 37.8920, 'lng_noreste' => -4.7740,
                'lat_suroeste' => 37.8850, 'lng_suroeste' => -4.7850,
                'lat_sureste' => 37.8850, 'lng_sureste' => -4.7740,
            ],
            [
                'nombre_zona' => 'La Judería',
                'lat_noroeste' => 37.8830, 'lng_noroeste' => -4.7870,
                'lat_noreste' => 37.8830, 'lng_noreste' => -4.7780,
                'lat_suroeste' => 37.8760, 'lng_suroeste' => -4.7870,
                'lat_sureste' => 37.8760, 'lng_sureste' => -4.7780,
            ],
            [
                'nombre_zona' => 'San Basilio',
                'lat_noroeste' => 37.8790, 'lng_noroeste' => -4.7900,
                'lat_noreste' => 37.8790, 'lng_noreste' => -4.7810,
                'lat_suroeste' => 37.8720, 'lng_suroeste' => -4.7900,
                'lat_sureste' => 37.8720, 'lng_sureste' => -4.7810,
            ],
            [
                'nombre_zona' => 'La Ribera',
                'lat_noroeste' => 37.8900, 'lng_noroeste' => -4.7780,
                'lat_noreste' => 37.8900, 'lng_noreste' => -4.7680,
                'lat_suroeste' => 37.8820, 'lng_suroeste' => -4.7780,
                'lat_sureste' => 37.8820, 'lng_sureste' => -4.7680,
            ],
        ];

        foreach ($zonas as $zona) {
            DB::table('zonas')->insert(array_merge($zona, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
