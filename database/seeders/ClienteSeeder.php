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
            ],
            [
                'nombre' => 'Carmen',
                'apellidos' => 'Rodríguez Pérez',
                'telefono' => '658234567',
                'email' => 'carmen.rodriguez@email.com',
            ],
            [
                'nombre' => 'Francisco',
                'apellidos' => 'Jiménez Torres',
                'telefono' => '659345678',
                'email' => 'francisco.jimenez@email.com',
            ],
            [
                'nombre' => 'Isabel',
                'apellidos' => 'Navarro Delgado',
                'telefono' => '660456789',
                'email' => 'isabel.navarro@email.com',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
