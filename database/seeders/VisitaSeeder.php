<?php

namespace Database\Seeders;

use App\Models\Visita;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo de estados: 1=Vendido, 2=En Camino, 3=Pendiente, 4=Volver luego, 5=Ausente, 6=Local Cerrado, 7=No Interesado, 8=Cancelada

        // Juan García (usuario 2) - Zona 1
        $juanVisitas = [
            // Vendido
            ['id_usuario' => 2, 'id_cliente' => 1, 'fecha_hora' => '2026-03-10 10:00:00', 'id_estado' => 1, 'observaciones' => 'Venta exitosa - Cliente muy interesado'],
            ['id_usuario' => 2, 'id_cliente' => 5, 'fecha_hora' => '2026-03-12 14:00:00', 'id_estado' => 1, 'observaciones' => 'Contrato firmado'],
            
            // En Camino
            ['id_usuario' => 2, 'id_cliente' => 9, 'fecha_hora' => '2026-03-15 09:00:00', 'id_estado' => 2, 'observaciones' => 'En ruta hacia el cliente'],
            
            // Pendiente
            ['id_usuario' => 2, 'id_cliente' => 11, 'fecha_hora' => '2026-03-14 11:00:00', 'id_estado' => 3, 'observaciones' => 'Espera feedback del cliente'],
            
            // Volver luego
            ['id_usuario' => 2, 'id_cliente' => 2, 'fecha_hora' => '2026-03-11 15:30:00', 'id_estado' => 4, 'observaciones' => 'Cliente no disponible, agendar próxima visita'],
            
            // Ausente
            ['id_usuario' => 2, 'id_cliente' => 9, 'fecha_hora' => '2026-03-13 10:00:00', 'id_estado' => 5, 'observaciones' => 'Nadie en casa, puerta cerrada'],
            
            // Local Cerrado
            ['id_usuario' => 2, 'id_cliente' => 12, 'fecha_hora' => '2026-03-16 16:00:00', 'id_estado' => 6, 'observaciones' => 'Local cerrado por reforma'],
            
            // No Interesado
            ['id_usuario' => 2, 'id_cliente' => 11, 'fecha_hora' => '2026-03-13 13:00:00', 'id_estado' => 7, 'observaciones' => 'Cliente rechazó la propuesta'],
            
            // Cancelada
            ['id_usuario' => 2, 'id_cliente' => 3, 'fecha_hora' => '2026-03-14 09:00:00', 'id_estado' => 8, 'observaciones' => 'Visita cancelada por cliente'],
        ];

        // María Fernández (usuario 3) - Zona 2
        $mariaVisitas = [
            // Vendido
            ['id_usuario' => 3, 'id_cliente' => 2, 'fecha_hora' => '2026-03-11 11:00:00', 'id_estado' => 1, 'observaciones' => 'Venta cerrada - Producto premium'],
            ['id_usuario' => 3, 'id_cliente' => 6, 'fecha_hora' => '2026-03-13 10:00:00', 'id_estado' => 1, 'observaciones' => 'Cliente satisfecho, repetirá'],
            
            // En Camino
            ['id_usuario' => 3, 'id_cliente' => 7, 'fecha_hora' => '2026-03-15 14:00:00', 'id_estado' => 2, 'observaciones' => 'Viajando hacia domicilio'],
            
            // Pendiente
            ['id_usuario' => 3, 'id_cliente' => 10, 'fecha_hora' => '2026-03-12 09:00:00', 'id_estado' => 3, 'observaciones' => 'Presupuesto enviado, pendiente respuesta'],
            
            // Volver luego
            ['id_usuario' => 3, 'id_cliente' => 6, 'fecha_hora' => '2026-03-10 16:00:00', 'id_estado' => 4, 'observaciones' => 'Cliente pide tiempo para decidir'],
            
            // Ausente
            ['id_usuario' => 3, 'id_cliente' => 8, 'fecha_hora' => '2026-03-14 12:00:00', 'id_estado' => 5, 'observaciones' => 'No abrieron la puerta'],
            
            // Local Cerrado
            ['id_usuario' => 3, 'id_cliente' => 10, 'fecha_hora' => '2026-03-16 10:00:00', 'id_estado' => 6, 'observaciones' => 'Negocio cerrado temporalmente'],
            
            // No Interesado
            ['id_usuario' => 3, 'id_cliente' => 7, 'fecha_hora' => '2026-03-11 14:00:00', 'id_estado' => 7, 'observaciones' => 'No tiene presupuesto disponible'],
            
            // Cancelada
            ['id_usuario' => 3, 'id_cliente' => 8, 'fecha_hora' => '2026-03-15 11:00:00', 'id_estado' => 8, 'observaciones' => 'Cliente cambió de proveedor'],
        ];

        // Pedro Martínez (usuario 4) - Zona 1
        $pedroVisitas = [
            // Vendido
            ['id_usuario' => 4, 'id_cliente' => 4, 'fecha_hora' => '2026-03-12 16:00:00', 'id_estado' => 1, 'observaciones' => 'Contrato firmado y pagado'],
            
            // En Camino
            ['id_usuario' => 4, 'id_cliente' => 5, 'fecha_hora' => '2026-03-16 09:00:00', 'id_estado' => 2, 'observaciones' => 'Saliendo hacia el cliente'],
            
            // Pendiente
            ['id_usuario' => 4, 'id_cliente' => 12, 'fecha_hora' => '2026-03-13 15:00:00', 'id_estado' => 3, 'observaciones' => 'Propuesta en análisis'],
            
            // Volver luego
            ['id_usuario' => 4, 'id_cliente' => 1, 'fecha_hora' => '2026-03-14 10:00:00', 'id_estado' => 4, 'observaciones' => 'Requiere más información'],
            
            // Ausente
            ['id_usuario' => 4, 'id_cliente' => 9, 'fecha_hora' => '2026-03-15 15:00:00', 'id_estado' => 5, 'observaciones' => 'Salió de viaje'],
            
            // Local Cerrado
            ['id_usuario' => 4, 'id_cliente' => 11, 'fecha_hora' => '2026-03-10 14:00:00', 'id_estado' => 6, 'observaciones' => 'Cerrado por inventario'],
            
            // No Interesado
            ['id_usuario' => 4, 'id_cliente' => 12, 'fecha_hora' => '2026-03-11 12:00:00', 'id_estado' => 7, 'observaciones' => 'Precio muy alto según cliente'],
            
            // Cancelada
            ['id_usuario' => 4, 'id_cliente' => 2, 'fecha_hora' => '2026-03-12 11:00:00', 'id_estado' => 8, 'observaciones' => 'Cancelada por problema de salud del cliente'],
        ];

        // Sofía Hernández (usuario 5) - Zona 2
        $sofiaVisitas = [
            // Vendido
            ['id_usuario' => 5, 'id_cliente' => 6, 'fecha_hora' => '2026-03-13 13:00:00', 'id_estado' => 1, 'observaciones' => 'Venta cerrada sin complicaciones'],
            
            // En Camino
            ['id_usuario' => 5, 'id_cliente' => 10, 'fecha_hora' => '2026-03-16 11:00:00', 'id_estado' => 2, 'observaciones' => 'Desplazándose al punto'],
            
            // Pendiente
            ['id_usuario' => 5, 'id_cliente' => 8, 'fecha_hora' => '2026-03-14 14:00:00', 'id_estado' => 3, 'observaciones' => 'Esperando confirmación de cita'],
            
            // Volver luego
            ['id_usuario' => 5, 'id_cliente' => 7, 'fecha_hora' => '2026-03-15 10:00:00', 'id_estado' => 4, 'observaciones' => 'Cliente ocupado este mes'],
            
            // Ausente
            ['id_usuario' => 5, 'id_cliente' => 2, 'fecha_hora' => '2026-03-11 15:00:00', 'id_estado' => 5, 'observaciones' => 'No encontrado en domicilio'],
            
            // Local Cerrado
            ['id_usuario' => 5, 'id_cliente' => 6, 'fecha_hora' => '2026-03-10 10:00:00', 'id_estado' => 6, 'observaciones' => 'Obras en la propiedad'],
            
            // No Interesado
            ['id_usuario' => 5, 'id_cliente' => 10, 'fecha_hora' => '2026-03-12 16:00:00', 'id_estado' => 7, 'observaciones' => 'Ya tiene proveedor'],
            
            // Cancelada
            ['id_usuario' => 5, 'id_cliente' => 7, 'fecha_hora' => '2026-03-14 09:00:00', 'id_estado' => 8, 'observaciones' => 'Decidió cambiar de dirección'],
        ];

        // Carlos López (usuario 6) - Zona 3
        $carlosVisitas = [
            // Vendido
            ['id_usuario' => 6, 'id_cliente' => 3, 'fecha_hora' => '2026-03-11 09:30:00', 'id_estado' => 1, 'observaciones' => 'Seguimiento finalizado exitosamente'],
            ['id_usuario' => 6, 'id_cliente' => 11, 'fecha_hora' => '2026-03-13 11:00:00', 'id_estado' => 1, 'observaciones' => 'Cliente satisfecho, repetirá próximo mes'],
            
            // En Camino
            ['id_usuario' => 6, 'id_cliente' => 4, 'fecha_hora' => '2026-03-15 12:00:00', 'id_estado' => 2, 'observaciones' => 'En ruta, 15 minutos de distancia'],
            
            // Pendiente
            ['id_usuario' => 6, 'id_cliente' => 9, 'fecha_hora' => '2026-03-14 13:00:00', 'id_estado' => 3, 'observaciones' => 'Envío presupuesto detallado'],
            
            // Volver luego
            ['id_usuario' => 6, 'id_cliente' => 3, 'fecha_hora' => '2026-03-12 14:00:00', 'id_estado' => 4, 'observaciones' => 'Cliente consultará con su socio'],
            
            // Ausente
            ['id_usuario' => 6, 'id_cliente' => 8, 'fecha_hora' => '2026-03-10 16:00:00', 'id_estado' => 5, 'observaciones' => 'No disponible hoy'],
            
            // Local Cerrado
            ['id_usuario' => 6, 'id_cliente' => 4, 'fecha_hora' => '2026-03-13 15:00:00', 'id_estado' => 6, 'observaciones' => 'Cierre anual'],
            
            // No Interesado
            ['id_usuario' => 6, 'id_cliente' => 9, 'fecha_hora' => '2026-03-11 11:00:00', 'id_estado' => 7, 'observaciones' => 'Producto no se ajusta a necesidades'],
            
            // Cancelada
            ['id_usuario' => 6, 'id_cliente' => 11, 'fecha_hora' => '2026-03-15 09:00:00', 'id_estado' => 8, 'observaciones' => 'Viaje imprevisto'],
        ];

        $todasVisitas = array_merge($juanVisitas, $mariaVisitas, $pedroVisitas, $sofiaVisitas, $carlosVisitas);

        foreach ($todasVisitas as $visita) {
            Visita::create($visita);
        }
    }
}
