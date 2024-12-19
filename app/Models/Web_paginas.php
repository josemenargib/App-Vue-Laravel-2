<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_paginas extends Model
{
    use HasFactory;
    public function seccionesPagina(){
        return $this->hasMany(Web_paginas_secciones::class,'pagina_id');
    }
}
