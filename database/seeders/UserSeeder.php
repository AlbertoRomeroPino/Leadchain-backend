<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario admin (root / root)
        $admin = User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Root',
            'email' => 'root@leadchain.com',
            'password' => Hash::make('root'),
            'rol' => 'admin',
            'id_zona' => 1,
        ]);

        // Usuarios comerciales
        User::create([
            'nombre' => 'Juan',
            'apellidos' => 'García López',
            'email' => 'juan.garcia@leadchain.com',
            'password' => Hash::make('root12'),
            'rol' => 'comercial',
            'id_responsable' => $admin->id,
            'id_zona' => 1,
        ]);

        User::create([
            'nombre' => 'María',
            'apellidos' => 'Fernández Ruiz',
            'email' => 'maria.fernandez@leadchain.com',
            'password' => Hash::make('root12'),
            'rol' => 'comercial',
            'id_responsable' => $admin->id,
            'id_zona' => 2,
        ]);

        User::create([
            'nombre' => 'Pedro',
            'apellidos' => 'Martínez Sánchez',
            'email' => 'pedro.martinez@leadchain.com',
            'password' => Hash::make('root12'),
            'rol' => 'comercial',
            'id_responsable' => $admin->id,
            'id_zona' => 3,
        ]);
    }
}
