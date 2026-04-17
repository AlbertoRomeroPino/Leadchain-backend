<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Edificio;

class ClienteEdificioSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener metadata de edificios (cliente_ids, planta, puerta)
        $edificiosMetadata = \Illuminate\Support\Facades\Cache::get('edificios_metadata', []);

        // Obtener todos los edificios en el mismo orden de CrearEdificioSeeder
        $edificios = Edificio::orderBy('created_at')->get();

        $index = 0;
        foreach ($edificios as $edificio) {
            if (isset($edificiosMetadata[$index])) {
                $metadata = $edificiosMetadata[$index];

                // Insertar relaciones para todos los cliente_ids de este edificio
                foreach ($metadata['cliente_ids'] as $clienteId) {
                    $exists = DB::table('cliente_edificio')
                        ->where('cliente_id', $clienteId)
                        ->where('edificio_id', $edificio->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('cliente_edificio')->insert([
                            'cliente_id' => $clienteId,
                            'edificio_id' => $edificio->id,
                            'planta' => $metadata['planta'] ?? null,
                            'puerta' => $metadata['puerta'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            $index++;
        }
    }
}
