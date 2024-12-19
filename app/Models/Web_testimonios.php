<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_testimonios extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsTo(User::class,"user_id");
    }

    public function datosUsuario(){
        return $this->belongsTo(Crm_datos_generales::class,"user_id");
    }
}


