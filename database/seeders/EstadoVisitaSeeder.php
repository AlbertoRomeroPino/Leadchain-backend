<?php

namespace Database\Seeders;

use App\Models\EstadoVisita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoVisitaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('estados_visita')->truncate();

        $estados = [
            ['id' => 1, 'etiqueta' => 'Vendido', 'color_hex' => '#2ECC71', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 2, 'etiqueta' => 'En Camino', 'color_hex' => '#F1C40F', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 3, 'etiqueta' => 'Pendiente', 'color_hex' => '#BDC3C7', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 4, 'etiqueta' => 'Volver luego', 'color_hex' => '#E67E22', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 5, 'etiqueta' => 'Ausente', 'color_hex' => '#9B59B6', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 6, 'etiqueta' => 'Local Cerrado', 'color_hex' => '#A67C52', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 7, 'etiqueta' => 'No Interesado', 'color_hex' => '#7F8C8D', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
            ['id' => 8, 'etiqueta' => 'Cancelada', 'color_hex' => '#212121', 'created_at' => '2026-04-22T05:52:14Z', 'updated_at' => '2026-04-22T05:52:14Z'],
        ];

        foreach ($estados as $estado) {
            DB::insert("
                INSERT INTO estados_visita (id, etiqueta, color_hex, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $estado['id'],
                $estado['etiqueta'],
                $estado['color_hex'],
                $estado['created_at'],
                $estado['updated_at'],
            ]);
        }
    }
}
