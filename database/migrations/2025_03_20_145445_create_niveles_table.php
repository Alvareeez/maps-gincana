<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('niveles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('id_lugar')->constrained('lugares')->onDelete('cascade');
            $table->foreignId('id_prueba')->constrained('pruebas')->onDelete('cascade');
            $table->foreignId('id_gincana')->constrained('gincanas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveles');
    }
};

