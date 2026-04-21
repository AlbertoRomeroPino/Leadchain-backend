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
            $table->string('nombre', 100);
            $table->timestamps();
        });

        // Añadir columna geometry para el área de la zona
        DB::statement('ALTER TABLE zonas ADD COLUMN area geometry(Polygon, 4326)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
