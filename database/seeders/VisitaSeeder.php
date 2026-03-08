<?php

namespace Database\Seeders;

use App\Models\Visita;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        $visitas = [
            [
                'id_usuario' => 2,
                'id_cliente' => 1,
                'fecha_hora' => '2026-03-10 10:00:00',
                'hora_visita' => '10:00:00',
                'id_estado' => 1,
                'observaciones' => 'Primera visita comercial',
            ],
            [
                'id_usuario' => 2,
                'id_cliente' => 2,
                'fecha_hora' => '2026-03-10 12:00:00',
                'hora_visita' => '12:00:00',
                'id_estado' => 2,
                'observaciones' => 'Interesado en servicios premium',
            ],
            [
                'id_usuario' => 3,
                'id_cliente' => 3,
                'fecha_hora' => '2026-03-11 09:30:00',
                'hora_visita' => '09:30:00',
                'id_estado' => 1,
                'observaciones' => 'Seguimiento de propuesta',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 4,
                'fecha_hora' => '2026-03-12 16:00:00',
                'hora_visita' => '16:00:00',
                'id_estado' => 4,
                'observaciones' => 'Contrato firmado',
            ],
        ];

        foreach ($visitas as $visita) {
            Visita::create($visita);
        }
    }
}
