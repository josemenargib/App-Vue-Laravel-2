<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_pagina_imagenes extends Model
{
    use HasFactory;
    /*public function pagina(){
        return $this->belongsTo(Web_paginas::class,'pagina_id');
    }
    public function seccion(){
        return $this->belongsTo(Web_secciones::class,'seccion_id');
    }*/
    public function paginaSeccion(){
        return $this->belongsTo(Web_paginas_secciones::class,'pagina_seccion_id');
    }
}
