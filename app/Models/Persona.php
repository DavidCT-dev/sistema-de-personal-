<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'nombres', 
        'apellido_pat', 
        'apellido_mat', 
        'fecha_nac', 
        'direccion', 
        'ci',  
        'celular', 
        'fech_ing', 
        'fech_baj', 
        'item',
        'tipo_contrato_id', 
        'horario_id', 
        'lugar_trabajo_id', 
        'cargo_id', 
        'genero_id',
        'kardex_visible',
        'auto_sabados',
        'uid_bio',
        'biometrico_registro',
        'total_dias_vacacion',
        'antiguedad',
        'created_at'
    ];

    // Eliminamos la relación biometrico() y agregamos:
    public function biometricos()
    {
        return $this->belongsToMany(Biometrico::class, 'persona_biometrico', 'persona_id', 'biometrico_id')
            ->withTimestamps()
            ->withTrashed();
    }

    // Resto de relaciones...
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id')->withTrashed();
    }

    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'horario_persona', 'persona_id', 'horario_id');
    }

    public function lugarTrabajo()
    {
        return $this->belongsTo(LugarTrabajo::class, 'lugar_trabajo_id')->withTrashed();
    }

    public function cargo()
    {
        return $this->belongsTo(Cargos::class, 'cargo_id')->withTrashed();
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id')->withTrashed();
    }

    public function vacaciones()
    {
        return $this->hasMany(Vacacion::class, 'id_persona')->withTrashed();
    }
}