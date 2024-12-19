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
        Schema::create('crm_datos_generales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre', 50);
            $table->string('apellido', 50)->nullable();
            $table->string('ci', 15)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('pais', 25)->nullable();
            $table->string('ciudad', 25)->nullable();
            $table->string('direccion', 30)->nullable();
            $table->string('foto_perfil', 255)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 11)->nullable();        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_datos_generales');
    }
};
