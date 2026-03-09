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
            // 4 coordenadas que definen las esquinas de la zona (cuadrícula)
            // Esquina Noroeste (NW)
            $table->decimal('lat_noroeste', 10, 7);
            $table->decimal('lng_noroeste', 10, 7);
            // Esquina Noreste (NE)
            $table->decimal('lat_noreste', 10, 7);
            $table->decimal('lng_noreste', 10, 7);
            // Esquina Suroeste (SW)
            $table->decimal('lat_suroeste', 10, 7);
            $table->decimal('lng_suroeste', 10, 7);
            // Esquina Sureste (SE)
            $table->decimal('lat_sureste', 10, 7);
            $table->decimal('lng_sureste', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
