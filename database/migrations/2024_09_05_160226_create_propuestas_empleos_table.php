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
        Schema::create('propuestas_empleos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 40);
            $table->string('email', 50);
            $table->string('contacto', 40)->nullable();
            $table->string('puesto', 50);
            $table->text('descripcion')->nullable();
            $table->string('modalidad', 20)->nullable();
            $table->string('descripcion_archivo', 250)->nullable();
            $table->string('imagen_oferta', 250)->nullable();
            $table->timestamp('fecha_limite_postulacion');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propuestas_empleos');
    }
};
