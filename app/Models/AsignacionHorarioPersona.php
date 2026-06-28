<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsignacionHorarioPersona extends Model
{
    use SoftDeletes;

    protected $table = 'asignacion_horario_persona';

    protected $fillable = [
        'id_persona',
        'id_horario',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'deleted_at',
    ];

    /**
     * Relación con el modelo Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Relación con el modelo Horario
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario');
    }
}
