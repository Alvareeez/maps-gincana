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
            $table->decimal('latitud', 12, 12);
            $table->decimal('longitud', 12, 12);
            $table->string('tipoMarcador');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares_destacados');
    }
};
