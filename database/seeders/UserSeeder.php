<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario admin de oficina (root / root) sin zona asignada
        $admin = User::updateOrCreate(
            ['email' => 'root@leadchain.com'],
            [
                'nombre' => 'Admin',
                'apellidos' => 'Root',
                'password' => Hash::make('12345678'),
                'rol' => 'admin',
                'id_zona' => null,
            ]
        );

        // Usuarios comerciales
        User::updateOrCreate(
            ['email' => 'juan.garcia@leadchain.com'],
            [
                'nombre' => 'Juan',
                'apellidos' => 'García López',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'maria.fernandez@leadchain.com'],
            [
                'nombre' => 'María',
                'apellidos' => 'Fernández Ruiz',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 2,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pedro.martinez@leadchain.com'],
            [
                'nombre' => 'Pedro',
                'apellidos' => 'Martínez Sánchez',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 3,
            ]
        );
    }
}
