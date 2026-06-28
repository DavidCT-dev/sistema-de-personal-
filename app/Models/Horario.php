<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'horarios';

    // Los campos que pueden ser asignados masivamente
    protected $fillable = [
        'descripcion',
        'tolerancia',
        'ingreso1',
        'salida1',
        'ingreso2',
        'salida2',
        'ingreso3',
        'salida3',
        'observaciones'
    ];

    public function personas()
{
    return $this->belongsToMany(Persona::class, 'horario_persona');
}
}
