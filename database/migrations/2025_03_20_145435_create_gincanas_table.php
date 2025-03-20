<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('gincanas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('estado', ['abierta', 'ocupada']);
            $table->foreignId('id_ganador')->nullable()->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gincanas');
    }
};

