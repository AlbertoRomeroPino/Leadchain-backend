<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('clientes')->truncate();

        $clientes = [
            ['nombre' => 'Juan', 'apellidos' => 'Pérez Garcia', 'telefono' => '600111222', 'email' => 'Juan.perez@email.com', 'created_at' => '2026-04-22T06:22:23Z', 'updated_at' => '2026-04-22T06:22:23Z'],
            ['nombre' => 'María', 'apellidos' => '', 'telefono' => null, 'email' => 'maria85@email.com', 'created_at' => '2026-04-22T06:22:23Z', 'updated_at' => '2026-04-22T06:22:23Z'],
            ['nombre' => 'Carlos', 'apellidos' => 'Rodríguez', 'telefono' => '611222333', 'email' => null, 'created_at' => '2026-04-22T06:22:23Z', 'updated_at' => '2026-04-22T06:22:23Z'],
            ['nombre' => 'Ana', 'apellidos' => 'López', 'telefono' => '622333444', 'email' => 'ana.lopez@prov.es', 'created_at' => '2026-04-22T06:24:28Z', 'updated_at' => '2026-04-22T06:24:28Z'],
            ['nombre' => 'Luis', 'apellidos' => '', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T06:24:28Z', 'updated_at' => '2026-04-22T06:24:28Z'],
            ['nombre' => 'Elena', 'apellidos' => 'Sánchez Ruíz', 'telefono' => null, 'email' => 'elena.sanchez@mail.com', 'created_at' => '2026-04-22T06:24:28Z', 'updated_at' => '2026-04-22T06:24:28Z'],
            ['nombre' => 'Pedro', 'apellidos' => '', 'telefono' => '633444555', 'email' => null, 'created_at' => '2026-04-22T06:25:49Z', 'updated_at' => '2026-04-22T06:25:49Z'],
            ['nombre' => 'Sofía', 'apellidos' => 'Martínez', 'telefono' => '644555666', 'email' => 'sofia.mtz@web.com', 'created_at' => '2026-04-22T06:25:49Z', 'updated_at' => '2026-04-22T06:25:49Z'],
            ['nombre' => 'Diego', 'apellidos' => '', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T06:25:49Z', 'updated_at' => '2026-04-22T06:25:49Z'],
            ['nombre' => 'Lucia', 'apellidos' => 'Gómez', 'telefono' => null, 'email' => 'lucia.g@servidor.com', 'created_at' => '2026-04-22T07:09:55Z', 'updated_at' => '2026-04-22T07:09:55Z'],
            ['nombre' => 'Manuel', 'apellidos' => 'Ferrero', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T07:09:55Z', 'updated_at' => '2026-04-22T07:09:55Z'],
            ['nombre' => 'Carmen', 'apellidos' => 'Ruíz Lara', 'telefono' => '655666777', 'email' => null, 'created_at' => '2026-04-22T07:09:55Z', 'updated_at' => '2026-04-22T07:09:55Z'],
            ['nombre' => 'Siluro', 'apellidos' => 'El del Pantano', 'telefono' => null, 'email' => 'gluglu@rio.es', 'created_at' => '2026-04-22T07:13:03Z', 'updated_at' => '2026-04-22T07:13:03Z'],
            ['nombre' => 'Piragüista', 'apellidos' => 'Desorientado', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T07:19:46Z', 'updated_at' => '2026-04-22T07:19:46Z'],
            ['nombre' => 'El Caimán', 'apellidos' => 'De la Fuensanta', 'telefono' => null, 'email' => 'no_soy_un_bolso@rio.es', 'created_at' => '2026-04-22T07:19:46Z', 'updated_at' => '2026-04-22T07:19:46Z'],
            ['nombre' => 'Jose', 'apellidos' => '', 'telefono' => null, 'email' => 'jose.test@mail.com', 'created_at' => '2026-04-22T07:21:50Z', 'updated_at' => '2026-04-22T07:21:50Z'],
            ['nombre' => 'Isabel', 'apellidos' => 'Castro', 'telefono' => '666777888', 'email' => 'isabel.castro@email.es', 'created_at' => '2026-04-22T07:21:50Z', 'updated_at' => '2026-04-22T07:21:50Z'],
            ['nombre' => 'Antonio', 'apellidos' => '', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T07:21:50Z', 'updated_at' => '2026-04-22T07:21:50Z'],
            ['nombre' => 'Rosa', 'apellidos' => 'Navarro', 'telefono' => null, 'email' => 'rosa.nav@mail.com', 'created_at' => '2026-04-22T07:24:04Z', 'updated_at' => '2026-04-22T07:24:04Z'],
            ['nombre' => 'Javier', 'apellidos' => '', 'telefono' => '677888999', 'email' => null, 'created_at' => '2026-04-22T07:24:04Z', 'updated_at' => '2026-04-22T07:24:04Z'],
            ['nombre' => 'Teresa', 'apellidos' => 'Díaz Sobrino', 'telefono' => '688999000', 'email' => 'teresa.diaz@web.es', 'created_at' => '2026-04-22T07:24:04Z', 'updated_at' => '2026-04-22T07:24:04Z'],
            ['nombre' => 'Francisco', 'apellidos' => '', 'telefono' => null, 'email' => 'fran.90@email.com', 'created_at' => '2026-04-22T07:24:04Z', 'updated_at' => '2026-04-22T07:24:04Z'],
            ['nombre' => 'Pilar', 'apellidos' => 'Jiménez', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T07:24:36Z', 'updated_at' => '2026-04-22T07:24:36Z'],
            ['nombre' => 'Miguel', 'apellidos' => '', 'telefono' => '699000111', 'email' => null, 'created_at' => '2026-04-22T07:25:07Z', 'updated_at' => '2026-04-22T07:25:07Z'],
            ['nombre' => 'Angela', 'apellidos' => 'Moreno', 'telefono' => '612345678', 'email' => 'angela.m@prov.com', 'created_at' => '2026-04-22T07:26:46Z', 'updated_at' => '2026-04-22T07:26:46Z'],
            ['nombre' => 'Rafael', 'apellidos' => '', 'telefono' => null, 'email' => 'rafa.g@mail.com', 'created_at' => '2026-04-22T07:26:46Z', 'updated_at' => '2026-04-22T07:26:46Z'],
            ['nombre' => 'Concepción', 'apellidos' => '', 'telefono' => null, 'email' => null, 'created_at' => '2026-04-22T07:26:46Z', 'updated_at' => '2026-04-22T07:26:46Z'],
            ['nombre' => 'Alberto', 'apellidos' => 'Vega', 'telefono' => '623456789', 'email' => 'alberto.v@email.com', 'created_at' => '2026-04-22T07:26:46Z', 'updated_at' => '2026-04-22T07:26:46Z'],
            ['nombre' => 'San Rafael Custodio Que Vigila El Puente Y El Rio!', 'apellidos' => 'Protector Eterno De Todos Los Cordobeses Que Se Queja Por Un Bloque De Pisos En Mitad De La Abolafia', 'telefono' => null, 'email' => 'el_arcangel@mezquita.es', 'created_at' => '2026-04-22T07:33:39Z', 'updated_at' => '2026-04-22T07:33:39Z'],
        ];

        foreach ($clientes as $cliente) {
            DB::table('clientes')->insert([
                'nombre' => $cliente['nombre'],
                'apellidos' => $cliente['apellidos'],
                'telefono' => $cliente['telefono'],
                'email' => $cliente['email'],
                'created_at' => $cliente['created_at'],
                'updated_at' => $cliente['updated_at'],
            ]);
        }
    }
}
