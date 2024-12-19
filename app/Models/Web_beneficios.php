<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_beneficios extends Model
{
    use HasFactory;
    public function Web_beneficios(){
        return $this->belongTo(Web_empresas::class,'empresa_id');
    }
}
