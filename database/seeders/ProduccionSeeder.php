<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProduccionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EstadoVisitaSeeder::class);

        User::updateOrCreate(
            ['email' => 'root@leadchain.com'],
            [
                'nombre' => 'Admin',
                'apellidos' => 'Testing 1',
                'password' => Hash::make('12345678'),
                'rol' => 'admin',
                'id_zona' => null,
                'id_responsable' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'root2@leadchain.com'],
            [
                'nombre' => 'Admin',
                'apellidos' => 'Testing 2',
                'password' => Hash::make('12345678'),
                'rol' => 'admin',
                'id_zona' => null,
                'id_responsable' => null,
            ]
        );
    }
}
