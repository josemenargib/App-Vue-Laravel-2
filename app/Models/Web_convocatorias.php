<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_convocatorias extends Model
{
    use HasFactory;
    public function batch(){
        return $this->belongsTo(Crm_batchs::class, 'batch_id');
    }
}
