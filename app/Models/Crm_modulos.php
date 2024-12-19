<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_modulos extends Model
{
    use HasFactory;

    // Relación muchos a muchos con crm_tecnologias a través de crm_curriculas
    public function tecnologias()
    {
        return $this->belongsToMany(Crm_tecnologias::class, 'crm_curriculas', 'modulo_id', 'tecnologia_id');
    }
    // Relación muchos a muchos con crm_especialidades a través de crm_curriculas
    public function especialidades()
    {
        return $this->belongsToMany(Crm_especialidades::class, 'crm_curriculas', 'modulo_id', 'especialidad_id');
    }

    // Relación muchos a muchos con crm_registros a través de crm_evaluaciones
    public function registros()
    {
        return $this->belongsToMany(Crm_registros::class, 'crm_evaluaciones', 'modulo_id', 'registro_id');
    }
    // Relación muchos a muchos con crm_tipo_pruebas a través de crm_evaluaciones
    public function tipos_pruebas()
    {
        return $this->belongsToMany(Crm_tipo_pruebas::class, 'crm_evaluaciones', 'modulo_id', 'tipo_prueba_id');
    }
}
