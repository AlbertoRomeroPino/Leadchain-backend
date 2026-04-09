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
        Schema::table('edificios', function (Blueprint $table) {
            // Hacer id_cliente nullable ya que la relación primaria es many-to-many
            $table->foreignId('id_cliente')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edificios', function (Blueprint $table) {
            $table->foreignId('id_cliente')->nullable(false)->change();
        });
    }
};
