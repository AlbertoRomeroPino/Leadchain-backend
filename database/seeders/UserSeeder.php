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

        // Usuarios comerciales - Se mantienen los 2 primeros
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

        // Comerciales adicionales con varias visitas
        User::updateOrCreate(
            ['email' => 'pedro.martinez@leadchain.com'],
            [
                'nombre' => 'Pedro',
                'apellidos' => 'Martínez Sánchez',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'sofia.hernandez@leadchain.com'],
            [
                'nombre' => 'Sofía',
                'apellidos' => 'Hernández García',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 2,
            ]
        );

        User::updateOrCreate(
            ['email' => 'carlos.lopez@leadchain.com'],
            [
                'nombre' => 'Carlos',
                'apellidos' => 'López Alcaide',
                'password' => Hash::make('12345678'),
                'rol' => 'comercial',
                'id_responsable' => $admin->id,
                'id_zona' => 3,
            ]
        );
    }
}
