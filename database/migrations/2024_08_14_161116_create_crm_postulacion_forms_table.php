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
        Schema::create('crm_postulacion_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulaciones_id')->unique()->constrained('crm_postulaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nivel_estudios',11);
            $table->string('nivel_academico',11);
            $table->string('nivel_programacion',11);
            $table->string('servicio_internet',11);
            $table->string('idioma_extranjero',11);
            $table->string('horario_trabajo', 11);
            $table->string('comentario', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_postulacion_forms');
    }
};
