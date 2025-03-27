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
            $table->foreignId('id_lista')->constrained('listas')->onDelete('cascade'); // Relación con listas
            $table->foreignId('lugar_destacado_id')->constrained('lugares_destacados')->onDelete('cascade'); // Relación con lugares destacados
            $table->foreignId('tipoMarcador')->constrained('tipo_marcador')->onDelete('cascade');
            $table->timestamps();

            // Evitar que un usuario guarde el mismo lugar más de una vez como favorito
            $table->unique(['id_lista', 'lugar_destacado_id']);
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
