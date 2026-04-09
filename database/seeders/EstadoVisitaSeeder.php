<?php

namespace Database\Seeders;

use App\Models\EstadoVisita;
use Illuminate\Database\Seeder;

class EstadoVisitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['etiqueta' => 'Vendido', 'color_hex' => '#2ECC71'],        // Verde: Éxito / Meta cumplida
            ['etiqueta' => 'En Camino', 'color_hex' => '#F1C40F'],      // Amarillo: Acción inmediata
            ['etiqueta' => 'Pendiente', 'color_hex' => '#BDC3C7'],      // Gris claro: Neutral / En espera
            ['etiqueta' => 'Volver luego', 'color_hex' => '#E67E22'],   // Naranja: Precaución / Seguimiento
            ['etiqueta' => 'Ausente', 'color_hex' => '#9B59B6'],        // Púrpura: Re-programar visita
            ['etiqueta' => 'Local Cerrado', 'color_hex' => '#A67C52'],  // Marrón: Obstáculo logístico
            ['etiqueta' => 'No Interesado', 'color_hex' => '#7F8C8D'],  // Gris oscuro: Cierre de oportunidad
            ['etiqueta' => 'Cancelada', 'color_hex' => '#212121'],      // Negro: Pérdida / Alerta
        ];

        foreach ($estados as $estado) {
            EstadoVisita::create($estado);
        }
    }
}
