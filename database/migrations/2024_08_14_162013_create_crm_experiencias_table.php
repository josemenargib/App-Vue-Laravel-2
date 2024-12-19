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
        Schema::create('crm_experiencias', function (Blueprint $table) {
            $table->id();             
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre'); 
            $table->text('descripcion')->nullable(); 
            $table->date('fecha_inicio')->nullable();
             $table->date('fecha_fin')->nullable();
             $table->boolean('actualidad')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_experiencias');
    }
};
