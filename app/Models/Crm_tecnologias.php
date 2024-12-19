<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_tecnologias extends Model
{
    use HasFactory;

    // Relación muchos a muchos con crm_modulos a través de crm_curriculas
    public function modulos()
    {
        return $this->belongsToMany(Crm_modulos::class, 'crm_curriculas', 'tecnologia_id', 'modulo_id');
    }
    // Relación muchos a muchos con crm_especialidades a través de crm_curriculas
    public function especialidades()
    {
        return $this->belongsToMany(Crm_especialidades::class, 'crm_curriculas', 'tecnologia_id', 'especialidad_id');
    }
}
