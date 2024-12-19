<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_evaluaciones extends Model
{
    use HasFactory;

    protected $fillable = ['registros_id', 'tipo_prueba_id', 'modulo_id', 'puntaje'];

    public function registro()
    {
        return $this->belongsTo(Crm_registros::class, 'registro_id');
    }
    public function modulo()
    {
        return $this->belongsTo(Crm_modulos::class, 'modulo_id');
    }
    public function tipo_prueba()
    {
        return $this->belongsTo(Crm_tipo_pruebas::class, 'tipo_prueba_id');
    }
}