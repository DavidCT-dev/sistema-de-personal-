<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'permisos';


    protected $fillable = [
        'fecha_permiso',
        'observacion',
        'duracion_permiso',
        'hora_inicio',
        'hora_fin',
        'fecha_fin_varios_dias',
        'dias_permiso_total',
        'estado',
        'aprobado_por_id',
        'aprobado_en',
        'solicitante_id',
        'id_persona',
        'motivo_permiso_id'
    ];

    protected $dates = [
        'fecha_permiso',
        'fecha_fin_varios_dias',
    ];


    public function empleado()
    {
        return $this->belongsTo(Persona::class, 'id_persona')->withTrashed();
    }

    // Relación con la persona que solicita el permiso
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }
    // Relación con quien aprobó el permiso
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }


    public function tipoPermiso()
    {
        return $this->belongsTo(MotivoPermiso::class, 'motivo_permiso_id');
    }
}
