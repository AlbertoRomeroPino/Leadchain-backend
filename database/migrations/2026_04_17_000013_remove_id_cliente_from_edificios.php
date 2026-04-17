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
        // Solo ejecutar si la columna existe (para bases de datos antiguas)
        if (Schema::hasColumn('edificios', 'id_cliente')) {
            Schema::table('edificios', function (Blueprint $table) {
                // Eliminar la foreign key
                $table->dropForeign(['id_cliente']);
                // Eliminar la columna
                $table->dropColumn('id_cliente');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            // Restaurar la columna
            $table->foreignId('id_cliente')->nullable()->constrained('clientes')->nullOnDelete();
        });
    }
};
