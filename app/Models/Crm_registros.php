<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_registros extends Model
{
    use HasFactory;
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function batch(){
        return $this->belongsTo(Crm_batchs::class,'batch_id');
    }
    public function datos_generales() {
        return $this->hasOne(Crm_datos_generales::class, 'user_id');
    }
    use HasFactory;
}
