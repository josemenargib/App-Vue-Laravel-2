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
        Schema::create('web_empresas', function (Blueprint $table) {
            $table->id();
            $table->string("razon_social");
            $table->string("nit");
            $table->string("direccion");
            $table->string("telefono");
            $table->string("ciudad");
            $table->string("pais");
            $table->string("representante_legal");
            $table->text("url_banner");
            $table->text("mision");
            $table->text("vision");
            $table->text("about");
            $table->string("latitud");
            $table->string("longitud");
            $table->text("historia");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_empresas');
    }
};
