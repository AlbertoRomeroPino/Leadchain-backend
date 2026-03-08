<?php

namespace Database\Seeders;

use App\Models\EstadoVisita;
use Illuminate\Database\Seeder;

class EstadoVisitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['etiqueta' => 'Pendiente', 'color_hex' => '#FFA500'],
            ['etiqueta' => 'Confirmada', 'color_hex' => '#28A745'],
            ['etiqueta' => 'Cancelada', 'color_hex' => '#DC3545'],
            ['etiqueta' => 'Completada', 'color_hex' => '#007BFF'],
            ['etiqueta' => 'Reprogramada', 'color_hex' => '#6C757D'],
        ];

        foreach ($estados as $estado) {
            EstadoVisita::create($estado);
        }
    }
}
