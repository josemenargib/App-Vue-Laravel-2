<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // * Run the migrations.
    public function up(): void
    {
        Schema::create('crm_tecnologias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->text('descripcion');
            $table->string('imagen')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    // * Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('crm_tecnologias');
    }
};
