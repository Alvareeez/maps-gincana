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
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lista')->nullable()->constrained('listas')->onDelete('cascade'); // Hacer nullable
            $table->foreignId('lugar_destacado_id')->constrained('lugares_destacados')->onDelete('cascade');
            $table->foreignId('tipoMarcador')->constrained('tipo_marcador')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lugar_destacado_id', 'id_lista']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
