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
        Schema::create('crm_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('crm_registros')->onDelete('cascade')->onUpdate('cascade'); 
            $table->string('nombre')->nullable(); 
            $table->text('storage_url')->nullable(); 
            $table->string('numero_referencia'); 
            $table->boolean('is_deleted')->default(false); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_documentos');
    }
};
