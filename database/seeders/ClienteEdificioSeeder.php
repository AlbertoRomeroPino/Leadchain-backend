<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteEdificioSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        DB::table('cliente_edificio')->truncate();

        $relaciones = [
            ['cliente_id' => 1, 'edificio_id' => 1, 'planta' => '1', 'puerta' => 'A'],
            ['cliente_id' => 2, 'edificio_id' => 1, 'planta' => '2', 'puerta' => 'A'],
            ['cliente_id' => 3, 'edificio_id' => 1, 'planta' => 'Bajo', 'puerta' => '1'],
            ['cliente_id' => 4, 'edificio_id' => 2, 'planta' => '3', 'puerta' => 'C'],
            ['cliente_id' => 5, 'edificio_id' => 2, 'planta' => '4', 'puerta' => 'Izquierda'],
            ['cliente_id' => 6, 'edificio_id' => 2, 'planta' => '5', 'puerta' => 'Derecha'],
            ['cliente_id' => 7, 'edificio_id' => 3, 'planta' => 'Entreplanta', 'puerta' => '2'],
            ['cliente_id' => 8, 'edificio_id' => 3, 'planta' => '1', 'puerta' => 'B'],
            ['cliente_id' => 9, 'edificio_id' => 3, 'planta' => '2', 'puerta' => 'A'],
            ['cliente_id' => 10, 'edificio_id' => 4, 'planta' => '3', 'puerta' => '1'],
            ['cliente_id' => 11, 'edificio_id' => 4, 'planta' => '4', 'puerta' => '2'],
            ['cliente_id' => 12, 'edificio_id' => 4, 'planta' => '5', 'puerta' => 'C'],
            ['cliente_id' => 13, 'edificio_id' => 9, 'planta' => 'Lecho del río', 'puerta' => 'Branq. Izq'],
            ['cliente_id' => 14, 'edificio_id' => 9, 'planta' => 'Cubierta', 'puerta' => 'Kayak 4'],
            ['cliente_id' => 15, 'edificio_id' => 9, 'planta' => 'Sótano -2', 'puerta' => 'Fauces'],
            ['cliente_id' => 16, 'edificio_id' => 5, 'planta' => 'Bajo', 'puerta' => 'B'],
            ['cliente_id' => 17, 'edificio_id' => 5, 'planta' => '1', 'puerta' => 'Izquierda'],
            ['cliente_id' => 18, 'edificio_id' => 5, 'planta' => '2', 'puerta' => 'Derecha'],
            ['cliente_id' => 19, 'edificio_id' => 6, 'planta' => '3', 'puerta' => 'A'],
            ['cliente_id' => 20, 'edificio_id' => 6, 'planta' => '4', 'puerta' => 'B'],
            ['cliente_id' => 21, 'edificio_id' => 6, 'planta' => '5', 'puerta' => '1'],
            ['cliente_id' => 22, 'edificio_id' => 6, 'planta' => '1', 'puerta' => '3'],
            ['cliente_id' => 23, 'edificio_id' => 6, 'planta' => '2', 'puerta' => 'C'],
            ['cliente_id' => 24, 'edificio_id' => 6, 'planta' => '3', 'puerta' => 'Izquierda'],
            ['cliente_id' => 25, 'edificio_id' => 6, 'planta' => '4', 'puerta' => 'Derecha'],
            ['cliente_id' => 26, 'edificio_id' => 6, 'planta' => '5', 'puerta' => 'A'],
            ['cliente_id' => 27, 'edificio_id' => 7, 'planta' => 'Bajo', 'puerta' => 'C'],
            ['cliente_id' => 28, 'edificio_id' => 8, 'planta' => '1', 'puerta' => 'D'],
            ['cliente_id' => 29, 'edificio_id' => 9, 'planta' => 'Encima del Triunfo', 'puerta' => 'Derecha'],
        ];

        foreach ($relaciones as $relacion) {
            DB::table('cliente_edificio')->insert([
                'cliente_id' => $relacion['cliente_id'],
                'edificio_id' => $relacion['edificio_id'],
                'planta' => $relacion['planta'],
                'puerta' => $relacion['puerta'],
            ]);
        }
    }
}
