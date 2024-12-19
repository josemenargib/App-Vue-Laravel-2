<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_batchs extends Model
{
    use HasFactory;
    public function Crm_especialidades(){
        return $this->belongsTo(Crm_especialidades::class, "especialidad_id");
    }
public function batchs(){
        return $this->hasMany(Web_convocatorias::class, 'batch_id');
    }
}
