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
        Schema::create('crm_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('crm_registros')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('modulo_id')->constrained('crm_modulos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tipo_prueba_id')->constrained('crm_tipo_pruebas')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('puntaje',5,2);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_evaluaciones');
    }
};
