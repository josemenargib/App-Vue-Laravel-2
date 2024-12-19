<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\Role;

class Crm_postulaciones extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'batch_id',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'motivo_postulacion',
    ];
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function batch()
    {
        return $this->belongsTo(Crm_batchs::class, 'batch_id');
    }   
    public function datos_generales() {
        return $this->hasOne(Crm_datos_generales::class, 'user_id');
    }  
}
