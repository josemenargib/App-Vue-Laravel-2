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
        Schema::create('web_reconocimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('web_empresas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('titulo',100);
            $table->string('imagen')->nullable()->default('default.png');
            $table->text('detalle')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_reconocimientos');
    }
};
