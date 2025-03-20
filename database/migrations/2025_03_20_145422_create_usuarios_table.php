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
        // Tabla de usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('username')->unique(); // Nombre de usuario único
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique(); // Correo único
            $table->timestamp('email_verified_at')->nullable(); // Verificación de email
            $table->string('password'); // Contraseña
            $table->foreignId('id_rol')->constrained('roles')->onDelete('cascade'); // Relación con roles
            $table->rememberToken(); // Token para recordar sesión
            $table->timestamps(); // created_at, updated_at
        });

        // Tabla para reinicio de contraseñas
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email como clave primaria
            $table->string('token'); // Token de reseteo
            $table->timestamp('created_at')->nullable(); // Fecha de creación
        });

        // Tabla de sesiones para controlar autenticación
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID de sesión
            $table->foreignId('user_id')->nullable()->constrained('usuarios')->onDelete('cascade'); // Relación con usuarios
            $table->string('ip_address', 45)->nullable(); // Dirección IP del usuario
            $table->text('user_agent')->nullable(); // Información del navegador
            $table->longText('payload'); // Datos de la sesión
            $table->integer('last_activity')->index(); // Última actividad en timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
