<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crm_certificaciones extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'storage_url', 'user_id', 'is_deleted'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
