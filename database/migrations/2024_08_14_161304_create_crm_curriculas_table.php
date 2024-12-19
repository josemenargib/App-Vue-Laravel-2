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
        Schema::create('crm_curriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tecnologia_id')->constrained('crm_tecnologias')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('especialidad_id')->constrained('crm_especialidades')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('modulo_id')->constrained('crm_modulos')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_curriculas');
    }
};
