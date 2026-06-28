<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feriado extends Model
{
    use HasFactory, SoftDeletes;

    // Nombre de la tabla asociada (opcional si coincide con el plural del modelo)
    protected $table = 'feriados';

    // Campos que pueden ser asignados de forma masiva
    protected $fillable = [
        'fecha',
        'descripcion',
        'observacion',
        'hora_inicio',
        'hora_fin',
        // 'tipo', //motivo   gestion   
                              
        'sexo',
        'motivo_feriado_id'
    ];

    // Opcional: Casts para definir el tipo de datos de los campos
    // protected $casts = [
    //     'fecha' => 'date',
    //     'hora_inicio' => 'datetime:H:i',
    //     'hora_fin' => 'datetime:H:i',
    // ];

    public function tipoFeriado()
    {
        return $this->belongsTo(MotivoFeriado::class, 'motivo_feriado_id');
    }
}
