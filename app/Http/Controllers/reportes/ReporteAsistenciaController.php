<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Response;
use App\Models\Persona;
use App\Models\Permiso;
use App\Models\Vacacion;
use App\Models\Feriado;
use App\Models\TipoContrato;
use Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\AsignacionHorarioPersona;
use App\Models\MotivoPermiso;

use Carbon\Carbon; // Importar Carbon

class ReporteAsistenciaController extends Controller
{
    public function generatePdfAsistencia(Request $request)
    {
        $empleadoJson = json_decode($request->input('empleado'));
        $marcacionesFinales = json_decode($request->input('marcacionesFinales'));

        $primera = reset($marcacionesFinales);
        $primeraFecha = $primera->fecha;
        $ultima = end($marcacionesFinales);
        $ultimaFecha = $ultima->fecha;

        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $fecha = new \DateTime($primeraFecha);
        $mes = Carbon::parse($fecha)->translatedFormat('F');
        $anio = $fecha->format('Y');

        $empleado = Persona::with(['horarios', 'tipoContrato', 'cargo'])
            ->where('kardex_visible', true)
            ->where('id', $empleadoJson->id)
            ->first();

        if (!$empleado) {
            Toastr::error('Empleado no tiene el kardex visible habilitado', 'Error');
            $empleados = Persona::with(['horario', 'tipoContrato', 'genero'])->get();
            $tiposContrato = TipoContrato::all();

            return view('content.evaluacionAsistencia.evaluacion', compact('empleados', 'tiposContrato'));
        }

        $fechaInicio = Carbon::parse($request->input('fecha_inicio'));
        $fechaFin = Carbon::parse($request->input('fecha_fin'));

        $permisos = Permiso::where(function ($query) use ($empleado) {
            $query->where('id_persona', $empleado->id)
                ->orWhere('solicitante_id', $empleado->id);
        })
            ->where('estado', 'aprobado')
            ->whereBetween('fecha_permiso', [$fechaInicio, $fechaFin])
            ->sum('dias_permiso_total');

        $vacaciones = Vacacion::where(function ($query) use ($empleado) {
            $query->where('id_persona', $empleado->id)
                ->orWhere('solicitante_id', $empleado->id);
        })
            ->where('estado', 'aprobado')
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_inicio', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
                    ->orWhereBetween('fecha_fin', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
                    ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                        $subQuery->where('fecha_inicio', '<=', $fechaInicio->toDateString())
                            ->where('fecha_fin', '>=', $fechaFin->toDateString());
                    });
            })
            ->sum('total_dias');

        foreach ($marcacionesFinales as $marcacion) {
            $fecha = Carbon::parse($marcacion->fecha);

            $tieneHorarioPortero = $empleado->horarios->contains(function ($horario) {
                return str_starts_with(strtoupper($horario->descripcion), 'PORTERO');
            });

            if (($fecha->isSunday() || $fecha->isSaturday()) && !$tieneHorarioPortero) {
                continue;
            }

            $marcacion->dia = ucfirst($fecha->translatedFormat('D'));

            $entrada1 = isset($marcacion->entrada_1) ? Carbon::parse($marcacion->entrada_1) : null;
            $salida1 = isset($marcacion->salida_1) ? Carbon::parse($marcacion->salida_1) : null;
            $entrada2 = isset($marcacion->entrada_2) ? Carbon::parse($marcacion->entrada_2) : null;
            $salida2 = isset($marcacion->salida_2) ? Carbon::parse($marcacion->salida_2) : null;

            foreach ($empleado->horarios as $horario) {
                $tieneDosIngresos = $horario->ingreso2 !== '00:00:00';
            }

            // Horas trabajo
            $segundosTrabajo = 0;
            if ($entrada1 && $salida1 && $salida1->gt($entrada1)) {
                $segundosTrabajo += $entrada1->diffInSeconds($salida1);
            }
            if ($entrada2 && $salida2 && $salida2->gt($entrada2)) {
                $segundosTrabajo += $entrada2->diffInSeconds($salida2);
            }
            $horas = floor($segundosTrabajo / 3600);
            $minutos = floor(($segundosTrabajo % 3600) / 60);
            $segundos = $segundosTrabajo % 60;
            $marcacion->horas_trabajo = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

            // Atrasos
            $marcacion->atrasos = '0.0';
            if (
                ($entrada1 && Carbon::parse($entrada1)->gt(Carbon::parse($marcacion->horario->ingreso1)->addMinutes(Carbon::parse($marcacion->horario->tolerancia)->minute))) ||
                ($entrada2 && Carbon::parse($entrada2)->gt(Carbon::parse($marcacion->horario->ingreso2)->addMinutes(Carbon::parse($marcacion->horario->tolerancia)->minute)))
            ) {
                $marcacion->atrasos = '1';
            }

            // Faltas
            if ($tieneDosIngresos) {
                $sinIngreso1 = empty($entrada1);
                $sinIngreso2 = empty($entrada2);
                $marcacion->faltas = ($sinIngreso1 && $sinIngreso2) ? 2 : 1;

                if (str_starts_with(strtolower($marcacion->obs), 'feriado')) {
                    $marcacion->faltas = 0;
                }
            } else {
                $marcacion->faltas = (empty($entrada1)) ? 1 : 0;
                if (str_starts_with(strtolower($marcacion->obs), 'feriado')) {
                    $marcacion->faltas = 0;
                }
            }

            // Abandono
            if ($tieneDosIngresos) {
                $sinSalida1 = empty($salida1);
                $sinSalida2 = empty($salida2);
                $marcacion->abandono = ($sinSalida1 && $sinSalida2) ? 2 : 1;

                if (str_starts_with(strtolower($marcacion->obs), 'feriado')) {
                    $marcacion->abandono = 0;
                }
            } else {
                $marcacion->abandono = (empty($salida1)) ? 1 : 0;
                if (str_starts_with(strtolower($marcacion->obs), 'feriado')) {
                    $marcacion->abandono = 0;
                }
            }

            // Días de trabajo
            if ($marcacion->faltas >= 2) {
                $marcacion->dias_trabajo = 0;
            } elseif ($marcacion->abandono >= 2) {
                $marcacion->dias_trabajo = 0.5;
            } else {
                $marcacion->dias_trabajo = 1;
            }

            $marcacion->observaciones = $marcacion->obs;
        }

        $pdf = PDF::setPaper('letter', '')->loadView('reportes.reporte-asistencia', [
            'empleado' => $empleado,
            'marcacionesFinales' => $marcacionesFinales,
            'primeraFecha' => $primeraFecha,
            'ultimaFecha' => $ultimaFecha,
            'mes' => $mes,
            'anio' => $anio,
            'vacaciones' => $vacaciones,
            'permisos' => $permisos
        ]);

        return Response::make($pdf->stream(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=archivo.pdf',
        ]);
    }



    public function generarReportePermiso(Request $request)
    {
        // Obtener parámetros individuales
        $empleadoId = $request->input('empleado');
        $motivo = $request->input('motivo');
        $fechaInicio = $request->input('fechaInicioReporte');
        $fechaFin = $request->input('fechaFinReporte');
        // Procesar el rango de fechas

        if (!$fechaInicio || !$fechaFin) {
            Toastr::error('ingrese fecha inicio y fecha  fin', 'Alerta');

            return redirect()->back();
        }

        // Convertir fechas
        $fechaInicioObj = Carbon::createFromFormat('d-m-Y', $fechaInicio);
        $fechaFinObj = Carbon::createFromFormat('d-m-Y', $fechaFin);

        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];

        $fecha = new \DateTime($fechaInicioObj);
        $mesIngles = $fecha->format('F');
        $mes = $meses[$mesIngles];
        $anio = $fecha->format('Y');

        // Configurar la consulta base de permisos
        $query = Permiso::query()
            ->whereBetween('fecha_permiso', [
                $fechaInicioObj->startOfDay(),
                $fechaFinObj->endOfDay()
            ]);

        // Filtrar por empleado si no es "todos"
        if ($empleadoId !== 'todos') {
            $query->with(['tipoPermiso'])->where(function ($q) use ($empleadoId) {
                $q->where('id_persona', $empleadoId)
                    ->orWhere('solicitante_id', $empleadoId);
            });

            // Obtener datos del empleado específico
            $empleado = Persona::with(['tipoContrato', 'horario', 'cargo'])->findOrFail($empleadoId);
        } else {
            // Para el caso de "todos" los empleados
            $empleado = null;
        }

        // Filtrar por motivo si está presente
        if (!empty($motivo)) {
            $query->with(['tipoPermiso'])->where('motivo_permiso_id', $motivo);
        }

        // Obtener los permisos
        $permisos = $query->get();
        // dd($permisos);
        // Si es reporte para todos los empleados, obtener lista de empleados con permisos
        $empleados = null;
        if ($empleadoId === 'todos') {
            $empleadosIds = $permisos->pluck('id_persona')->merge($permisos->pluck('solicitante_id'))->unique();

            $empleados = Persona::with(['tipoContrato', 'horario', 'cargo'])
                ->whereIn('id', $empleadosIds)
                ->get();
        }

        // dd($empleados);
        $pdf = PDF::setPaper('letter')->loadView('reportes.reporte-permisos', [
            'empleado' => $empleado, // null si es "todos"
            'empleados' => $empleados, // lista de empleados si es "todos"
            'primeraFecha' => $fechaInicio,
            'ultimaFecha' => $fechaFin,
            'mes' => $mes,
            'anio' => $anio,
            'permisos' => $permisos,
            'motivoFiltro' => $motivo,
            'todosEmpleados' => $empleadoId === 'todos'
        ]);

        $nombreArchivo = $empleado
            ? "permisos-{$empleado->ci}-{$mes}-{$anio}.pdf"
            : "permisos-todos-{$mes}-{$anio}.pdf";

        return Response::make($pdf->stream(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=' . $nombreArchivo,
        ]);
    }


    public function generarReporteVacacion(Request $request)
    {
        $empleadoId = $request->input('empleado');
        $fechaInicio = $request->input('fechaInicioReporte');
        $fechaFin = $request->input('fechaFinReporte');

        if (!$fechaInicio || !$fechaFin) {
            Toastr::error('Ingrese fecha de inicio y fecha de fin', 'Alerta');
            return redirect()->back();
        }

        // Convertir fechas
        $fechaInicioObj = Carbon::createFromFormat('d-m-Y', $fechaInicio);
        $fechaFinObj = Carbon::createFromFormat('d-m-Y', $fechaFin);

        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];

        $fecha = new \DateTime($fechaInicioObj);
        $mesIngles = $fecha->format('F');
        $mes = $meses[$mesIngles];
        $anio = $fecha->format('Y');

        $query = Vacacion::query()
            ->whereBetween('fecha_inicio', [
                $fechaInicioObj->startOfDay(),
                $fechaFinObj->endOfDay()
            ]);


        $empleado = null;
        $empleados = null;

        if ($empleadoId !== 'todos') {
            $query->where(function ($q) use ($empleadoId) {
                $q->where('id_persona', $empleadoId)
                    ->orWhere('solicitante_id', $empleadoId);
            });

            $empleado = Persona::with(['tipoContrato', 'horario', 'cargo'])->findOrFail($empleadoId);
        }

        $vacaciones = $query->get();

        // Si es "todos", obtener lista de empleados involucrados
        if ($empleadoId === 'todos') {
            $empleadosIds = $vacaciones->pluck('id_persona')->merge($vacaciones->pluck('solicitante_id'))->unique();

            $empleados = Persona::with(['tipoContrato', 'horario', 'cargo'])
                ->whereIn('id', $empleadosIds)
                ->get();
        }

        $pdf = PDF::setPaper('letter')->loadView('reportes.reporte-vacaciones', [
            'empleado' => $empleado, // null si es todos
            'empleados' => $empleados, // colección si es todos
            'primeraFecha' => $fechaInicio,
            'ultimaFecha' => $fechaFin,
            'mes' => $mes,
            'anio' => $anio,
            'vacaciones' => $vacaciones,
            'todosEmpleados' => $empleadoId === 'todos'
        ]);

        $nombreArchivo = $empleado
            ? "vacaciones-{$empleado->ci}-{$mes}-{$anio}.pdf"
            : "vacaciones-todos-{$mes}-{$anio}.pdf";

        return Response::make($pdf->stream(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename=' . $nombreArchivo,
        ]);
    }












function getImageBase64($path)
{
    $fullPath = public_path($path);
    if (!file_exists($fullPath)) {
        return null;
    }
    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
    $data = file_get_contents($fullPath);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

public function generatePdfAsistenciaTodos(Request $request)
{
    // Configuración inicial
    set_time_limit(600); // 10 minutos
    ini_set('memory_limit', '1024M'); // 1GB

    // Validación
    $validated = $request->validate([
        'fechaInicioReporte' => 'required|date',
        'fechaFinReporte' => 'required|date|after_or_equal:fechaInicioReporte',
        'tipoContrato' => 'required|string'
    ]);

    $fechaInicio = Carbon::parse($validated['fechaInicioReporte'])->startOfDay();
    $fechaFin = Carbon::parse($validated['fechaFinReporte'])->endOfDay();

    // Obtener empleados con relaciones necesarias
    $personas = Persona::with([
            'horarios',
            'tipoContrato',
            'cargo',
            'biometricos',
            'lugarTrabajo',
            'genero'
        ])
        ->where('kardex_visible', true)
        ->when($validated['tipoContrato'] !== 'todos', fn($q) => $q->where('tipo_contrato_id', $validated['tipoContrato']))
        ->whereHas('biometricos')
        ->get();

    if ($personas->isEmpty()) {
        Toastr::error('No hay empleados para generar el reporte', 'Alerta');
        return redirect()->back();
    }

    // Precargar datos comunes
    $datosComunes = $this->precargarDatosComunesOptimizado($personas->pluck('id'), $fechaInicio, $fechaFin);

    // Procesar empleados
    $reportes = [];
    $marcacionesPorBase = $this->obtenerMarcacionesPorLote($personas, $fechaInicio, $fechaFin);

    foreach ($personas as $empleado) {
        $marcaciones = $marcacionesPorBase[$empleado->ci] ?? collect();

        $reportes[] = $this->procesarEmpleadoOptimizado(
            $empleado,
            $fechaInicio,
            $fechaFin,
            $marcaciones,
            $datosComunes
        );
    }

    $data = [
        'autfLogo' => $this->getImageBase64('uatf/AUTF.png'),
        'personalLogo' => $this->getImageBase64('uatf/personal.jpg')
    ];

    return response()->json([
        'reportes' => $reportes,
        'fechaInicio' => $fechaInicio->format('d-m-Y'),
        'fechaFin' => $fechaFin->format('d-m-Y'),
        'images' => $data
    ], 200, [], JSON_PRETTY_PRINT);
}

private function precargarDatosComunesOptimizado($idsPersonas, $fechaInicio, $fechaFin)
{
    return [
        'permisos' => Permiso::whereBetween('fecha_permiso', [$fechaInicio, $fechaFin])
            ->whereIn('id_persona', $idsPersonas)
            ->select('id', 'id_persona', 'fecha_permiso', 'motivo_permiso_id')
            ->with('tipoPermiso:id,descripcion')
            ->get()
            ->groupBy(['fecha_permiso', 'id_persona']),

        'vacaciones' => Vacacion::whereDate('fecha_inicio', '<=', $fechaFin)
            ->whereDate('fecha_fin', '>=', $fechaInicio)
            ->whereIn('id_persona', $idsPersonas)
            ->select('id', 'id_persona', 'fecha_inicio', 'fecha_fin')
            ->get()
            ->groupBy('id_persona'),

        'feriados' => Feriado::with('tipoFeriado:id,descripcion')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->select('id', 'fecha', 'descripcion', 'motivo_feriado_id')
            ->get()
            ->keyBy('fecha'),

        'asignaciones' => AsignacionHorarioPersona::whereIn('id_persona', $idsPersonas)
            ->whereDate('fecha_inicio', '<=', $fechaFin)
            ->whereDate('fecha_fin', '>=', $fechaInicio)
            ->with('horario:id,descripcion,ingreso1,salida1,ingreso2,salida2,ingreso3,salida3')
            ->select('id', 'id_persona', 'fecha_inicio', 'fecha_fin', 'id_horario')
            ->get()
            ->groupBy('id_persona')
    ];
}

private function obtenerMarcacionesPorLote($personas, $fechaInicio, $fechaFin)
{
    $marcacionesPorBase = [];

    foreach ($personas as $persona) {
        foreach ($persona->biometricos as $biometrico) {
            $base = $biometrico->nombre_base_de_datos;

            try {
                $marcaciones = DB::table($base)
                    ->where('ci', $persona->ci)
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->select('id', 'fecha', 'hora', 'ci')
                    ->get();

                foreach ($marcaciones as $m) {
                    $ci = $m->ci;
                    $fecha = $m->fecha;

                    if (!isset($marcacionesPorBase[$ci][$fecha])) {
                        $marcacionesPorBase[$ci][$fecha] = collect();
                    }

                    $marcacionesPorBase[$ci][$fecha]->push($m);
                }
            } catch (\Exception $e) {
                Log::error("Error accediendo a BD biométrica {$base}: " . $e->getMessage());
            }
        }
    }

    return $marcacionesPorBase;
}

private function procesarEmpleadoOptimizado($empleado, $fechaInicio, $fechaFin, $marcaciones, $datosComunes)
{
    $marcacionesFinales = $this->procesarAsistenciaOptimizado(
        $empleado,
        $fechaInicio,
        $fechaFin,
        $marcaciones,
        $datosComunes['permisos'],
        $datosComunes['vacaciones'],
        $datosComunes['feriados'],
        $datosComunes['asignaciones']
    );

    $palabrasClave = MotivoPermiso::pluck('descripcion')
        ->map(fn($desc) => strtolower($desc))
        ->all();

    // Normalizar observaciones
    $observacionesNormalizadas = $marcacionesFinales->filter(function ($m) {
        return !empty($m->observaciones);
    })->map(function ($m) {
        return mb_strtolower(trim($m->observaciones));
    });

    // Conteos específicos
    $cuentaHaber = $observacionesNormalizadas->filter(fn($obs) => $obs === 'cuenta haber')->count();
    $cuentaVacacion = $observacionesNormalizadas->filter(fn($obs) => $obs === 'cuenta vacacion')->count();
    $vacacion = $observacionesNormalizadas->filter(fn($obs) => $obs === 'vacación' || $obs === 'vacacion')->count();
    $bajaMedica = $observacionesNormalizadas->filter(fn($obs) => $obs === 'baja médica' || $obs === 'baja medica')->count();

    // Otros permisos
    $otrosPermisos = $observacionesNormalizadas->filter(function ($obs) use ($palabrasClave) {
        return in_array($obs, $palabrasClave);
    })->reject(function ($obs) {
        return in_array($obs, ['cuenta haber', 'cuenta vacacion', 'vacación', 'vacacion', 'baja médica', 'baja medica']);
    })->count();

    return [
        'empleado' => [
            'id' => $empleado->id,
            'ci' => $empleado->ci,
            'nombres' => $empleado->nombres,
            'apellido_pat' => $empleado->apellido_pat,
            'apellido_mat' => $empleado->apellido_mat,
            'item' => $empleado->item,
            'total_dias_vacacion' => $empleado->total_dias_vacacion,
            'antiguedad' => $empleado->antiguedad,
            'auto_sabados' => $empleado->auto_sabados,
            'direccion' => $empleado->direccion,
            'celular' => $empleado->celular,
            'fecha_nac' => $empleado->fecha_nac,
            'fech_ing' => $empleado->fech_ing,
            'fech_baj' => $empleado->fech_baj,
            'horarios' => $empleado->horarios,
            'tipoContrato' => $empleado->tipoContrato,
            'cargo' => $empleado->cargo,
            'lugarTrabajo' => $empleado->lugarTrabajo,
            'genero' => $empleado->genero,
            'biometricos' => $empleado->biometricos
        ],
        'marcacionesFinales' => $marcacionesFinales,
        'vacaciones' => $vacacion,
        'permisos' => $observacionesNormalizadas->filter(fn($obs) => in_array($obs, $palabrasClave))->count(),
        'detalle_permisos' => [
            'cuenta_haber' => $cuentaHaber,
            'cuenta_vacacion' => $cuentaVacacion,
            'vacacion' => $vacacion,
            'baja_medica' => $bajaMedica,
            'otros' => $otrosPermisos,
        ]
    ];
}

private function procesarAsistenciaOptimizado($empleado, $fechaInicio, $fechaFin, $marcaciones, $permisos, $vacaciones, $feriados, $asignaciones)
{
    $dias = collect();
    $fechaActual = $fechaInicio->copy();

    while ($fechaActual <= $fechaFin) {
        $fechaStr = $fechaActual->format('Y-m-d');
        $horario = $this->obtenerHorarioParaFecha($empleado, $fechaStr, $asignaciones);

        // Verificar si tiene horario de portero
        $tieneHorarioPortero = $empleado->horarios->contains(function ($horario) {
            return str_starts_with(strtoupper($horario->descripcion), 'PORTERO');
        });

        // Verificar si tiene auto sábado habilitado
        $autoSabadoHabilitado = $empleado->auto_sabados ?? false;

        // Lógica para determinar si se debe procesar el día
        $procesarDia = true;

        if ($fechaActual->isWeekend()) {
            if ($fechaActual->isSaturday() && $autoSabadoHabilitado) {
                // Procesar el sábado normalmente
                $procesarDia = true;
            } elseif (!$tieneHorarioPortero) {
                // Saltar fines de semana (excepto si es portero o sábado con auto sábado)
                $procesarDia = false;
            }
        }

        if (!$procesarDia) {
            $fechaActual->addDay();
            continue;
        }

        // Procesar el día (laboral, sábado con auto-sábado o portero en fin de semana)
        $marcacionesDia = $marcaciones[$fechaStr] ?? collect();

        $diaProcesado = $this->procesarDiaConMultiplesHorarios(
            $empleado,
            $fechaStr,
            $marcacionesDia,
            $permisos,
            $vacaciones,
            $feriados,
            $horario
        );

        $dias->push($diaProcesado);
        $fechaActual->addDay();
    }

    return $dias;
}

private function procesarDiaConMultiplesHorarios($empleado, $fecha, $marcacionesDia, $permisos, $vacaciones, $feriados, $horario)
{
    $carbonFecha = Carbon::parse($fecha);
    
    $procesado = [
        'fecha' => $carbonFecha->format('Y-m-d'),
        'dia' => ucfirst($carbonFecha->translatedFormat('D')),
        'horario' => $horario,
        'observaciones' => $this->obtenerObservacionesOptimizado(
            $empleado->id,
            $fecha,
            $permisos,
            $vacaciones,
            $feriados
        ),
        'entrada_1' => null,
        'salida_1' => null,
        'entrada_2' => null,
        'salida_2' => null,
        'horas_trabajo' => '00:00:00',
        'atrasos' => 0,
        'abandono' => 0,
        'faltas' => 0,
        'dias_trabajo' => 0
    ];

    if ($marcacionesDia->isNotEmpty()) {
        $marcacionesTimestamps = $marcacionesDia->sortBy('hora')->map(function ($m) {
            return [
                'hora' => $m->hora,
                'timestamp' => strtotime($m->hora),
                'id' => $m->id
            ];
        });

        // Obtener marcaciones según el horario
        $procesado['entrada_1'] = $this->getMarcacion($horario->ingreso1, [-5400, 5400], true, $marcacionesTimestamps);
        $procesado['salida_1'] = $this->getMarcacion($horario->salida1, [-5400, 5400], false, $marcacionesTimestamps);
        $procesado['entrada_2'] = $this->getMarcacion($horario->ingreso2, [-5400, 5400], true, $marcacionesTimestamps);
        $procesado['salida_2'] = $this->getMarcacion($horario->salida2, [-5400, 19800], false, $marcacionesTimestamps);

        // Calcular horas trabajadas
        $segundosTrabajo = 0;
        $tolerancia = $horario->tolerancia ? Carbon::parse($horario->tolerancia)->minute : 0;

        // Turno mañana
        if ($procesado['entrada_1'] && $procesado['salida_1']) {
            $entrada1 = Carbon::parse($procesado['entrada_1']);
            $salida1 = Carbon::parse($procesado['salida_1']);

            if ($salida1->greaterThan($entrada1)) {
                $segundosTrabajo += $entrada1->diffInSeconds($salida1);
            }

            // Atrasos turno mañana
            if ($horario->ingreso1) {
                $ingresoConTolerancia = Carbon::parse($horario->ingreso1)->addMinutes($tolerancia);
                if ($entrada1->greaterThan($ingresoConTolerancia)) {
                    $procesado['atrasos'] += 1;
                }
            }
        }

        // Turno tarde
        if ($procesado['entrada_2'] && $procesado['salida_2']) {
            $entrada2 = Carbon::parse($procesado['entrada_2']);
            $salida2 = Carbon::parse($procesado['salida_2']);

            if ($salida2->greaterThan($entrada2)) {
                $segundosTrabajo += $entrada2->diffInSeconds($salida2);
            }

            // Atrasos turno tarde
            if ($horario->ingreso2) {
                $ingresoConTolerancia = Carbon::parse($horario->ingreso2)->addMinutes($tolerancia);
                if ($entrada2->greaterThan($ingresoConTolerancia)) {
                    $procesado['atrasos'] += 1;
                }
            }
        }

        // Convertir segundos a formato HH:MM:SS
        $horas = floor($segundosTrabajo / 3600);
        $minutos = floor(($segundosTrabajo % 3600) / 60);
        $segundos = $segundosTrabajo % 60;
        $procesado['horas_trabajo'] = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

        // Cálculo de abandono
        $procesado['abandono'] = 0;
        if ($procesado['entrada_1'] && !$procesado['salida_1']) {
            $procesado['abandono'] += 1;
        }
        if ($procesado['entrada_2'] && !$procesado['salida_2']) {
            $procesado['abandono'] += 1;
        }

        // Cálculo de días trabajados
        $tieneHorarioDoble = ($horario->ingreso1 && $horario->ingreso2) ? true : false;

        if ($procesado['abandono'] > 0) {
            if ($tieneHorarioDoble) {
                $completoManana = ($procesado['entrada_1'] && $procesado['salida_1']);
                $completoTarde = ($procesado['entrada_2'] && $procesado['salida_2']);

                if ($completoManana || $completoTarde) {
                    $procesado['dias_trabajo'] = 0.5;
                } else {
                    $procesado['dias_trabajo'] = 0;
                }
            } else {
                $procesado['dias_trabajo'] = 0;
            }
        } else {
            $procesado['dias_trabajo'] = 1;
        }
    }

    return (object)$procesado;
}

private function getMarcacion($horaRef, $rango, $asc, &$marcacionesTimestamps)
{
    if (!$horaRef || $horaRef === '00:00:00') return null;

    $result = $marcacionesTimestamps
        ->filter(function ($m) use ($horaRef, $rango) {
            return $m['timestamp'] >= strtotime($horaRef) + $rango[0] &&
                   $m['timestamp'] <= strtotime($horaRef) + $rango[1];
        });

    $result = $asc ? $result->sortBy('timestamp') : $result->sortByDesc('timestamp');

    $marcacion = $result->first();
    if ($marcacion) {
        $marcacionesTimestamps = $marcacionesTimestamps->reject(fn($m) => $m['id'] === $marcacion['id']);
        return $marcacion['hora'];
    }

    return null;
}

private function obtenerHorarioParaFecha($empleado, $fecha, $asignaciones)
{
    $asignacion = optional($asignaciones[$empleado->id] ?? collect())
        ->first(function ($a) use ($fecha) {
            return $fecha >= $a->fecha_inicio && $fecha <= $a->fecha_fin;
        });

    return $asignacion?->horario ?? $empleado->horarios->first();
}

private function obtenerObservacionesOptimizado($idPersona, $fecha, $permisos, $vacaciones, $feriados)
{
    if ($feriado = $feriados[$fecha] ?? null) {
        return ($feriado->tipoFeriado->descripcion ?? '') . ' - ' . $feriado->descripcion;
    }

    if ($permiso = $permisos[$fecha][$idPersona] ?? null) {
        return $permiso->first()->tipoPermiso->descripcion ?? ($permiso->first()->motivo ?: 'Permiso');
    }

    if (($vacacion = $vacaciones[$idPersona] ?? null) &&
        $vacacion->contains(function ($v) use ($fecha) {
            return $fecha >= $v->fecha_inicio && $fecha <= $v->fecha_fin;
        })
    ) {
        return 'Vacación';
    }

    return '';
}
}
