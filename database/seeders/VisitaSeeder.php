<?php

namespace Database\Seeders;

use App\Models\Visita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('visitas')->truncate();

        $visitas = [
            [
                'id_usuario' => 3,
                'id_cliente' => 13,
                'fecha_hora' => '2026-04-22T09:44:00Z',
                'id_estado' => 1,
                'observaciones' => 'Un poco profundo y dificil de llegar pero el cliente muy amable',
                'created_at' => '2026-04-22T07:45:06Z',
                'updated_at' => '2026-04-22T07:45:06Z',
            ],
            [
                'id_usuario' => 3,
                'id_cliente' => 14,
                'fecha_hora' => '2026-04-22T09:45:00Z',
                'id_estado' => 7,
                'observaciones' => 'Me pidio ayuda de como llegar a la orilla. Quizas en otro momento',
                'created_at' => '2026-04-22T07:46:22Z',
                'updated_at' => '2026-04-22T07:46:22Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 1,
                'fecha_hora' => '2026-04-22T02:47:00Z',
                'id_estado' => 1,
                'observaciones' => 'Cliente encantado con la oferta, firma y paga la señal ahora mismo.',
                'created_at' => '2026-04-22T07:54:26Z',
                'updated_at' => '2026-04-22T07:54:26Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 2,
                'fecha_hora' => '2026-04-22T07:54:00Z',
                'id_estado' => 2,
                'observaciones' => 'El camión está cruzando el Puente Romano, llega en unos 5 minutos.',
                'created_at' => '2026-04-22T07:54:50Z',
                'updated_at' => '2026-04-22T07:54:50Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 3,
                'fecha_hora' => '2026-04-22T07:55:00Z',
                'id_estado' => 3,
                'observaciones' => 'Dice que tiene que consultarlo con su mujer y me llama el lunes.',
                'created_at' => '2026-04-22T07:55:05Z',
                'updated_at' => '2026-04-22T07:55:05Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 4,
                'fecha_hora' => '2026-04-22T07:55:00Z',
                'id_estado' => 4,
                'observaciones' => 'Hay muchísima gente en la tienda ahora, me han pedido volver luego.',
                'created_at' => '2026-04-22T07:55:23Z',
                'updated_at' => '2026-04-22T07:55:23Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 5,
                'fecha_hora' => '2026-04-22T07:55:00Z',
                'id_estado' => 5,
                'observaciones' => 'He llamado tres veces al timbre y no sale nadie, dejo nota debajo.',
                'created_at' => '2026-04-22T07:55:33Z',
                'updated_at' => '2026-04-22T07:55:33Z',
            ],
            [
                'id_usuario' => 4,
                'id_cliente' => 6,
                'fecha_hora' => '2026-04-22T07:55:00Z',
                'id_estado' => 6,
                'observaciones' => 'El local está cerrado por reforma hasta la semana que viene seguro.',
                'created_at' => '2026-04-22T07:55:46Z',
                'updated_at' => '2026-04-22T07:55:46Z',
            ],
            [
                'id_usuario' => 5,
                'id_cliente' => 24,
                'fecha_hora' => '2026-04-22T09:56:00Z',
                'id_estado' => 7,
                'observaciones' => 'No le interesa el producto porque dice que ya tiene uno parecido.',
                'created_at' => '2026-04-22T07:56:33Z',
                'updated_at' => '2026-04-22T07:57:10Z',
            ],
            [
                'id_usuario' => 5,
                'id_cliente' => 23,
                'fecha_hora' => '2026-04-22T07:56:00Z',
                'id_estado' => 8,
                'observaciones' => 'Visita cancelada porque el cliente se ha puesto enfermo de repente.',
                'created_at' => '2026-04-22T07:56:48Z',
                'updated_at' => '2026-04-22T07:56:48Z',
            ],
            [
                'id_usuario' => 7,
                'id_cliente' => 19,
                'fecha_hora' => '2026-04-22T07:58:00Z',
                'id_estado' => 1,
                'observaciones' => 'Venta completada, quiere que le enviemos la factura por el correo.',
                'created_at' => '2026-04-22T07:58:13Z',
                'updated_at' => '2026-04-22T07:58:13Z',
            ],
            [
                'id_usuario' => 7,
                'id_cliente' => 20,
                'fecha_hora' => '2026-04-22T07:58:00Z',
                'id_estado' => 2,
                'observaciones' => 'El repartidor está en la zona de La Torrecilla buscando el número.',
                'created_at' => '2026-04-22T07:58:21Z',
                'updated_at' => '2026-04-22T07:58:21Z',
            ],
            [
                'id_usuario' => 7,
                'id_cliente' => 21,
                'fecha_hora' => '2026-04-22T07:58:00Z',
                'id_estado' => 3,
                'observaciones' => 'Tenemos que revisar si quedan unidades en el almacén de Rabanales.',
                'created_at' => '2026-04-22T07:58:38Z',
                'updated_at' => '2026-04-22T07:58:38Z',
            ],
            [
                'id_usuario' => 7,
                'id_cliente' => 22,
                'fecha_hora' => '2026-04-22T07:58:00Z',
                'id_estado' => 4,
                'observaciones' => 'Está reunido con un proveedor, dice que pase después de comer hoy.',
                'created_at' => '2026-04-22T07:58:55Z',
                'updated_at' => '2026-04-22T07:58:55Z',
            ],
            [
                'id_usuario' => 7,
                'id_cliente' => 25,
                'fecha_hora' => '2026-04-22T07:59:00Z',
                'id_estado' => 6,
                'observaciones' => 'Negocio cerrado permanentemente, hay un cartel de se alquila aquí.',
                'created_at' => '2026-04-22T07:59:18Z',
                'updated_at' => '2026-04-22T07:59:18Z',
            ],
            [
                'id_usuario' => 6,
                'id_cliente' => 12,
                'fecha_hora' => '2026-04-22T07:59:00.000Z',
                'id_estado' => 7,
                'observaciones' => 'Dice que el precio es excesivo para su presupuesto de este año.',
                'created_at' => '2026-04-22T07:59:48.000Z',
                'updated_at' => '2026-04-22T07:59:48.000Z',
            ],
            [
                'id_usuario' => 6,
                'id_cliente' => 16,
                'fecha_hora' => '2026-04-22T08:00:00.000Z',
                'id_estado' => 1,
                'observaciones' => 'Todo perfecto, cliente VIP que quiere ampliar el contrato pronto.',
                'created_at' => '2026-04-22T08:00:11.000Z',
                'updated_at' => '2026-04-22T08:00:11.000Z',
            ],
            [
                'id_usuario' => 6,
                'id_cliente' => 17,
                'fecha_hora' => '2026-04-22T08:00:00.000Z',
                'id_estado' => 3,
                'observaciones' => 'Esperando a que el jefe de zona dé el visto bueno al descuento.',
                'created_at' => '2026-04-22T08:00:38.000Z',
                'updated_at' => '2026-04-22T08:00:38.000Z',
            ],
            [
                'id_usuario' => 6,
                'id_cliente' => 11,
                'fecha_hora' => '2026-04-22T08:00:00.000Z',
                'id_estado' => 7,
                'observaciones' => 'Se ha mudado a otra zona y ya no le interesa nuestro servicio.',
                'created_at' => '2026-04-22T08:01:03.000Z',
                'updated_at' => '2026-04-22T08:01:03.000Z',
            ],
        ];

        foreach ($visitas as $visita) {
            Visita::create($visita);
        }
    }
}


