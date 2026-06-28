<?php

namespace App\Http\Controllers\Content\opciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Carbon\Carbon;
use App\Models\LugarTrabajo;
use App\Models\Cargos;
use App\Models\TipoContrato;
use App\Models\MotivoPermiso;
use App\Models\MotivoFeriado;

class OpcionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Paginamos los datos con un tamaño de página (por ejemplo, 10 elementos por página)
        $tiposContrato = TipoContrato::all();
        $lugaresTrabajo = LugarTrabajo::all();
        $cargos = Cargos::all();
        $motivoPermisos = MotivoPermiso::all();
        $motivoFeriados = MotivoFeriado::all();

        // Enviamos los datos paginados a la vista
        return view('content.opciones.index', [
            'tiposContrato' => $tiposContrato,
            'lugaresTrabajo' => $lugaresTrabajo,
            'cargos' => $cargos,
            'motivoPermisos' => $motivoPermisos,
            'motivoFeriados' => $motivoFeriados,
        ]);
    }



    public function storeTipoContrato(Request $request)
    {
        $request->validate([
            'tipo_contrato' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Lógica para guardar el tipo de contrato
            $tipoContrato = new TipoContrato;
            $tipoContrato->descripcion = $request->tipo_contrato;
            $tipoContrato->save();

            DB::commit();
            Toastr::success('Tipo de contrato creado con éxito.', 'Éxito');


            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear tipo de contrato.', 'Error');
            return redirect()->back();
        }
    }

    public function updateTipoContrato(Request $request, $id)
    {
        $request->validate([
            'tipo_contrato' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Buscar el tipo de contrato por su ID
            $tipoContrato = TipoContrato::findOrFail($id);

            // Actualizar la descripción del tipo de contrato
            $tipoContrato->descripcion = $request->tipo_contrato;
            $tipoContrato->save();

            DB::commit();

            // Mensaje de éxito
            Toastr::success('Tipo de contrato actualizado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al actualizar el tipo de contrato: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function deleteTipoContrato(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tipo de contrato por su ID
            $tipoContrato = TipoContrato::where('id',$request->tipoContratoId)->first();
            // Eliminar el tipo de contrato
            $tipoContrato->delete();

            DB::commit();

            // Mensaje de éxito
            Toastr::success('Tipo de contrato eliminado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al eliminar el tipo de contrato', 'Error');
            return redirect()->back();
        }
    }



    public function storeLugarTrabajo(Request $request)
    {
        $request->validate([
            'lugar_trabajo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Lógica para guardar el lugar de trabajo
            $lugarTrabajo = new LugarTrabajo;
            $lugarTrabajo->descripcion = $request->lugar_trabajo;
            $lugarTrabajo->save();

            DB::commit();
            Toastr::success('Lugar de trabajo creado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear lugar de trabajo.', 'Error');
            return redirect()->back();
        }
    }

    public function updateLugarTrabajo(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'lugar_trabajo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $lugarTrabajo = LugarTrabajo::findOrFail($id);
            $lugarTrabajo->descripcion = $request->lugar_trabajo;

            // Guardar los cambios
            $lugarTrabajo->save();

            DB::commit();
            Toastr::success('Lugar de trabajo actualizado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al guardar el lugar de trabajo: ', 'Error');
            return redirect()->back();
        }
    }

    public function deleteLugarTrabajo(Request $request, $id)
    {
        
        DB::beginTransaction();
        try {
            // Buscar el tipo de contrato por su ID
            $lugarTrabajo = LugarTrabajo::where('id',$request->lugarTrabajoId)->first();
            // dd($lugarTrabajo);
            // Eliminar el tipo de contrato

            $lugarTrabajo->delete();

            DB::commit();

            // Mensaje de éxito
            Toastr::success('Lugar de Trabajo eliminado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al eliminar el Lugar de Trabajo' , 'Error');
            return redirect()->back();
        }
    }





    public function storeCargos(Request $request)
    {
        $request->validate([
            'cargo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Lógica para guardar el cargo
            $cargo = new Cargos;
            $cargo->descripcion = $request->cargo;
            $cargo->save();

            DB::commit();
            Toastr::success('Cargo creado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear cargo.', 'Error');
            return redirect()->back();
        }
    }

    public function updateCargo(Request $request, $id)
    {
        
        // Validar los datos del formulario
        $request->validate([
            'cargo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $cargo = Cargos::findOrFail($id);
            $cargo->descripcion = $request->cargo;

            // Guardar los cambios
            $cargo->save();

            DB::commit();
            Toastr::success('Cargo actualizado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al guardar el Cargo: ', 'Error');
            return redirect()->back();
        }
    }

    public function deleteCargo(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tipo de contrato por su ID
            $cargo = Cargos::where('id',$request->cargoId)->first();

            // Eliminar el tipo de contrato
            $cargo->delete();

            DB::commit();

            // Mensaje de éxito
            Toastr::success('Cargo eliminado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al eliminar el cargo', 'Error');
            return redirect()->back();
        }
    }


    public function storeMotivoPermiso(Request $request)
    {
        $request->validate([
            'motivo_permiso' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Lógica para guardar el cargo
            $motivoPermiso = new MotivoPermiso;
            $motivoPermiso->descripcion = $request->motivo_permiso;
            $motivoPermiso->save();

            DB::commit();
            Toastr::success('Motivo de permiso creado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear cargo.', 'Error');
            return redirect()->back();
        }
    }

    public function updateMotivoPermiso(Request $request, $id)
    {
        
        // dd($id);
        // Validar los datos del formulario
        $request->validate([
            'motivo_permiso' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $motivoPermiso = MotivoPermiso::findOrFail($id);
            $motivoPermiso->descripcion = $request->motivo_permiso;

            // Guardar los cambios
            $motivoPermiso->save();

            DB::commit();
            Toastr::success('Motivo del permiso actualizado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al guardar el motivo: ', 'Error');
            return redirect()->back();
        }
    }

    public function deleteMotivoPermiso(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tipo de contrato por su ID
            $motivo = MotivoPermiso::where('id',$request->motivoPermisioId)->first();

            // Eliminar el tipo de contrato
            $motivo->delete();

            DB::commit();

            // Mensaje de éxito
            Toastr::success('Motivo de permiso eliminado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al eliminar el motivo', 'Error');
            return redirect()->back();
        }
    }








     public function storeMotivoFeriado(Request $request)
    {
        $request->validate([
            'motivo_feriado' => 'required|string|max:255',
        ]);
        DB::beginTransaction();
        try {
            // Lógica para guardar el cargo
            $motivoFeriado = new MotivoFeriado;
            $motivoFeriado->descripcion = $request->motivo_feriado;
            $motivoFeriado->save();

            DB::commit();
            Toastr::success('Motivo de feriado creado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al crear motivo feriado.', 'Error');
            return redirect()->back();
        }
    }

    public function updateMotivoFeriado(Request $request, $id)
    {
        
        // dd($id);
        // Validar los datos del formulario
        $request->validate([
            'motivo_feriado' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $motivoFeriado = MotivoFeriado::findOrFail($id);
            $motivoFeriado->descripcion = $request->motivo_feriado;

            // Guardar los cambios
            $motivoFeriado->save();

            DB::commit();
            Toastr::success('Motivo del feriado actualizado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al guardar el motivo de feriado: ', 'Error');
            return redirect()->back();
        }
    }

    public function deleteMotivoFeriado(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Buscar el tipo de contrato por su ID
            $motivo = MotivoFeriado::where('id',$request->motivoFeriadoId)->first();

            // Eliminar el tipo de contrato
            $motivo->delete();

            
            DB::commit();

            // Mensaje de éxito
            Toastr::success('Motivo de Feriado eliminado con éxito.', 'Éxito');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            // Mensaje de error
            Toastr::error('Error al eliminar el motivo', 'Error');
            return redirect()->back();
        }
    }
}
