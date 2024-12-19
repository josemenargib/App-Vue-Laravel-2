<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class Crm_egresados extends Model
{
    use HasFactory;

    protected $table = 'egresados';

    protected $fillable = [
        'user_id',
        'additional_info',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->assignEgresadoRole();
        });

        static::updated(function ($model) {
            $model->assignEgresadoRole();
        });
    }

    protected function assignEgresadoRole()
    {
        if ($this->user) {
            try {
                $this->user->syncRoles(['egresado']);
            } catch (\Exception $e) {
                Log::error('Error al asignar el rol: ' . $e->getMessage());
            }
        }
    }
}
