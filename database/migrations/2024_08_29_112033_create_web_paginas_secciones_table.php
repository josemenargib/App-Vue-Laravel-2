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
        Schema::create('web_paginas_secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_id')->constrained('web_paginas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('seccion_id')->constrained('web_secciones')->onDelete('cascade')->onUpdate('cascade');
            $table->string('tipo_presentacion',1);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_paginas_secciones');
    }
};
