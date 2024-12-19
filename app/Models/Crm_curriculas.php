<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_curriculas extends Model
{
    use HasFactory;
    public function tecnologia() {
        return $this->belongsTo(Crm_tecnologias::class, 'tecnologia_id');
    }
    public function especialidad() {
        return $this->belongsTo(Crm_especialidades::class, 'especialidad_id');
    }
    public function modulo() {
        return $this->belongsTo(Crm_modulos::class, 'modulo_id');
    }
}
