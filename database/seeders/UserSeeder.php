<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('users')->truncate();

        $users = [
            [
                'nombre' => 'Admin',
                'apellidos' => 'Testing 1',
                'email' => 'root@leadchain.com',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'admin',
                'id_responsable' => null,
                'id_zona' => null,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T05:52:14Z',
                'updated_at' => '2026-04-22T05:52:14Z',
            ],
            [
                'nombre' => 'Admin',
                'apellidos' => 'Testing 2',
                'email' => 'root2@leadchain.com',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'admin',
                'id_responsable' => null,
                'id_zona' => null,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T05:52:15Z',
                'updated_at' => '2026-04-22T05:52:15Z',
            ],
            [
                'nombre' => 'El Pato',
                'apellidos' => 'Mareado De La Albolafia Que No Encuentra El Edificio',
                'email' => 'pato.rio@guadalquivir.es',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'comercial',
                'id_responsable' => 1,
                'id_zona' => 5,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T07:40:28Z',
                'updated_at' => '2026-04-22T07:40:28Z',
            ],
            [

                'nombre' => 'Rafael',
                'apellidos' => 'Cruz Montilla',
                'email' => 'rafa.centro@comercial.es',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'comercial',
                'id_responsable' => 1,
                'id_zona' => 1,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T07:41:22Z',
                'updated_at' => '2026-04-22T07:41:22Z',
            ],
            [
                'nombre' => 'Carmen',
                'apellidos' => 'Flores Jurado',
                'email' => 'carmen.torrecilla@comercial.es',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'comercial',
                'id_responsable' => 1,
                'id_zona' => 4,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T07:41:54Z',
                'updated_at' => '2026-04-22T07:41:54Z',
            ],
            [
                'nombre' => 'Manuel',
                'apellidos' => 'Ortiz Serrano',
                'email' => 'manuel.sagunto@comercial.es',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'comercial',
                'id_responsable' => 1,
                'id_zona' => 2,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T07:42:30Z',
                'updated_at' => '2026-04-22T07:42:30Z',
            ],
            [
                'nombre' => 'Lucía',
                'apellidos' => 'García Roldán',
                'email' => 'lucia.rabanales@comercial.es',
                'password' => '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC',
                'rol' => 'comercial',
                'id_responsable' => 1,
                'id_zona' => 3,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => '2026-04-22T07:42:59Z',
                'updated_at' => '2026-04-22T07:42:59Z',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'nombre' => $user['nombre'],
                'apellidos' => $user['apellidos'],
                'email' => $user['email'],
                'password' => $user['password'],
                'rol' => $user['rol'],
                'id_responsable' => $user['id_responsable'],
                'id_zona' => $user['id_zona'],
                'email_verified_at' => $user['email_verified_at'],
                'remember_token' => $user['remember_token'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
            ]);
        }
    }
}
