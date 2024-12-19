<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_solicitud_detalles extends Model
{
    use HasFactory;
    public function solicitud(){
        return $this->belongsTo(Crm_solicitudes::class, 'solicutud_id');
    }
    public function solicitudEstado(){
        return $this->belongsTo(Crm_solicitud_estados::class, 'solicitud_estado_id');
    }
    
}
