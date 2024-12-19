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
        Schema::create('web_redes_sociales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('web_empresas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre',50);
            $table->string('logo_img')->nullable();
            $table->string('url', 250)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_redes_sociales');
    }
};
