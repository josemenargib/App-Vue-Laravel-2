<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propuestas_empleos extends Model
{
    protected $fillable = [
        'empresa',
        'email',
        'contacto',
        'puesto',
        'descripcion',
        'modalidad',
        'descripcion_archivo',
        'imagen_postulacion',
        'fecha_limite_postulacion',
        'is_deleted'
    ];

    use HasFactory;
}
