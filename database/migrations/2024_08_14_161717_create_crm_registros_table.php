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
        Schema::create('crm_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('crm_batchs')->onDelete('cascade')->onUpdate('cascade'); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade'); 
            $table->string('estado')->nullable(); 
            $table->text('descripcion')->nullable(); 
            $table->boolean('is_deleted')->default(false); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_registros');
    }
};
