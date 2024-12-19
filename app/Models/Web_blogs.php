<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_blogs extends Model
{
    use HasFactory;
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function datos_users(){
        return $this->belongsTo(Crm_datos_generales::class,'user_id');
    }
    
    public function comentarios(){
        return $this->hasMany(Web_comentarios::class,'blog_id')->where('is_deleted', false)->orderBy('id','desc');
    }
}
