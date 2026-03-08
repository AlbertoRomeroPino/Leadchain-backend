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

        // Añadir columna geometry con PostGIS
        DB::statement("ALTER TABLE zonas ADD COLUMN poligono_coordenadas GEOMETRY(Point, 4326) NOT NULL");
        
        // Índice espacial
        DB::statement('CREATE INDEX idx_zonas_poligono ON zonas USING GIST(poligono_coordenadas)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
