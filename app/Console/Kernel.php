<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ActualizarAntiguedadCommand;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('usuarios:actualizar-antiguedad')->quarterly();
    // $schedule->command('usuarios:actualizar-antiguedad')->yearly();

        // $schedule->command('usuarios:actualizar-antiguedad')
        // ->everyMinute() // Verifica cada minuto
        // ->when(function () {
        //     $fechaProgramada = Carbon::create(2025, 4, 20, 17, 35, 0, 'America/La_Paz');
        //     return Carbon::now('America/La_Paz')->greaterThanOrEqualTo($fechaProgramada);
        // })
        // ->timezone('America/La_Paz')
        // ->withoutOverlapping(); // Evita ejecuciones duplicadas


        // Ejecutar el comando el primer día de cada mes a las 00:00
        // $schedule->command('usuarios:actualizar-antiguedad')
        // ->monthlyOn(1, '00:00');
    }

    /**
     * Registra los comandos para la aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Aquí puedes registrar tus comandos personalizados
        // 'App\Console\Commands\YourCommand',
        ActualizarAntiguedadCommand::class,
    ];

    protected function scheduleTimezone()
    {
        return 'America/La_Paz';
    }

    /**
     * Define the application's command schedule.
     */
    

    
}
