<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_datos_generales extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'nombre', 
        'apellido',
        'ci',
        'telefono',
        'pais',
        'ciudad',
        'direccion',
        'genero',
        'fecha_nacimiento'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
