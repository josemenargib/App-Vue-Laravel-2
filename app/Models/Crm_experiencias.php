<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_experiencias extends Model
{
    use HasFactory;
   
    protected $fillable = ['nombre', 'descripcion', 'user_id', 'is_deleted', 'fecha_inicio', 'fecha_fin', 'actualidad'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
