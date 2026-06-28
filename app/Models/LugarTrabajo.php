<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LugarTrabajo extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'lugar_trabajos';

    // Los campos que pueden ser asignados masivamente
    protected $fillable = [
        'descripcion',
    ];
}
