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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 150);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->foreignId('id_usuario_asignado')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        // Índice
        Schema::table('clientes', function (Blueprint $table) {
            $table->index('id_usuario_asignado', 'idx_clientes_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
