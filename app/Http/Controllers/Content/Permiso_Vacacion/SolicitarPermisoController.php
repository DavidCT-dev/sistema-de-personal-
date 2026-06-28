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
use App\Models\Permiso;
use App\Models\MotivoPermiso;

class SolicitarPermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        //dd( $user);
$permisos = Permiso::with(['tipoPermiso'])
    ->where(function ($query) use ($user) {
        $query->where('solicitante_id', $user->persona_id)
              ->orWhere('id_persona', $user->persona_id);
    })
    ->get();
        //dd($permisos);
        $motivoPermisos  = MotivoPermiso::all();
        return view('content.permiso-vacacion.permiso',compact('permisos','motivoPermisos'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Crear un nuevo permiso con los datos validados
            $permiso = new Permiso();
            $permiso->fecha_permiso = $request['fecha_permiso'];
            $permiso->solicitante_id = $user->id;
            $permiso->motivo_permiso_id = $request['motivo'];
            $permiso->duracion_permiso = $request['duracion_permiso'];

            // Manejar el caso de medio día
            if ($request['duracion_permiso'] === 'medio_dia') {
                $permiso->hora_inicio = $request['hora_inicio'];
                $permiso->hora_fin = $request['hora_fin'];
                $permiso->dias_permiso_total = 0.5; // Un solo día

                $permiso->fecha_fin_varios_dias = null; // No aplica para medio día
            }

            // Manejar el caso de varios días
            if ($request['duracion_permiso'] === 'varios_dias') {
                $permiso->fecha_fin_varios_dias = $request['fecha_fin_varios_dias'];
                $permiso->hora_inicio = null; // No aplica para varios días
                $permiso->hora_fin = null; // No aplica para varios días
            
                // Calcular la cantidad de días de permiso excluyendo fines de semana
                $inicio = new \DateTime($request['fecha_permiso']);
                $fin = new \DateTime($request['fecha_fin_varios_dias']);
                $fin->modify('+1 day'); // Para incluir el último día en el cálculo
            
                $interval = new \DateInterval('P1D');
                $periodo = new \DatePeriod($inicio, $interval, $fin);
            
                $diasLaborales = 0;
            
                foreach ($periodo as $fecha) {
                    $diaSemana = $fecha->format('N'); // 1 (lunes) a 7 (domingo)
                    if ($diaSemana < 6) { // Si no es sábado (6) ni domingo (7)
                        $diasLaborales++;
                    }
                }
            
                $permiso->dias_permiso_total = $diasLaborales;
            }

            // Manejar el caso de día completo
            if ($request['duracion_permiso'] === 'dia_completo') {
                $permiso->fecha_fin_varios_dias = null; // No aplica para día completo
                $permiso->hora_inicio = null; // No aplica para día completo
                $permiso->hora_fin = null; // No aplica para día completo
                $permiso->dias_permiso_total = 1; // Un solo día
            }



            $motivoiPermiso = MotivoPermiso::findOrFail($request['motivo']);

            if(preg_match('/VACACION/i', $motivoiPermiso->descripcion)){
                $persona = Persona::findOrFail($user->id);

            if ($persona->total_dias_vacacion < $permiso->dias_permiso_total) {
                DB::rollback();
                Toastr::error('No cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
                return redirect()->route('solicitar-permisos.index');
            }       
            $persona->total_dias_vacacion -= $permiso->dias_permiso_total;

            // // Actualizar días disponibles
            $persona->save();
            }



            // Guardar el permiso en la base de datos
            $permiso->save();

            // Confirmar la transacción
            DB::commit();

            // Redirigir con mensaje de éxito
            Toastr::success('Permiso Solicitado correctamente :)', 'Success');
            return redirect()->route('solicitar-permisos.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: ' . $e->getMessage(), 'Error');

            // Redirigir a la lista de permisos
            return redirect()->route('solicitar-permisos.index');
        }
    }
    
    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);

        $permiso->update([
            'estado' => 'cancelado',
            'aprobado_en' => now()
        ]);

        Toastr::success('Permiso cancelado correctamente :)', 'Success');
        return redirect()->route('solicitar-permisos.index');
    }

    
}
