<?php

namespace App\Models;
use App\Models\Crm_curriculas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_especialidades extends Model
{
    use HasFactory;
    public function curriculas()
    {
        return $this->hasMany(Crm_curriculas::class, 'especialidad_id', 'id');
    }
}
