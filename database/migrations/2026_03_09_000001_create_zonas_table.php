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
        // Habilitar extensión PostGIS
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_zona', 100);
            $table->timestamps();
        });

        // Añadir columnas geometry para las 4 esquinas de la zona (cuadrícula)
        DB::statement('ALTER TABLE zonas ADD COLUMN esquina_noroeste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN esquina_noreste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN esquina_suroeste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN esquina_sureste geometry(Point, 4326)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
