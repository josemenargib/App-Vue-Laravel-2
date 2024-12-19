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
        Schema::create('crm_batchs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialidad_id')->constrained('crm_especialidades')->onDelete('cascade')->onUpdate('cascade');
            $table->string('version', 15);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('descripcion')->nullable();
            $table->string('requisitos')->nullable();
            $table->string('imagen', 50)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_batchs');
    }
};
