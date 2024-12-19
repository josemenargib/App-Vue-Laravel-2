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
        Schema::create('crm_entrevistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulaciones_id')->constrained('crm_postulaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre', 15)->nullable();
            $table->string('tipo', 15);
            $table->date('fecha');  
            $table->time('hora_inicio');  
            $table->time('hora_fin');  
            $table->text('enlace');
            $table->string('estado', 15);
            $table->boolean('rendimiento')->nullable()->default(null);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_entrevistas');
    }
};
