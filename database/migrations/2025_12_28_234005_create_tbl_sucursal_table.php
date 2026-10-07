<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Tbl_Sucursal', function (Blueprint $table) {
            $table->id('suc_id');
            $table->string('suc_nombre', 100);
            $table->string('suc_direccion', 200);
            $table->string('suc_telefono', 20)->nullable();
            $table->string('suc_email', 100)->nullable();
            $table->char('suc_estado', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Tbl_Sucursal');
    }
};