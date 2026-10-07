<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Tbl_Rol', function (Blueprint $table) {
            $table->id('rol_id');
            $table->string('rol_nombre', 50)->unique();
            $table->string('rol_descripcion', 200)->nullable();
            $table->char('rol_estado', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Tbl_Rol');
    }
};