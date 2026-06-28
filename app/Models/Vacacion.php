<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacacion extends Model
{
    // Habilitar Soft Deletes (eliminación lógica)
    use SoftDeletes;

    protected $table = 'vacaciones';

    protected $fillable = [
        'id_persona', 
        'total_dias',

        'observacion',
        'estado',
        'aprobado_en',
        'solicitante_id',
        'aprobado_por_id',
        'fecha_inicio',
        'fecha_fin'
    ];

    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function empleado()
    {
        return $this->belongsTo(Persona::class, 'id_persona')->withTrashed();
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }
}