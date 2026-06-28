<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Biometrico extends Model
{
    use SoftDeletes;
    
    protected $table = 'biometricos';

    protected $fillable = [
        'nombre_biometrico',
        'ip_biometrico',
        'nombre_base_de_datos',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'persona_biometrico', 'biometrico_id', 'persona_id')
            ->withTimestamps()
            ->withTrashed();
    }
}