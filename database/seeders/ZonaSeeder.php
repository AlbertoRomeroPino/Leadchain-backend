<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonaSeeder extends Seeder
{
    public function run(): void
    {
        $zonas = [
            ['nombre_zona' => 'Centro', 'lng' => -4.7794, 'lat' => 37.8882],
            ['nombre_zona' => 'La Judería', 'lng' => -4.7822, 'lat' => 37.8794],
            ['nombre_zona' => 'San Basilio', 'lng' => -4.7856, 'lat' => 37.8756],
            ['nombre_zona' => 'La Ribera', 'lng' => -4.7731, 'lat' => 37.8856],
        ];

        foreach ($zonas as $zona) {
            DB::table('zonas')->insert([
                'nombre_zona' => $zona['nombre_zona'],
                'poligono_coordenadas' => DB::raw("ST_GeomFromText('POINT({$zona['lng']} {$zona['lat']})', 4326)"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
