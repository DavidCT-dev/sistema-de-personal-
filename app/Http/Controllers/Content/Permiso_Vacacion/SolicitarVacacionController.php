<?php

namespace App\Http\Controllers\Content\Permiso_Vacacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cargos;
use App\Models\Genero;
use App\Models\Horario;
use App\Models\LugarTrabajo;
use App\Models\TipoContrato;
use App\Models\Biometrico;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use Carbon\Carbon;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\RequestEmpleado;
use App\Models\Vacacion;


class SolicitarVacacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();


        $vacaciones = Vacacion::where('solicitante_id',$user->id)->get();

        // dd($vacaciones);
        return view('content.permiso-vacacion.vacacion',compact('vacaciones'));
    }

    public function store(Request $request)
    {

        if(!$request->fecha_inicio || !$request->fecha_fin ){
            Toastr::error('Ingrese las fechas solicitadas. :(', 'Error');
            return redirect()->route('solicitar-vacacion.index');
        }
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $vacacion = new Vacacion();


            $vacacion->fecha_inicio = Carbon::parse($request->fecha_inicio);
            $vacacion->fecha_fin = Carbon::parse($request->fecha_fin);

            $vacacion->solicitante_id = $user->id; // Registrar quién hizo la solicitud
            $vacacion->estado = 'pendiente'; // Estado inicial
            

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
            
            $vacacion->total_dias = $diasLaborales;
            


            $persona = Persona::findOrFail($user->id);

            if ($persona->total_dias_vacacion < $diasLaborales) {
                DB::rollback();
                Toastr::error('No cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
                return redirect()->route('solicitar-vacacion.index');
            }       
            $persona->total_dias_vacacion -= $diasLaborales;

            // // Actualizar días disponibles
            $persona->save();

            // Guardar los cambios
            $vacacion->save();



        DB::commit();

        Toastr::success('Solicitud de vacacion creada exitosamente. :)', 'Success');
        return redirect()->route('solicitar-vacacion.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: ' . $e->getMessage(), 'Error');

            // Redirigir a la lista de permisos
            return redirect()->route('solicitar-vacacion.index');
        }
    }
    


    public function update(Request $request, $id)
    {
        
        $vacacion = Vacacion::findOrFail($id);

        $vacacion->update([
            'estado' => 'cancelado',
            'aprobado_en' => now()
        ]);

        Toastr::success('Vacación cancelada correctamente :)', 'Success');
        return redirect()->route('solicitar-vacacion.index');

    }

    
}
