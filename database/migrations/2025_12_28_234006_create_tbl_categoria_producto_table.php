<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Tbl_CategoriaServicio', function (Blueprint $table) {
            $table->id('cats_id');
            $table->string('cats_nombre', 100)->unique();
            $table->string('cats_descripcion', 255)->nullable();
            $table->char('cats_estado', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Tbl_CategoriaServicio');
    }
};