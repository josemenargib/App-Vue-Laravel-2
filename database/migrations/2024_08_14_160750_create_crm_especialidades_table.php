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
        Schema::create('crm_especialidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion_corta',250)->nullable();
            $table->text('descripcion_larga')->nullable();
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
        Schema::dropIfExists('crm_especialidades');
    }
};
