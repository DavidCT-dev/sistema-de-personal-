<?php

namespace App\Http\Controllers\Content\Inasistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feriado;
use App\Models\MotivoFeriado;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Toastr;
use Exception;
use App\Http\Requests\FeriadoRequest;

class FeriadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feriados = Feriado::orderBy('fecha', 'asc')->with(['tipoFeriado'])->get();
        $motivoFeriados = MotivoFeriado::all();
        return view('content.inasistencia.feriados', compact('feriados','motivoFeriados'));
    }



   public function store(FeriadoRequest $request)
{
    DB::beginTransaction();
    try {
        // Parse the date format from 'dd-mm-yyyy' to Carbon instance
        $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
        
        Feriado::create([
            'fecha' => $fecha,
            'descripcion' => $request->descripcion,
            'observacion' => $request->observacion,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'motivo_feriado_id' => $request->tipo,
            'sexo' => $request->sexo
        ]);

        DB::commit();

        Toastr::success('Feriado creado correctamente :)', 'Success');
        return redirect()->route('feriados.index');
        
    } catch (\Exception $e) {
        DB::rollback();
                
        Toastr::error('Error: No se pudo crear el feriado', 'Error');
        return redirect()->route('feriados.index');
    }
}



    public function update(FeriadoRequest $request, $id)
    {
        // Iniciar transacción
        DB::beginTransaction();
        try {
            // Buscar el feriado por ID
            $feriado = Feriado::findOrFail($id);

            // Actualizar los datos del feriado
            $feriado->update([
                'fecha' => Carbon::parse($request->fecha),
                'descripcion' => $request->descripcion,
                'observacion' => $request->observacion,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'motivo_feriado_id' => $request->tipo,
                'sexo' => $request->sexo,
            ]);

            // Confirmar la transacción
            DB::commit();

            // Mensaje de éxito
            Toastr::success('Feriado actualizado correctamente :)', 'Success');

            // Redirigir a la lista de feriados
            return redirect()->route('feriados.index');
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: ' . $e->getMessage(), 'Error');

            // Redirigir a la lista de feriados
            return redirect()->route('feriados.index');
        }
    }


    public function destroy(Request $request, $id)
    {

        // Iniciar la transacción de la base de datos
        DB::beginTransaction();

        try {
            // Buscar al empleado por su ID
            $feriado = Feriado::find($request->feriadoId);

            // Si no se encuentra el empleado, lanzar un error
            if (!$feriado) {
                Toastr::error('Error: No existe feriado. ', 'Error');
            }

            // Eliminar de forma lógica (Soft Delete)

            // Confirmar la transacción



            $feriado->delete();

            DB::commit();

            Toastr::success('Feriado eliminado correctamente :)', 'Success');


            return redirect()->route('feriados.index');
        } catch (Exception $e) {
            // Si ocurre un error, revertir la transacción
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error al eliminar', 'Error');

            // Redirigir a la lista de empleados
            return redirect()->route('feriados.index');
        }
    }





    public function crearFeriados()
    {
        $anioActual = now()->year;

        // Verificar si ya existen feriados para el año actual
    $feriadosExistentes = Feriado::whereYear('fecha', $anioActual)->exists();

    if ($feriadosExistentes) {
        Toastr::error('Los feriados para el año ' . $anioActual . ' ya han sido creados.', 'Error');
        return redirect()->route('feriados.index');
    }

        // ✅ FERIADOS FIJOS
        $feriados = [
            ['nombre' => 'Año Nuevo', 'fecha' => Carbon::create($anioActual, 1, 1)],
            ['nombre' => 'Día del Trabajo', 'fecha' => Carbon::create($anioActual, 5, 1)],
            ['nombre' => 'Navidad', 'fecha' => Carbon::create($anioActual, 12, 25)],
            ['nombre' => 'Día de la Independencia', 'fecha' => Carbon::create($anioActual, 8, 6)],
            ['nombre' => 'Día de la Creación del Estado Plurinacional de Bolivia', 'fecha' => Carbon::create($anioActual, 1, 22)],
            [
                'nombre' => 'Día del Padre', 
                'fecha' => Carbon::create($anioActual, 3, 19),
                'hora_inicio' => '14:00',
                'hora_fin' => '18:30',
            ],
            [
                'nombre' => 'Día de la Madre', 
                'fecha' => Carbon::create($anioActual, 5, 27),
                'hora_inicio' => '14:00',
                'hora_fin' => '18:30',
            ],
            ['nombre' => 'Día del Maestro', 'fecha' => Carbon::create($anioActual, 6, 6)],
            ['nombre' => 'Año Nuevo Aymara', 'fecha' => Carbon::create($anioActual, 6, 21)],
            ['nombre' => 'Día del Estudiante', 'fecha' => Carbon::create($anioActual, 9, 21)],
            [
                'nombre' => 'Día de la Mujer Boliviana', 
                'fecha' => Carbon::create($anioActual, 10, 11),
                'hora_inicio' => '14:00',
                'hora_fin' => '18:30',
            ],
            ['nombre' => 'Todos los Santos', 'fecha' => Carbon::create($anioActual, 11, 1)],
            
            ['nombre' => 'Día del Docente Universitario', 'fecha' => Carbon::create($anioActual, 6, 6)],
            ['nombre' => 'Día del Trabajador Universitario', 'fecha' => Carbon::create($anioActual, 7, 18)],
            ['nombre' => 'Día de la Autonomía  Universitario', 'fecha' => Carbon::create($anioActual, 7, 25)],
            ['nombre' => 'Fundación de la U.A.T.F.', 'fecha' => Carbon::create($anioActual, 10, 15)],
            ['nombre' => 'Aniversario de Potosí', 'fecha' => Carbon::create($anioActual, 11, 10)],
            
        ];

        // ✅ FERIADOS MÓVILES
        $pascua = $this->calcularPascua($anioActual);
        $feriadosMoviles = [
            ['nombre' => 'Carnaval Lunes', 'fecha' => $pascua->copy()->subDays(48)],
            ['nombre' => 'Carnaval Martes', 'fecha' => $pascua->copy()->subDays(47)],
            // ['nombre' => 'Jueves Santo', 'fecha' => $pascua->copy()->subDays(3)],
            ['nombre' => 'Viernes Santo', 'fecha' => $pascua->copy()->subDays(2)],
            ['nombre' => 'Corpus Christi', 'fecha' => $pascua->copy()->addDays(60)],
        ];

        // ✅ Insertar en la base de datos evitando duplicados
        foreach (array_merge($feriados, $feriadosMoviles) as $feriado) {
            // Determinar el género según el nombre del feriado
            $nombreFeriado = strtolower($feriado['nombre']); // Convertir a minúsculas para facilitar la comparación
            $sexo = 'T'; // Por defecto, todos los feriados son para "Todos"
        
            if (strpos($nombreFeriado, 'día del padre') !== false) {
                $sexo = 'M'; // Masculino para el Día del Padre
            } elseif (strpos($nombreFeriado, 'día de la madre') !== false) {
                $sexo = 'F'; // Femenino para el Día de la Madre
            } elseif (strpos($nombreFeriado, 'día de la mujer') !== false) {
                $sexo = 'F'; // Femenino para el Día de la Mujer Boliviana
            }
        
            // Crear o actualizar el registro de feriado
            Feriado::updateOrCreate(
                ['fecha' => $feriado['fecha']], // Evita duplicados usando la fecha como clave única
                [
                    'descripcion' => $feriado['nombre'],
                    'observacion' => 'Feriado oficial',
                    'hora_inicio' => $feriado['hora_inicio'] ?? '00:00',
                    'hora_fin' => $feriado['hora_fin'] ?? '23:59',
                    'motivo_feriado_id' => '1',
                    'sexo' => $sexo, // Asignar el género determinado
                ]
            );
        }

        Toastr::success('Feriados creados correctamente para el año '. $anioActual, 'Success');
        return redirect()->route('feriados.index');
    }

    /**
     * 📌 Calcular la fecha de Pascua (algoritmo de Meeus/Jones/Butcher)
     */
    private function calcularPascua($year)
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = ($h + $l - 7 * $m + 114) % 31 + 1;

        return Carbon::create($year, $mes, $dia);
    }
}
