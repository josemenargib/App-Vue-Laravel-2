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
        Schema::create('crm_pruebas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->constrained('crm_postulaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tipo_prueba_id')->constrained('crm_tipo_pruebas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('responsable_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('puntaje',5,2)->nullable();     
            $table->string('rendimiento')->nullable();
            $table->string('codigo_evaluacion',15)->nullable();
            $table->date('fecha');
            $table->string('enlace',250);
            $table->string('enlace_alternativo',250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_pruebas');
    }
};
