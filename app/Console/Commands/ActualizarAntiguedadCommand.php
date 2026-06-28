<?php

namespace App\Console\Commands;

use App\Models\Persona;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ActualizarAntiguedadCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:actualizar-antiguedad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Incrementa la antigüedad de todos los usuarios cada 1 de enero';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $personas = Persona::all();
    $hoy = Carbon::now();

    foreach ($personas as $persona) {
        // Calcular antigüedad en años desde created_at
        $fechaIngreso = Carbon::parse($persona->fech_ing);
        $aniosAntiguedadTotal = $fechaIngreso->diffInYears($hoy);

        // Asegurar que la antigüedad sea un entero
        $aniosAntiguedad = $aniosAntiguedadTotal - $persona->antiguedad;
        $persona->antiguedad += intval($aniosAntiguedad);

        // Calcular días de vacación y asegurarse que sea entero
        $diasVacacion = $this->calcularVacacion($persona->antiguedad);
        $persona->total_dias_vacacion += intval($diasVacacion);

        // No modificar created_at para conservar fecha de ingreso original
        $persona->save();
    }

    $this->info('¡Proceso Completado Antiguedades Actualizadas!');
}

public function calcularVacacion($antiguedad)
{
    if ($antiguedad >= 0 && $antiguedad <= 4) {
        return 15;
    } elseif ($antiguedad >= 5 && $antiguedad <= 9) {
        return 20;
    } elseif ($antiguedad >= 10) {
        return 30;
    }

    return 0;
}

}
