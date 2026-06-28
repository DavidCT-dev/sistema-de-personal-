<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Models\Persona;
use App\Console\Commands\ActualizarAntiguedadCommand;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


\Illuminate\Support\Facades\Schedule::command('usuarios:actualizar-antiguedad');


// Artisan::command('usuarios:actualizar-antiguedad', function () {
//     $this->comment('El comando está siendo ejecutado cada minuto.');
// })->purpose('Display an inspiring quote')->everyMinute();
