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
        Schema::create('web_pagina_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_seccion_id')->constrained('web_paginas_secciones')->onDelete('cascade')->onUpdate('cascade');
            $table->string('detalle',50)->nullable();
            $table->string('url_imagen');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_pagina_imagenes');
    }
};
