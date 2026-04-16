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

        // Insertar todas las relaciones desde edificios con cliente_ids
        $edificios = Edificio::all();

        foreach ($edificios as $edificio) {
            // Buscar los cliente_ids para este edificio
            $metadata = collect($edificiosMetadata)->firstWhere('cliente_ids', function($clienteIds) use ($edificio) {
                return in_array($edificio->id_cliente, $clienteIds);
            });

            // Si no encontramos por id_cliente, buscar por índice en edificios
            if (!$metadata) {
                foreach ($edificiosMetadata as $meta) {
                    if (in_array($edificio->id_cliente, $meta['cliente_ids'])) {
                        $metadata = $meta;
                        break;
                    }
                }
            }

            // Si tenemos metadata, insertar relaciones para todos los cliente_ids
            if ($metadata) {
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
        }
    }
}
