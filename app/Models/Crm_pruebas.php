<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_pruebas extends Model
{
    use HasFactory;

    public function postulaciones(){
        return $this->belongsTo(Crm_postulaciones::class,'postulacion_id');
    }
    public function users(){
        return $this->belongsTo(User::class,'responsable_id');
    }
    public function tipo_pruebas(){
        return $this->belongsTo(Crm_tipo_pruebas::class,'tipo_prueba_id');
    }
}
