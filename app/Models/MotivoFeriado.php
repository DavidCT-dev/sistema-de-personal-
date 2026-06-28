<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoFeriado extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'motivo_feriado';

    protected $fillable = [
        'descripcion',
    ];


    public function permisos()
    {
        return $this->hasMany(Feriado::class, 'motivo_feriado_id');
    }
}
