<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marcacion extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'marcaciones';

    protected $fillable = [
        'ingreso1', // Fecha en formato 'YYYY-MM-DD'
        'salida1',  // Hora en formato 'HH:MM:SS'
        'ingreso2',    // Número de cédula o identificación
        'salida2',    // Dirección IP
        'id_persona'
    ];

    protected $casts = [
        'fecha' => 'date',  // Cast a tipo date
        'hora' => 'datetime',   // Cast a tipo time
    ];
    
    public function empleado()
    {
        return $this->belongsTo(Persona::class, 'id_persona')->withTrashed();
    }
}
