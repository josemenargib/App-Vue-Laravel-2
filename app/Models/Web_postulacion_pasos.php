<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_postulacion_pasos extends Model
{
    use HasFactory;

    public function empresa(){
        return $this->belongsTo(Web_empresas::class,'empresa_id');
    }
}
