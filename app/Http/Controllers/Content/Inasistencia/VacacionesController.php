<?php

namespace App\Http\Controllers\Content\Inasistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Toastr;
use Exception;
use App\Models\Persona;
use App\Models\Vacacion;
use App\Http\Requests\VacacionRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Imports\CIsImport;

class VacacionesController extends Controller
{
    public function __construct()
    {
        // Solo pueden acceder los usuarios con tipo de contrato PERMANENTE
        $this->middleware('can:solicitar-vacacion')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vacaciones = Vacacion::with(['empleado.lugarTrabajo', 'empleado.cargo', 'solicitante.persona',])->get();
        // Solo personas cuyo tipo de contrato tenga descripcion "PERMANENTE"
        $empleados = Persona::all();
        // dd($empleados->count());
        return view('content.inasistencia.vacaciones', compact('vacaciones', 'empleados'));
    }


    public function store(VacacionRequest $request)
    {
        DB::beginTransaction();
        try {
            // Crear la vacación con los datos básicos
            $vacacion = new Vacacion();
            $vacacion->fecha_inicio = Carbon::parse($request->fecha_inicio);
            $vacacion->fecha_fin = Carbon::parse($request->fecha_fin);
            $vacacion->id_persona = $request->id_persona;
            $vacacion->observacion = $request->observacion ?? null;
            $vacacion->estado = 'aprobado';




            $inicio = new \DateTime($request->fecha_inicio);
            $fin = new \DateTime($request->fecha_fin);
            $fin->modify('+1 day'); // Incluir el día final en el cálculo

            $interval = new \DateInterval('P1D');
            $periodo = new \DatePeriod($inicio, $interval, $fin);

            $diasLaborales = 0;

            foreach ($periodo as $fecha) {
                $diaSemana = $fecha->format('N'); // 1 (lunes) - 7 (domingo)
                if ($diaSemana < 6) { // Si no es sábado (6) ni domingo (7)
                    $diasLaborales++;
                }
            }


            
            $persona = Persona::findOrFail($request->id_persona);

            if ($persona->total_dias_vacacion < $diasLaborales) {
                DB::rollback();
                Toastr::error('El empleado no cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
                return redirect()->route('vacaciones.index');
            }       
            $persona->total_dias_vacacion -= $diasLaborales;

            // // Actualizar días disponibles
            $persona->save();
        


            $vacacion->total_dias = $diasLaborales;

            // Guardar los cambios
            $vacacion->save();

            DB::commit();

            Toastr::success('Vacación creada exitosamente :)', 'Success');
            return redirect()->route('vacaciones.index');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear la vacación: ' . $e->getMessage(), 'Error');
            return redirect()->route('vacaciones.index');
        }
    }

    public function update(VacacionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar la vacación por su ID
            $vacacion = Vacacion::findOrFail($id);


            // Actualizar campos básicos
            $vacacion->fecha_inicio = Carbon::parse($request->fecha_inicio);
            $vacacion->fecha_fin = Carbon::parse($request->fecha_fin);

            $vacacion->id_persona = $request->id_persona;
            $vacacion->observacion = $request->observacion;



            $inicio = new \DateTime($request->fecha_inicio);
            $fin = new \DateTime($request->fecha_fin);
            $fin->modify('+1 day'); // Incluir el día final en el cálculo

            $interval = new \DateInterval('P1D');
            $periodo = new \DatePeriod($inicio, $interval, $fin);

            $diasLaborales = 0;

            foreach ($periodo as $fecha) {
                $diaSemana = $fecha->format('N'); // 1 (lunes) - 7 (domingo)
                if ($diaSemana < 6) { // Si no es sábado (6) ni domingo (7)
                    $diasLaborales++;
                }
            }

            if ($vacacion->estado == 'aprobado') {
                if ($vacacion->id_persona) {
                    $persona = Persona::findOrFail($vacacion->id_persona);
                } else {
                    $persona = Persona::findOrFail($vacacion->solicitante_id);
                }

                $persona->total_dias_vacacion += $vacacion->total_dias;
                $persona->total_dias_vacacion -= $diasLaborales;

                $persona->save();
            }

            $vacacion->total_dias = $diasLaborales;


            // Guardar cambios
            $vacacion->save();

            DB::commit();

            Toastr::success('Vacación actualizada exitosamente :)', 'Success');
            return redirect()->route('vacaciones.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            Toastr::error('Error al actualizar la vacación: ' . $e->getMessage(), 'Error');
            return redirect()->route('vacaciones.index');
        }
    }


    public function destroy(Request $request, $id)
    {
        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Buscar la vacación por su ID
            $vacacion = Vacacion::findOrFail($request->vacacionId);

            // Verificar si la vacación existe
            if (!$vacacion) {
                Toastr::error('Error: No se encontró la vacación.', 'Error');
                return redirect()->route('vacaciones.index');
            }

            // Eliminar la vacación
            $vacacion->delete();

            // Confirmar la transacción
            DB::commit();

            // Mostrar mensaje de éxito
            Toastr::success('Vacación eliminada correctamente.', 'Success');
            return redirect()->route('vacaciones.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Mostrar mensaje de error
            Toastr::error('Error: No se pudo eliminar la vacación. ' . $e->getMessage(), 'Error');

            // Redirigir a la lista de vacaciones
            return redirect()->route('vacaciones.index');
        }
    }


    public function approve(Request $request, $id)
    {

        $vacacion = Vacacion::findOrFail($id);

        $vacacion->estado = 'aprobado';
        $vacacion->aprobado_por_id = auth()->id();
        $vacacion->aprobado_en = now();
        $vacacion->observacion = null;



        if ($vacacion->id_persona) {
            $persona = Persona::findOrFail($vacacion->id_persona);
        } else {
            $persona = Persona::findOrFail($vacacion->solicitante_id);
        }


        // Validar días disponibles
        // if ($persona->total_dias_vacacion < $vacacion->total_dias) {
        //     DB::rollback();
        //     Toastr::error('El empleado no cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
        //     return redirect()->route('vacaciones.index');
        // }       

        // // Actualizar días disponibles
        $persona->total_dias_vacacion -= $vacacion->total_dias;
        $persona->save();
        $vacacion->save();


        Toastr::success('Permiso ha sido confirmado con exito :)', 'Success');
        return redirect()->route('vacaciones.index');
    }

    public function reject(Request $request, $id)
    {

        $request->validate([
            'motivo_rechazo' => 'nullable|string|max:500|regex:/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/'
        ], [
            'motivo_rechazo.string' => 'El motivo debe ser un texto válido.',
            'motivo_rechazo.max' => 'El motivo no puede superar los 500 caracteres.',
            'motivo_rechazo.regex' => 'El motivo debe contener texto válido (al menos una letra).'
        ]);

        $vacacion = Vacacion::findOrFail($id);



        // Validar que el permiso esté pendiente


        $vacacion->update([
            'estado' => 'rechazado',
            'aprobado_por_id' => auth()->id(),
            'aprobado_en' => now(),
            'observacion' => $request['motivo_rechazo'] // Opcional: guardar también en observación
        ]);

        Toastr::success('Permiso rechazado', 'Success');
        return redirect()->route('vacaciones.index');
    }



public function CargarArchivoExcel(Request $request)
{
    dd($request);
    try {
        if (!$request->has('employee_data')) {
            throw new \Exception('No se recibieron datos del empleado');
        }

        $employeeData = json_decode($request->input('employee_data'), true);

        // Validate employeeData structure before proceeding.
        // if (!isset($employeeData['ci']) || !isset($employeeData['vacaciones']) || !is_array($employeeData['vacaciones'])) {
        //     throw new \Exception('Formato de datos inválido.  Debe incluir "ci" y un array "vacaciones".');
        // }

        $user = auth()->user();
        $persona = Persona::where('ci', $employeeData['ci'])->first();

        if (!$persona) {
            Toastr::error('Persona no encontrada en el sistema: ' . $employeeData['ci'], 'Error'); 
        }

        foreach ($employeeData['vacaciones'] as $vacacionData) {
            $vacacion = new Vacacion();
            $vacacion->fecha_inicio = $vacacionData['inicio'];
            $vacacion->fecha_fin = $vacacionData['fin'];
            $vacacion->id_persona = $persona->id;
            $vacacion->observacion = 'Importado';
            $vacacion->estado = ($vacacionData['estado'] == 'Autorizada') ? 'aprobado' : (($vacacionData['estado'] == 'Denegada') ? 'rechazado' : 'pendiente'); 
            $vacacion->total_dias = $vacacionData['dias'];
            $vacacion->aprobado_por_id = $user->id;
            $vacacion->save(); 
        }

        Toastr::success('Datos procesados correctamente', 'Éxito');
        return back();
    } catch (\Exception $e) {
        Toastr::error('Error: ' . $e->getMessage(), 'Error');
        return back()->withInput();
    }
}
}
