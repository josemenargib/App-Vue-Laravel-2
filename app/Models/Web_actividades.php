<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_actividades extends Model
{
    use HasFactory;
    public function imagenes(){
        return $this->hasMany(Web_imagenes::class,'actividad_id');
    }
    public function empresa(){
        return $this->belongsTo(Web_empresas::class,'empresa_id');
    }
}
