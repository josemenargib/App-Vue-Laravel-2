<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Crm_entrevista_detalles extends Model
{
    use HasFactory;
    public function entrevistas()
    {
        return $this->belongsTo(Crm_entrevistas::class, 'entrevista_id');
    } 
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
