<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_solicitudes extends Model
{
    use HasFactory;
    public function usuario(){
      return $this->belongsTo(User::class, 'user_id');
    }
    public function detalles()
    {
        return $this->hasMany(Crm_solicitud_detalles::class, 'solicitud_id');
    }
}
