<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_empresas extends Model
{
    // Opcionalmente, puedes definir la tabla si no sigue la convención de nombres de Laravel
    protected $table = 'web_empresas';

    // Define los campos que pueden ser llenados de manera masiva
    protected $fillable = [
        'razon_social',
        'nit',
        'direccion',
        'telefono',
        'ciudad',
        'pais',
        'representante_legal',
        'url_banner',
        'mision',
        'vision',
        'about',
        'longitud',
        'latitud',
        'historia'
    ];
}
