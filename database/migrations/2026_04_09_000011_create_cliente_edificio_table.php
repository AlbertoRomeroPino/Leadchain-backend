<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('planta', 20)->nullable();
            $table->string('puerta', 10)->nullable();
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['cliente_id', 'edificio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_edificio');
    }
};
