<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Antonio',
                'apellidos' => 'López Moreno',
                'telefono' => '657123456',
                'email' => 'antonio.lopez@email.com',
                'id_usuario_asignado' => 2,
            ],
            [
                'nombre' => 'Carmen',
                'apellidos' => 'Rodríguez Pérez',
                'telefono' => '658234567',
                'email' => 'carmen.rodriguez@email.com',
                'id_usuario_asignado' => 2,
            ],
            [
                'nombre' => 'Francisco',
                'apellidos' => 'Jiménez Torres',
                'telefono' => '659345678',
                'email' => 'francisco.jimenez@email.com',
                'id_usuario_asignado' => 3,
            ],
            [
                'nombre' => 'Isabel',
                'apellidos' => 'Navarro Delgado',
                'telefono' => '660456789',
                'email' => 'isabel.navarro@email.com',
                'id_usuario_asignado' => 4,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
