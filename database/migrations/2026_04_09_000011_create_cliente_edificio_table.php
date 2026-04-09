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
        Schema::create('cliente_edificio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('edificio_id')->constrained('edificios')->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['cliente_id', 'edificio_id']);
        });

        // Migrar datos existentes desde id_cliente en edificios
        DB::statement('
            INSERT INTO cliente_edificio (cliente_id, edificio_id, created_at, updated_at)
            SELECT id_cliente, id, NOW(), NOW()
            FROM edificios
            WHERE id_cliente IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_edificio');
    }
};
