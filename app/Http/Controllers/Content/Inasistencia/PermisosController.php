<?php

namespace App\Http\Controllers\Content\Inasistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Toastr;
use Exception;
use App\Models\Persona;
use App\Models\Permiso;
use App\Models\MotivoPermiso;

use App\Http\Requests\PermisoRequest;
use Illuminate\Pagination\Paginator;

class PermisosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request)
{
    $query = Permiso::with(['empleado.tipoContrato', 'empleado', 'solicitante.persona', 'tipoPermiso']);

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('empleado', function($q) use ($search) {
            $q->where('ci', 'like', "%{$search}%");
        });
    }

    $permisos = $query->paginate(25)->appends($request->except('page'));

    $empleados = Persona::all();
    $motivoPermisos = MotivoPermiso::all();

    return view('content.inasistencia.permisos', compact('permisos', 'empleados', 'motivoPermisos'));
}



    public function store(PermisoRequest $request)
    {
        DB::beginTransaction();
        try {
            // Crear un nuevo permiso con los datos validados
            $permiso = new Permiso();
            $permiso->fecha_permiso = $request['fecha_permiso'];
            $permiso->id_persona = $request['id_persona'];
            $permiso->motivo_permiso_id = $request['motivo'];
            $permiso->observacion = $request['observacion'];
            $permiso->duracion_permiso = $request['duracion_permiso'];
            $permiso->estado = 'aprobado';

            // Manejar el caso de medio día
            if ($request['duracion_permiso'] === 'medio_dia') {
                $permiso->hora_inicio = $request['hora_inicio'];
                $permiso->hora_fin = $request['hora_fin'];
                $permiso->fecha_fin_varios_dias = null; // No aplica para medio día
                $permiso->dias_permiso_total = 0.5;
            }

            // Manejar el caso de varios días
            if ($request['duracion_permiso'] === 'varios_dias') {
                $permiso->fecha_fin_varios_dias = $request['fecha_fin_varios_dias'];
                $permiso->hora_inicio = null; // No aplica para varios días
                $permiso->hora_fin = null; // No aplica para varios días

                

                $inicio = new \DateTime($request->fecha_permiso);
                $fin = new \DateTime($request->fecha_fin_varios_dias);
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
                $persona = Persona::findOrFail($request['id_persona']);

            if ($persona->total_dias_vacacion < $permiso->dias_permiso_total) {
                DB::rollback();
                Toastr::error('El empleado no cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
                return redirect()->route('permisos.index');
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
            Toastr::success('Permiso creado correctamente :)', 'Success');
            return redirect()->route('permisos.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: ' . $e->getMessage(), 'Error');

            // Redirigir a la lista de permisos
            return redirect()->route('permisos.index');
        }
    }


    public function update(PermisoRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el permiso por su ID
            $permiso = Permiso::findOrFail($id);

            // Actualizar los campos básicos del permiso con los datos validados
            $permiso->fecha_permiso = $request['fecha_permiso'];
            $permiso->id_persona = $request['id_persona'];
            $permiso->motivo_permiso_id = $request['motivo'];
            $permiso->observacion = $request['observacion'];
            $permiso->duracion_permiso = $request['duracion_permiso'];

            // Manejar el caso de medio día
            if ($request['duracion_permiso'] === 'medio_dia') {
                $permiso->hora_inicio = $request['hora_inicio'];
                $permiso->hora_fin = $request['hora_fin'];
                $permiso->fecha_fin_varios_dias = null; // No aplica para medio día
                $permiso->dias_permiso_total = 0.5;
            }

            // Manejar el caso de varios días
            if ($request['duracion_permiso'] === 'varios_dias') {
                $permiso->fecha_fin_varios_dias = $request['fecha_fin_varios_dias'];
                $permiso->hora_inicio = null; // No aplica para varios días
                $permiso->hora_fin = null; // No aplica para varios días

                // Calcular la cantidad de días de permiso
                // $inicio = new \DateTime($request['fecha_permiso']);
                // $fin = new \DateTime($request['fecha_fin_varios_dias']);
                // $interval = $inicio->diff($fin);

                $inicio = new \DateTime($request->fecha_permiso);
                $fin = new \DateTime($request->fecha_fin_varios_dias);
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

                
                $motivo = MotivoPermiso::findOrFail($request['motivo']);

                if ($permiso->estado == 'aprobado' && preg_match('/vacacion/i', $motivo->descripcion)) {
                    if ($permiso->id_persona) {
                        $persona = Persona::findOrFail($permiso->id_persona);
                    } else {
                        $persona = Persona::findOrFail($permiso->solicitante_id);
                    }

                    $persona->total_dias_vacacion += $permiso->dias_permiso_total;
                    $persona->total_dias_vacacion -= $diasLaborales;

                    $persona->save();
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

            // Guardar los cambios en la base de datos
            $permiso->save();

            // Confirmar la transacción
            DB::commit();

            // Redirigir con mensaje de éxito
            Toastr::success('Permiso actualizado correctamente :)', 'Success');
            return redirect()->route('permisos.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: no se puedo acttualizar el permiso', 'Error');

            // Redirigir a la lista de permisos
            return redirect()->route('permisos.index');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el permiso por su ID
            $permiso = Permiso::findOrFail($request->permisoId);

            if (!$permiso) {
                Toastr::error('Error: No existe permiso. ', 'Error');
            }
            // Eliminar el permiso
            $permiso->delete();

            // Confirmar la transacción
            DB::commit();

            // Redirigir con mensaje de éxito
            Toastr::success('Permiso eliminado correctamente :)', 'Success');
            return redirect()->route('permisos.index');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();

            // Mostrar mensaje de error
            Toastr::error('Error: No se pudo eliminar el permiso. ', 'Error');

            // Redirigir a la lista de permisos
            return redirect()->route('permisos.index');
        }
    }

    public function approve(Request $request, $id)
    {

        $permiso = Permiso::with('tipoPermiso')->findOrFail($id);

        if (preg_match('/vacacion/i', $permiso->tipoPermiso->descripcion)) {

            if ($permiso->id_persona) {
                $persona = Persona::findOrFail($permiso->id_persona);
            } else {
                $persona = Persona::findOrFail($permiso->solicitante_id);
            }


            // if ($persona->total_dias_vacacion < $permiso->dias_permiso_total) {
            //     DB::rollback();
            //     Toastr::error('El empleado no cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
            //     return redirect()->route('permisos.index');
            // }       

            // // Actualizar días disponibles
            $persona->total_dias_vacacion -= $permiso->dias_permiso_total;
            $persona->save();
        }

        $permiso->update([
            'estado' => 'aprobado',
            'aprobado_por_id' => auth()->id(),
            'aprobado_en' => now(),
            // 'observacion' => null
        ]);

        Toastr::success('Permiso ha sido confirmado con exito :)', 'Success');
        return redirect()->route('permisos.index');
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

        $permiso = Permiso::with('tipoPermiso')->findOrFail($id);

        if (preg_match('/vacacion/i', $permiso->tipoPermiso->descripcion) && $permiso->estado == 'aprobado') {

            if ($permiso->id_persona) {
                $persona = Persona::findOrFail($permiso->id_persona);
            } else {
                $persona = Persona::findOrFail($permiso->solicitante_id);
            }


            // if ($persona->total_dias_vacacion < $permiso->dias_permiso_total) {
            //     DB::rollback();
            //     Toastr::error('El empleado no cuenta con suficientes días de vacación. Días disponibles: '. $persona->total_dias_vacacion, 'Error');
            //     return redirect()->route('permisos.index');
            // }       

            // // Actualizar días disponibles
            $persona->total_dias_vacacion += $permiso->dias_permiso_total;
            $persona->save();
        }



        // Validar que el permiso esté pendiente


        $permiso->update([
            'estado' => 'rechazado',
            'aprobado_por_id' => auth()->id(),
            'aprobado_en' => now(),
            'observacion' => trim($request['motivo_rechazo']) ??  $permiso->observacion // Opcional: guardar también en observación
        ]);

        Toastr::success('Permiso rechazado', 'Success');
        return redirect()->route('permisos.index');
    }
}
