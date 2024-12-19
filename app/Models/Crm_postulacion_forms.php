<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_postulacion_forms extends Model
{
    use HasFactory;
    protected $fillable = [
        'postulaciones_id',
        'nivel_estudios',
        'nivel_academico',
        'nivel_programacion',
        'servicio_internet',
        'idioma_extranjero',
        'horario_trabajo',
        'comentario',
    ];
    public function postulaciones(){
        return $this->belongsTo(Crm_postulaciones::class,'postulaciones_id');
    } 
}
