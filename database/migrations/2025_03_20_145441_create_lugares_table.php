<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lugares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('pista');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 10, 8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares');
    }
};
