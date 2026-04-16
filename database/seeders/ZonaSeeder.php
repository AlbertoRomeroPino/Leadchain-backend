<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonaSeeder extends Seeder
{
    private function buildPolygonWkt(array $zona): string
    {
        // Si tiene 'poligono' array, usarlo; si no, usar las 4 esquinas
        if (isset($zona['poligono']) && is_array($zona['poligono'])) {
            $points = $zona['poligono'];
        } else {
            $points = [
                $zona['noroeste'],
                $zona['noreste'],
                $zona['sureste'],
                $zona['suroeste'],
                $zona['noroeste'],
            ];
        }

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
            [
                'nombre_zona' => 'Encinarejo',
                'poligono' => [
                    ['lng' => -4.934541551, 'lat' => 37.825505895],
                    ['lng' => -4.918957,    'lat' => 37.826623584],
                    ['lng' => -4.923722524, 'lat' => 37.840271573],
                    ['lng' => -4.928144587, 'lat' => 37.840440887],
                    ['lng' => -4.929217903, 'lat' => 37.840373161],
                    ['lng' => -4.930420017, 'lat' => 37.840407024],
                    ['lng' => -4.931793862, 'lat' => 37.840373161],
                    ['lng' => -4.932652515, 'lat' => 37.839831355],
                    ['lng' => -4.933339437, 'lat' => 37.839628176],
                    ['lng' => -4.93484208,  'lat' => 37.838781593],
                    ['lng' => -4.936387655, 'lat' => 37.838612275],
                    ['lng' => -4.934541551, 'lat' => 37.825505895], // Cierre del polígono
                ],
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
