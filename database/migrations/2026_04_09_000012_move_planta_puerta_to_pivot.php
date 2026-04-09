<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columnas a la tabla pivot
        Schema::table('cliente_edificio', function (Blueprint $table) {
            $table->string('planta')->nullable()->after('edificio_id');
            $table->string('puerta')->nullable()->after('planta');
        });

        // Copiar datos de edificios a la tabla pivot
        DB::statement('
            UPDATE cliente_edificio
            SET planta = (
                SELECT planta FROM edificios WHERE edificios.id = cliente_edificio.edificio_id
            ),
            puerta = (
                SELECT puerta FROM edificios WHERE edificios.id = cliente_edificio.edificio_id
            )
        ');

        // Eliminar columnas de edificios
        Schema::table('edificios', function (Blueprint $table) {
            $table->dropColumn(['planta', 'puerta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Agregar columnas de vuelta a edificios
        Schema::table('edificios', function (Blueprint $table) {
            $table->string('planta')->nullable()->after('tipo');
            $table->string('puerta')->nullable()->after('planta');
        });

        // Copiar datos desde la tabla pivot (usar el primer cliente de cada edificio)
        DB::statement('
            UPDATE edificios
            SET planta = (
                SELECT planta FROM cliente_edificio 
                WHERE cliente_edificio.edificio_id = edificios.id 
                LIMIT 1
            ),
            puerta = (
                SELECT puerta FROM cliente_edificio 
                WHERE cliente_edificio.edificio_id = edificios.id 
                LIMIT 1
            )
        ');

        // Eliminar columnas de la tabla pivot
        Schema::table('cliente_edificio', function (Blueprint $table) {
            $table->dropColumn(['planta', 'puerta']);
        });
    }
};
