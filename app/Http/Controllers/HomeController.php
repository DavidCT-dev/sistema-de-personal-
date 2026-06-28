<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Permiso;
use App\Models\Vacacion;
use App\Models\Horario;
use App\Models\LugarTrabajo;
use App\Models\TipoContrato;
use App\Models\Cargos;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener el año y mes actual
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $currentDate = Carbon::now();

        // Conteo de personas
        $totalPersonas = Persona::count();

        // Cantidad de permisos en el mes actual
        $permisosMesActual = Permiso::whereYear('fecha_permiso', $currentYear)
            ->whereMonth('fecha_permiso', $currentMonth)
            ->count();

        // Cantidad de vacaciones en el año actual
        $vacacionesAnioActual  = Vacacion::where('fecha_fin', '>=', $currentDate)->count();

        // Total de horarios
        $totalHorarios = Horario::count();

        // Total de lugares de trabajo
        $totalLugaresTrabajo = LugarTrabajo::count();

        // Total de tipos de contrato
        $totalTiposContrato = TipoContrato::count();

        // Total de cargos
        $totalCargos = Cargos::count();

        // Pasar los datos a la vista
        return view('dashboard.home', [
            'totalPersonas' => $totalPersonas,
            'permisosMesActual' => $permisosMesActual,
            'vacacionesAnioActual' => $vacacionesAnioActual,
            'totalHorarios' => $totalHorarios,
            'totalLugaresTrabajo' => $totalLugaresTrabajo,
            'totalTiposContrato' => $totalTiposContrato,
            'totalCargos' => $totalCargos,
        ]);
    }
}
