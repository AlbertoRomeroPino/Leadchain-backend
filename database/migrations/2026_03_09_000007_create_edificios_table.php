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
        Schema::create('edificios', function (Blueprint $table) {
            $table->id();
            $table->string('direccion_completa', 255);
            $table->foreignId('id_zona')->constrained('zonas')->restrictOnDelete();
            $table->string('tipo', 50);
            $table->timestamps();

            $table->index('id_zona', 'idx_edificios_zona');
        });

        // Añadir columna geometry con PostGIS
        DB::statement("ALTER TABLE edificios ADD COLUMN ubicacion GEOMETRY(Point, 4326) NOT NULL");

        // Índice espacial
        DB::statement('CREATE INDEX idx_edificios_ubicacion ON edificios USING GIST(ubicacion)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edificios');
    }
};
