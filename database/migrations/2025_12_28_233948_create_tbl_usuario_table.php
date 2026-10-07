<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Tbl_Usuario', function (Blueprint $table) {
            $table->id('usr_id');
            $table->string('usr_nombre', 100);
            $table->string('usr_apellido', 100);
            $table->string('usr_cedula', 10)->unique();
            $table->string('usr_email', 150)->unique();
            $table->string('usr_telefono', 20)->nullable();
            $table->string('usr_password');
            $table->foreignId('usr_rolId')->constrained('Tbl_Rol', 'rol_id');
            $table->timestamp('usr_fechaRegistro')->useCurrent();
            $table->char('usr_estado', 1)->default('A');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Tbl_Usuario');
    }
};