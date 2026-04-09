<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Edificio;

class ClienteEdificioSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener datos de planta/puerta del cache (guardado por EdificioSeeder)
        $plantaPuertaData = \Illuminate\Support\Facades\Cache::get('edificios_planta_puerta', []);

        // Primero, insertar todas las relaciones base desde edificios.id_cliente con planta/puerta
        $edificios = Edificio::whereNotNull('id_cliente')->get();

        foreach ($edificios as $edificio) {
            $exists = DB::table('cliente_edificio')
                ->where('cliente_id', $edificio->id_cliente)
                ->where('edificio_id', $edificio->id)
                ->exists();

            if (!$exists) {
                // Buscar los datos de planta/puerta
                $plantaPuerta = collect($plantaPuertaData)->firstWhere('cliente_id', $edificio->id_cliente);
                
                DB::table('cliente_edificio')->insert([
                    'cliente_id' => $edificio->id_cliente,
                    'edificio_id' => $edificio->id,
                    'planta' => $plantaPuerta['planta'] ?? null,
                    'puerta' => $plantaPuerta['puerta'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Luego, insertar relaciones adicionales para que algunos clientes estén en múltiples edificios
        $relacionesAdicionales = [
            ['cliente_id' => 2, 'edificio_id' => 8, 'planta' => '2', 'puerta' => 'F'],   // Carmen también en Blanco Belmonte
            ['cliente_id' => 3, 'edificio_id' => 14, 'planta' => '2', 'puerta' => 'I'],  // Francisco también en Calle Orfila
            ['cliente_id' => 5, 'edificio_id' => 10, 'planta' => '4', 'puerta' => 'J'],  // Javier también en Calle Herrera
            ['cliente_id' => 6, 'edificio_id' => 7, 'planta' => '1', 'puerta' => 'B'],   // Elena también en Calle Judería
            ['cliente_id' => 7, 'edificio_id' => 8, 'planta' => '3', 'puerta' => 'G'],   // Roberto también en Blanco Belmonte
            ['cliente_id' => 13, 'edificio_id' => 3, 'planta' => '1', 'puerta' => null], // Andrés también en Plaza Tendillas
            ['cliente_id' => 14, 'edificio_id' => 2, 'planta' => 'Bajo', 'puerta' => null], // Violeta también en Gran Capitán
        ];

        // Insertar solo relaciones que no existan ya
        foreach ($relacionesAdicionales as $relacion) {
            $exists = DB::table('cliente_edificio')
                ->where('cliente_id', $relacion['cliente_id'])
                ->where('edificio_id', $relacion['edificio_id'])
                ->exists();

            if (!$exists) {
                DB::table('cliente_edificio')->insert([
                    'cliente_id' => $relacion['cliente_id'],
                    'edificio_id' => $relacion['edificio_id'],
                    'planta' => $relacion['planta'],
                    'puerta' => $relacion['puerta'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}



