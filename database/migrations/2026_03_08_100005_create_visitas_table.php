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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_cliente')->constrained('clientes')->restrictOnDelete();
            $table->timestamp('fecha_hora');
            $table->time('hora_visita')->nullable();
            $table->foreignId('id_estado')->constrained('estados_visita')->restrictOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Índices
        Schema::table('visitas', function (Blueprint $table) {
            $table->index('id_usuario', 'idx_visitas_usuario');
            $table->index('id_cliente', 'idx_visitas_cliente');
            $table->index('fecha_hora', 'idx_visitas_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
