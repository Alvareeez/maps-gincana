<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lugares_destacados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('direccion');
            $table->decimal('latitud', 14, 8);
            $table->decimal('longitud', 14, 8);
            // RELACION MARCADOR
            $table->foreignId('tipoMarcador')->constrained('tipo_marcador')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares_destacados');
    }
};
