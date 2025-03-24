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
            $table->foreignId('lista_id')->constrained('listas')->onDelete('cascade'); // Relación con listas
            $table->foreignId('lugar_destacado_id')->constrained('lugares_destacados')->onDelete('cascade'); // Relación con lugares destacados
            $table->text('tipoMarcador');
            $table->timestamps();
            
            // Evitar que un usuario guarde el mismo lugar más de una vez como favorito
            $table->unique(['lista_id', 'lugar_destacado_id']);
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
