<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_imagenes extends Model
{
    use HasFactory;
    public function actividad(){
        return $this->belongsTo(Web_actividades::class,'actividad_id');
    }
}
