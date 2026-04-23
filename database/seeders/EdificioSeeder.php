<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdificioSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('edificios')->truncate();

        $edificios = [
            [
                'direccion_completa' => 'Calle Moricos, 34',
                'id_zona' => 1,
                'tipo' => 'Complejo residencial',
                'ubicacion' => 'POINT (-4.7732001543045 37.890618777723)',
                'created_at' => '2026-04-22T06:07:11Z',
                'updated_at' => '2026-04-22T06:07:11Z',
            ],
            [
                'direccion_completa' => 'Calle Claudio Marcelo, 1',
                'id_zona' => 1,
                'tipo' => 'Residencial',
                'ubicacion' => 'POINT (-4.7790205478668 37.884530729479)',
                'created_at' => '2026-04-22T06:08:06Z',
                'updated_at' => '2026-04-22T06:08:06Z',
            ],
            [
                'direccion_completa' => 'Calle Escañuela, 14',
                'id_zona' => 1,
                'tipo' => 'Edificio',
                'ubicacion' => 'POINT (-4.7680932283401 37.887452039369)',
                'created_at' => '2026-04-22T06:09:22Z',
                'updated_at' => '2026-04-22T06:09:22Z',
            ],
            [
                'direccion_completa' => 'Calle 28 de Febrero, 10',
                'id_zona' => 2,
                'tipo' => 'Edificio',
                'ubicacion' => 'POINT (-4.7607171535492 37.894030912855)',
                'created_at' => '2026-04-22T06:10:53Z',
                'updated_at' => '2026-04-22T06:10:53Z',
            ],
            [
                'direccion_completa' => 'Calle platero  Pedro de Bares, 7',
                'id_zona' => 2,
                'tipo' => 'Edificio',
                'ubicacion' => 'POINT (-4.7611570358276 37.895453294691)',
                'created_at' => '2026-04-22T06:12:05Z',
                'updated_at' => '2026-04-22T06:12:05Z',
            ],
            [
                'direccion_completa' => 'Residencias de universidad Rabanales',
                'id_zona' => 3,
                'tipo' => 'Residencial',
                'ubicacion' => 'POINT (-4.7237992286682 37.914229544738)',
                'created_at' => '2026-04-22T06:13:39Z',
                'updated_at' => '2026-04-22T06:13:39Z',
            ],
            [
                'direccion_completa' => 'Agrocor la torrecilla',
                'id_zona' => 4,
                'tipo' => 'Tienda',
                'ubicacion' => 'POINT (-4.7933650016785 37.854081019215)',
                'created_at' => '2026-04-22T06:14:42Z',
                'updated_at' => '2026-04-22T06:14:42Z',
            ],
            [
                'direccion_completa' => 'Obramat',
                'id_zona' => 4,
                'tipo' => 'Supermercado',
                'ubicacion' => 'POINT (-4.7923028469086 37.847583297192)',
                'created_at' => '2026-04-22T06:16:17Z',
                'updated_at' => '2026-04-22T06:16:17Z',
            ],
            [
                'direccion_completa' => 'Rio Guadalquivir',
                'id_zona' => 5,
                'tipo' => 'Rio',
                'ubicacion' => 'POINT (-4.7820138931274 37.871898501141)',
                'created_at' => '2026-04-22T06:09:54Z',
                'updated_at' => '2026-04-22T07:35:48Z',
            ],
        ];

        foreach ($edificios as $edificio) {
           DB::insert("
                INSERT INTO edificios (direccion_completa, id_zona, tipo, ubicacion, created_at, updated_at)
                VALUES (?, ?, ?, ST_GeomFromText(?, 4326), ?, ?)
            ", [
                $edificio['direccion_completa'],
                $edificio['id_zona'],
                $edificio['tipo'],
                $edificio['ubicacion'],
                $edificio['created_at'],
                $edificio['updated_at'],
            ]);
        }
    }
}
