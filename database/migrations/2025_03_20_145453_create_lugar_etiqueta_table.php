<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lugar_etiqueta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_etiqueta')->constrained('etiquetas')->onDelete('cascade');
            $table->foreignId('id_lugar')->constrained('lugares_destacados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugar_etiqueta');
    }
};
