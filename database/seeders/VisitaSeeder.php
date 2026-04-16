<?php

namespace Database\Seeders;

use App\Models\Visita;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        // Distribución POR ZONA (cada comercial solo con sus clientes de zona):
        // Zona 1 (Juan García id=2, Pedro Martínez id=4): clientes 1, 4, 5, 9
        // Zona 2 (María Fernández id=3, Sofía Hernández id=5): clientes 2, 6, 7, 10
        // Zona 3 (Carlos López id=6): clientes 3, 8, 11, 15
        // Zona 4: clientes 12, 13, 14, 16 (sin comercial asignado - distribuir entre comerciales multzona)
        // REQUISITO: 2 visitas por estado (16 total), sin repetir cliente

        // $visitas = [
        //     // Estado 1: Vendido - Juan García y María Fernández (zonas diferentes)
        //     ['id_usuario' => 2, 'id_cliente' => 1, 'fecha_hora' => '2026-03-10 10:00:00', 'id_estado' => 1, 'observaciones' => 'Venta exitosa - Cliente muy interesado'],
        //     ['id_usuario' => 3, 'id_cliente' => 2, 'fecha_hora' => '2026-03-11 11:00:00', 'id_estado' => 1, 'observaciones' => 'Venta cerrada - Producto premium'],
            
        //     // Estado 2: En Camino - Pedro Martínez y Sofía Hernández
        //     ['id_usuario' => 4, 'id_cliente' => 4, 'fecha_hora' => '2026-03-12 09:00:00', 'id_estado' => 2, 'observaciones' => 'En ruta hacia el cliente'],
        //     ['id_usuario' => 5, 'id_cliente' => 6, 'fecha_hora' => '2026-03-13 14:00:00', 'id_estado' => 2, 'observaciones' => 'Viajando hacia domicilio'],
            
        //     // Estado 3: Pendiente - Juan García y María Fernández
        //     ['id_usuario' => 2, 'id_cliente' => 5, 'fecha_hora' => '2026-03-14 11:00:00', 'id_estado' => 3, 'observaciones' => 'Presupuesto enviado, pendiente respuesta'],
        //     ['id_usuario' => 3, 'id_cliente' => 7, 'fecha_hora' => '2026-03-15 09:00:00', 'id_estado' => 3, 'observaciones' => 'Propuesta en análisis'],
            
        //     // Estado 4: Volver luego - Pedro Martínez y Sofía Hernández
        //     ['id_usuario' => 4, 'id_cliente' => 9, 'fecha_hora' => '2026-03-16 14:00:00', 'id_estado' => 4, 'observaciones' => 'Cliente pide tiempo para decidir'],
        //     ['id_usuario' => 5, 'id_cliente' => 10, 'fecha_hora' => '2026-03-17 10:00:00', 'id_estado' => 4, 'observaciones' => 'Cliente no disponible, agendar próxima visita'],
            
        //     // Estado 5: Ausente - Carlos López (Zona 3) y Juan García
        //     ['id_usuario' => 6, 'id_cliente' => 3, 'fecha_hora' => '2026-03-18 10:00:00', 'id_estado' => 5, 'observaciones' => 'Nadie en casa, puerta cerrada'],
        //     ['id_usuario' => 2, 'id_cliente' => 12, 'fecha_hora' => '2026-03-19 12:00:00', 'id_estado' => 5, 'observaciones' => 'No abrieron la puerta'],
            
        //     // Estado 6: Local Cerrado - Carlos López y María Fernández
        //     ['id_usuario' => 6, 'id_cliente' => 8, 'fecha_hora' => '2026-03-20 16:00:00', 'id_estado' => 6, 'observaciones' => 'Local cerrado por reforma'],
        //     ['id_usuario' => 3, 'id_cliente' => 13, 'fecha_hora' => '2026-03-21 10:00:00', 'id_estado' => 6, 'observaciones' => 'Negocio cerrado temporalmente'],
            
        //     // Estado 7: No Interesado - Carlos López y Pedro Martínez
        //     ['id_usuario' => 6, 'id_cliente' => 11, 'fecha_hora' => '2026-03-22 12:00:00', 'id_estado' => 7, 'observaciones' => 'Cliente rechazó la propuesta'],
        //     ['id_usuario' => 4, 'id_cliente' => 14, 'fecha_hora' => '2026-03-23 14:00:00', 'id_estado' => 7, 'observaciones' => 'No tiene presupuesto disponible'],
            
        //     // Estado 8: Cancelada - Carlos López y Sofía Hernández
        //     ['id_usuario' => 6, 'id_cliente' => 15, 'fecha_hora' => '2026-03-24 09:00:00', 'id_estado' => 8, 'observaciones' => 'Visita cancelada por cliente'],
        //     ['id_usuario' => 5, 'id_cliente' => 16, 'fecha_hora' => '2026-03-25 11:00:00', 'id_estado' => 8, 'observaciones' => 'Cliente cambió de proveedor'],
        // ];

        // foreach ($visitas as $visita) {
        //     Visita::create($visita);
        // }
    }
}


