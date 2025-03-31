<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('gincanas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable;
            $table->enum('estado', ['abierta', 'ocupada']);
            $table->integer('cantidad_jugadores');
            $table->integer('cantidad_grupos');
            $table->integer('id_ganador')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gincanas');
    }
};
