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
        Schema::create('crm_solicitud_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('crm_solicitudes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('solicitud_estado_id')->constrained('crm_solicitud_estados')->onDelete('cascade')->onUpdate('cascade');
            $table->string('fecha_postulacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_solicitud_detalles');
    }
};
