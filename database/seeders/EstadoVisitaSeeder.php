<?php

namespace Database\Seeders;

use App\Models\EstadoVisita;
use Illuminate\Database\Seeder;

class EstadoVisitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['etiqueta' => 'Pendiente', 'color_hex' => '#9E9E9E'],       // Gris: Aún no se ha realizado
            ['etiqueta' => 'En Camino', 'color_hex' => '#2196F3'],       // Azul: El técnico/comercial va hacia allá
            ['etiqueta' => 'En Proceso', 'color_hex' => '#FFC107'],      // Ámbar: Se está realizando la visita
            ['etiqueta' => 'Vendido', 'color_hex' => '#4CAF50'],         // Verde: Éxito total
            ['etiqueta' => 'Ausente', 'color_hex' => '#FF9800'],         // Naranja: El cliente no estaba
            ['etiqueta' => 'No Interesado', 'color_hex' => '#F44336'],   // Rojo: El cliente rechazó la oferta
            ['etiqueta' => 'Volver luego', 'color_hex' => '#673AB7'],    // Púrpura: No pudo atender, pidió otra hora
            ['etiqueta' => 'Local Cerrado', 'color_hex' => '#795548'],   // Marrón: Negocio fuera de horario o cerrado
            ['etiqueta' => 'Presupuestado', 'color_hex' => '#00BCD4'],   // Cian: Se dejó propuesta para decidir
            ['etiqueta' => 'Cancelada', 'color_hex' => '#212121'],       // Negro/Gris oscuro: Anulada previamente
        ];

        foreach ($estados as $estado) {
            EstadoVisita::create($estado);
        }
    }
}
