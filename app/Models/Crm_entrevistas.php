<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Role;

class Crm_entrevistas extends Model
{
    use HasFactory;
    public function postulaciones()
    {
        return $this->belongsTo(Crm_postulaciones::class, 'postulaciones_id');
    }
    public function entrevista_detalle()
    {
        return $this->hasMany(Crm_entrevista_detalles::class, 'entrevista_id');
    }
}
