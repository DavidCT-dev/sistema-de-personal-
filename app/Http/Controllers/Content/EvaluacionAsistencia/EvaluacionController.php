<?php

namespace App\Http\Controllers\Content\EvaluacionAsistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permiso;
use App\Models\Vacacion;
use App\Models\Horario;
use App\Models\Feriado;
use App\Models\LugarTrabajo;
use App\Models\TipoContrato;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use Carbon\Carbon; // Importar Carbon
use Toastr;
use Exception;
use App\Models\Marcacion;
use App\Models\AsignacionHorarioPersona;

class EvaluacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empleados = Persona::with('horarios')->get();
        $tiposContrato = TipoContrato::all();
        return view('content.evaluacionAsistencia.evaluacion', compact('empleados', 'tiposContrato'));
    }



    public function show(Request $request, $id)
    {
        // Validar los parámetros de la solicitud
        $fechaInicioParam = $request->query->get('fechaInicioReporte');
        $fechaFinParam = $request->query->get('fechaFinReporte');
        $empleadoIdParam = $request->query->get('empleado_id');

        if (!$fechaInicioParam || !$fechaFinParam  || !$empleadoIdParam) {
            Toastr::error('Seleccione fechas y empleado', 'Danger');
            return redirect()->back();
        }

        // Reemplazar espacios por guiones para que el formato sea correcto
        $fechaInicioStr = str_replace(' ', '-', trim($fechaInicioParam));
        $fechaFinStr = str_replace(' ', '-', trim($fechaFinParam));

        // Convertir a objetos Carbon con formato definido
        $fechaInicio = Carbon::createFromFormat('d-m-Y', $fechaInicioStr);
        $fechaFin = Carbon::createFromFormat('d-m-Y', $fechaFinStr);

        $empleado_id = $request->input('empleado_id');

        // Buscar empleado y su horario
        $empleado = Persona::with(['horarios', 'biometricos', 'genero'])->find($empleado_id);
        if (!$empleado) {
            Toastr::error('Empleado no encontrado :(', 'Danger');
            return redirect()->back();
        }
        if (!$empleado->biometricos || $empleado->biometricos->isEmpty()) {
            Toastr::error('Este empleado no está asignado a ningún biométrico :(', 'Danger');
            return redirect()->back();
        }

        $empleados = Persona::with('horarios')->get();

        // Obtener marcaciones del empleado para el rango de fechas
        $marcaciones = collect();

        foreach ($empleado->biometricos as $biometrico) {
            try {
                $biometricoMarcaciones = DB::table($biometrico->nombre_base_de_datos)
                    ->where('ci', $empleado->ci)
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->select('*', DB::raw("'{$biometrico->nombre_base_de_datos}' as nombre_biometrico")) // Añadir nombre del biométrico
                    ->get();

                $marcaciones = $marcaciones->merge($biometricoMarcaciones);
            } catch (\Exception $e) {
                // Puedes loguear el error o notificar si una base falla
                //Log::error("Error consultando BD biométrica {$biometrico->nombre_base_de_datos}: " . $e->getMessage());
            }
        }

        // Procesar marcaciones por día
        $marcacionesProcesadas = $marcaciones->groupBy('fecha')->map(function ($marcacionesDia, $fecha) use ($empleado) {
            $marcacionesDia = $marcacionesDia->sortBy('hora');

            // Mapear las marcaciones con su origen biométrico
            $marcacionesTimestampsOriginal = $marcacionesDia->map(function ($marcacion) {
                return [
                    'id' => $marcacion->id,
                    'hora' => $marcacion->hora,
                    'timestamp' => strtotime($marcacion->hora),
                    'biometrico' => $marcacion->nombre_biometrico ?? 'Desconocido' // Asumiendo que este campo existe
                ];
            });

            $resto_horas = $marcacionesDia->map(function ($m) {
                return [
                    'hora' => $m->hora,
                    'id' => $m->id,
                    'biometrico' => $m->nombre_base_de_datos ?? 'Desconocido'
                ];
            });

            $mejorResultado = null;
            $mayorCoincidencias = 0;

            foreach ($empleado->horarios as $horario) {
                $marcacionesTimestamps = clone $marcacionesTimestampsOriginal;

                // Modificar las funciones para mantener el origen biométrico
                $getMarcacion = function (&$marcacionesTimestamps, $horaEsperada, $rango, $asc = true) {
                    if (!$horaEsperada) return null;

                    $timestamp = strtotime($horaEsperada);
                    $result = $marcacionesTimestamps
                        ->filter(fn($m) => $m['timestamp'] >= $timestamp + $rango[0] &&
                            $m['timestamp'] <= $timestamp + $rango[1])
                        ->sortBy('timestamp', SORT_REGULAR, !$asc);

                    $marcacion = $result->first();
                    if ($marcacion) {
                        $marcacionesTimestamps = $marcacionesTimestamps->reject(fn($m) => $m['id'] === $marcacion['id']);
                        return [
                            'id' => $marcacion['id'], // Incluir el ID en el resultado
                            'hora' => $marcacion['hora'],
                            'biometrico' => $marcacion['biometrico']
                        ];
                    }
                    return null;
                };

                // Obtener las marcaciones con su origen
                $entrada1 = $getMarcacion($marcacionesTimestamps, $horario->ingreso1, [-5400, 5400], true);
                $salida1 = $getMarcacion($marcacionesTimestamps, $horario->salida1, [-5400, 5400], false);
                $entrada2 = $getMarcacion($marcacionesTimestamps, $horario->ingreso2, [-5400, 5400], true);
                $salida2 = $getMarcacion($marcacionesTimestamps, $horario->salida2, [-5400, 19800], false);

                $coincidencias = collect([$entrada1, $salida1, $entrada2, $salida2])
                    ->filter()
                    ->count();

                if ($coincidencias > $mayorCoincidencias) {
                    $mejorResultado = [
                        'fecha' => Carbon::parse($fecha)->format('Y-m-d'),
                        'entrada_1' => $entrada1 ? $entrada1['hora'] : null,
                        'entrada_1_id' => $entrada1 ? $entrada1['id'] : null, // Incluir el ID
                        'entrada_1_biometrico' => $entrada1 ? $entrada1['biometrico'] : null,
                        'salida_1' => $salida1 ? $salida1['hora'] : null,
                        'salida_1_id' => $salida1 ? $salida1['id'] : null, // Incluir el ID
                        'salida_1_biometrico' => $salida1 ? $salida1['biometrico'] : null,
                        'entrada_2' => $entrada2 ? $entrada2['hora'] : null,
                        'entrada_2_id' => $entrada2 ? $entrada2['id'] : null, // Incluir el ID
                        'entrada_2_biometrico' => $entrada2 ? $entrada2['biometrico'] : null,
                        'salida_2' => $salida2 ? $salida2['hora'] : null,
                        'salida_2_id' => $salida2 ? $salida2['id'] : null, // Incluir el ID
                        'salida_2_biometrico' => $salida2 ? $salida2['biometrico'] : null,
                        'resto_de_entradas' => $resto_horas,
                    ];
                    $mayorCoincidencias = $coincidencias;
                }
            }

            // Buscar observaciones (mantenemos esta parte igual)
            $permiso = Permiso::where('fecha_permiso', $fecha)
                ->with('tipoPermiso')
                ->where(function ($query) use ($empleado) {
                    $query->where('id_persona', $empleado->id)
                        ->orWhere('solicitante_id', $empleado->id);
                })->first();

            $vacacion = Vacacion::where('fecha_inicio', $fecha)
                ->where(function ($query) use ($empleado) {
                    $query->where('id_persona', $empleado->id)
                        ->orWhere('solicitante_id', $empleado->id);
                })->first();

            $feriado = Feriado::where('fecha', $fecha)->with('tipoFeriado')->first();

            $observacion = $feriado
                ? ($feriado->tipoFeriado ? $feriado->tipoFeriado->descripcion . ' - ' . $feriado->descripcion : $feriado->descripcion)
                : ($permiso
                    ? ($permiso->tipoPermiso ? $permiso->tipoPermiso->descripcion : ($permiso->motivo ?: 'Permiso'))
                    : ($vacacion ? 'Vacación' : ''));

            if ($mejorResultado) {
                $mejorResultado['obs'] = $observacion;
            }

            return $mejorResultado;
        })->filter()->values();



        // Crear las fechas faltantes para completar el mes
        $fechasDelMes = collect();
        $fechaInicioTemp = clone $fechaInicio;
        while ($fechaInicioTemp->lte($fechaFin)) {
            $fechasDelMes->push($fechaInicioTemp->format('Y-m-d'));
            $fechaInicioTemp->addDay();
        }

        // Filtrar las fechas procesadas para obtener solo las fechas faltantes
        $fechasExistentes = $marcacionesProcesadas->pluck('fecha')->toArray();
        $fechasFaltantes = $fechasDelMes->filter(function ($fecha) use ($fechasExistentes) {
            return !in_array($fecha, $fechasExistentes);
        });

        // Añadir las fechas faltantes con datos nulos
        $marcacionesFaltantes = $fechasFaltantes->map(function ($fecha) use ($empleado) {

            $permiso = Permiso::where('fecha_permiso', $fecha)
                ->with(['tipoPermiso'])
                ->where(function ($query) use ($empleado) {
                    $query->where('id_persona', $empleado->id)
                        ->orWhere('solicitante_id', $empleado->id);
                })->first();
            // dd($permiso);

            $vacacion = Vacacion::where('fecha_inicio', $fecha)->where(function ($query) use ($empleado) {
                $query->where('id_persona', $empleado->id)
                    ->orWhere('solicitante_id', $empleado->id);
            })->first();

            $feriado = Feriado::where('fecha', $fecha)
                ->with(['tipoFeriado'])->first();


            return [
                'fecha' => $fecha,
                'entrada_1' => null,
                'salida_1' => null,
                'entrada_2' => null,
                'salida_2' => null,
                'resto_de_entradas' => [],
                'obs' => $feriado
                    ? ($feriado->tipoFeriado ? $feriado->tipoFeriado->descripcion . ' - ' . $feriado->descripcion : $feriado->descripcion)
                    : ($permiso
                        ? ($permiso->tipoPermiso ? $permiso->tipoPermiso->descripcion : ($permiso->motivo ?: 'Permiso'))
                        : ($vacacion
                            ? 'Vacación'
                            : '')
                    )


            ];
        });

        // Fusionar y ordenar las marcaciones
        $marcacionesFinales = $marcacionesProcesadas->merge($marcacionesFaltantes)->sortBy('fecha');

        $empleado->tipoContrato;
        $empleado->genero;

        $tiposContrato = TipoContrato::all();
        // dd($marcacionesFinales);
        return view('content.evaluacionAsistencia.evaluacion', [
            'empleado' => $empleado,
            'marcacionesFinales' => $marcacionesFinales,
            'empleados' => $empleados,
            'fechaInicio' => $fechaInicioStr,
            'fechaFin' => $fechaFinStr,
            'tiposContrato' => $tiposContrato
        ]);
    }



    public function update(Request $request, $id)
    {
        $empleado = Persona::with(['horarios', 'biometricos'])->findOrFail($request->empleado);

        // Validar que el empleado tenga biométricos configurados
        if ($empleado->biometricos->isEmpty()) {
            Toastr::error('El empleado no tiene biométricos configurados', 'Error');
            return back();
        }

        $ci = $empleado->ci;

        foreach ($request->marcaciones as $marcacion) {
            // Limpiar fecha (eliminar el día de la semana entre paréntesis)
            $fechaLimpia = explode(' ', $marcacion['fecha'])[0];
            $fecha = Carbon::createFromFormat('Y-m-d', $fechaLimpia)->format('Y-m-d');

            // Procesar cada tipo de marcación
            $tiposMarcacion = ['entrada_1', 'salida_1', 'entrada_2', 'salida_2'];

            foreach ($tiposMarcacion as $tipo) {
                // Solo procesar si hay hora definida
                if (!empty($marcacion[$tipo])) {
                    $idCampo = $tipo . '_id';
                    $biometricoCampo = $tipo . '_biometrico';

                    // Verificar si tenemos información del biométrico original
                    if (isset($marcacion[$biometricoCampo])) {
                        $biometricoOriginal = $marcacion[$biometricoCampo];

                        // Buscar si el biométrico original está entre los registrados
                        $biometricoActual = $empleado->biometricos->firstWhere('nombre_base_de_datos', $biometricoOriginal);

                        if ($biometricoActual) {
                            // Actualizar en el biométrico original
                            if (!empty($marcacion[$idCampo])) {
                                DB::table($biometricoOriginal)
                                    ->where('id', $marcacion[$idCampo])
                                    ->update([
                                        'hora' => $marcacion[$tipo],
                                    ]);
                            } else {
                                DB::table($biometricoOriginal)->insert([
                                    'fecha' => $fecha,
                                    'hora' => $marcacion[$tipo],
                                    'ci' => $ci,
                    
                                ]);
                            }
                        } else {
                            // Si no está registrado, usar el primer biométrico disponible
                            $biometricoPrimario = $empleado->biometricos->first()->nombre_base_de_datos;

                            // Eliminar del biométrico original si existe
                            if (!empty($marcacion[$idCampo])) {
                                DB::table($biometricoOriginal)
                                    ->where('id', $marcacion[$idCampo])
                                    ->delete();
                            }

                            // Insertar en el biométrico primario
                            DB::table($biometricoPrimario)->insert([
                                'fecha' => $fecha,
                                'hora' => $marcacion[$tipo],
                                'ci' => $ci,
                
                            ]);
                        }
                    } else {
                        // Si no hay información de biométrico, usar el primero disponible
                        $biometricoPrimario = $empleado->biometricos->first()->nombre_base_de_datos;

                        DB::table($biometricoPrimario)->insert([
                            'fecha' => $fecha,
                            'hora' => $marcacion[$tipo],
                            'ci' => $ci,
                        ]);
                    }
                }
            }
        }

        Toastr::success('Marcaciones actualizadas correctamente', 'Éxito');
        return back();
    }
}
