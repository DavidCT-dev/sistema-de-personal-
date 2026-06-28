<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargos extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'cargos';

    // Los campos que pueden ser asignados masivamente
    protected $fillable = [
        'descripcion',
    ];
}
