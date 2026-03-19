<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonaSeeder extends Seeder
{
    private function buildPolygonWkt(array $zona): string
    {
        $points = [
            $zona['noroeste'],
            $zona['noreste'],
            $zona['sureste'],
            $zona['suroeste'],
            $zona['noroeste'],
        ];

        $coordinates = array_map(
            fn($point) => sprintf('%s %s', (float) $point['lng'], (float) $point['lat']),
            $points
        );

        return 'POLYGON((' . implode(', ', $coordinates) . '))';
    }

    public function run(): void
    {
        // Zonas de Córdoba como cuadrículas con 4 esquinas (geometry points)
        $zonas = [
            [
                'nombre_zona' => 'Centro',
                'noroeste' => ['lat' => 37.8920, 'lng' => -4.7850],
                'noreste'  => ['lat' => 37.8920, 'lng' => -4.7740],
                'suroeste' => ['lat' => 37.8850, 'lng' => -4.7850],
                'sureste'  => ['lat' => 37.8850, 'lng' => -4.7740],
            ],
            [
                'nombre_zona' => 'La Judería',
                'noroeste' => ['lat' => 37.8830, 'lng' => -4.7870],
                'noreste'  => ['lat' => 37.8830, 'lng' => -4.7780],
                'suroeste' => ['lat' => 37.8760, 'lng' => -4.7870],
                'sureste'  => ['lat' => 37.8760, 'lng' => -4.7780],
            ],
            [
                'nombre_zona' => 'San Basilio',
                'noroeste' => ['lat' => 37.8790, 'lng' => -4.7900],
                'noreste'  => ['lat' => 37.8790, 'lng' => -4.7810],
                'suroeste' => ['lat' => 37.8720, 'lng' => -4.7900],
                'sureste'  => ['lat' => 37.8720, 'lng' => -4.7810],
            ],
            [
                'nombre_zona' => 'La Ribera',
                'noroeste' => ['lat' => 37.8900, 'lng' => -4.7780],
                'noreste'  => ['lat' => 37.8900, 'lng' => -4.7680],
                'suroeste' => ['lat' => 37.8820, 'lng' => -4.7780],
                'sureste'  => ['lat' => 37.8820, 'lng' => -4.7680],
            ],
        ];

        foreach ($zonas as $zona) {
            DB::statement("
                INSERT INTO zonas (nombre_zona, area, created_at, updated_at)
                VALUES (
                    ?,
                    ST_GeomFromText(?, 4326),
                    NOW(),
                    NOW()
                )
            ", [
                $zona['nombre_zona'],
                $this->buildPolygonWkt($zona),
            ]);
        }
    }
}
