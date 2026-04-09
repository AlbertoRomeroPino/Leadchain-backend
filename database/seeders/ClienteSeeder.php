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
            [
                'nombre' => 'Javier',
                'apellidos' => 'Ruiz Vázquez',
                'telefono' => '661567890',
                'email' => 'javier.ruiz@email.com',
            ],
            [
                'nombre' => 'Elena',
                'apellidos' => 'García Martín',
                'telefono' => '662678901',
                'email' => 'elena.garcia@email.com',
            ],
            [
                'nombre' => 'Roberto',
                'apellidos' => 'Sánchez Díaz',
                'telefono' => '663789012',
                'email' => 'roberto.sanchez@email.com',
            ],
            [
                'nombre' => 'Marta',
                'apellidos' => 'Fernández López',
                'telefono' => '664890123',
                'email' => 'marta.fernandez@email.com',
            ],
            [
                'nombre' => 'David',
                'apellidos' => 'Martínez González',
                'telefono' => '665901234',
                'email' => 'david.martinez@email.com',
            ],
            [
                'nombre' => 'Patricia',
                'apellidos' => 'Álvarez Serrano',
                'telefono' => '666012345',
                'email' => 'patricia.alvarez@email.com',
            ],
            [
                'nombre' => 'Miguel',
                'apellidos' => 'Castro Ramos',
                'telefono' => '667123456',
                'email' => 'miguel.castro@email.com',
            ],
            [
                'nombre' => 'Lucía',
                'apellidos' => 'Domínguez Flores',
                'telefono' => '668234567',
                'email' => 'lucia.dominguez@email.com',
            ],
            [
                'nombre' => 'Andrés',
                'apellidos' => 'Molina Herrera',
                'telefono' => '669345678',
                'email' => 'andres.molina@email.com',
            ],
            [
                'nombre' => 'Violeta',
                'apellidos' => 'Navarro Cortés',
                'telefono' => '670456789',
                'email' => 'violeta.navarro@email.com',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
