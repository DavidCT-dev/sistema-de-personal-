<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoPermiso extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'motivo_permiso';

    protected $fillable = [
        'descripcion',
    ];


    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'motivo_permiso_id');
    }
}
