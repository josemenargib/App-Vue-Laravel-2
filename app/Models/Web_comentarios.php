<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Web_comentarios extends Model
{
    use HasFactory;
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    } 
    public function blogs(){
        return $this->belongsTo(Web_blogs::class,'blog_id');
    }
    
}
