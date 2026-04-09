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

        // Insertar todas las relaciones desde edificios.id_cliente con planta/puerta
        $edificios = Edificio::whereNotNull('id_cliente')
            ->where('id_cliente', '<=', 16) // Solo clientes 1-16
            ->get();

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
    }
}
