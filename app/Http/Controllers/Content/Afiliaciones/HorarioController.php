<?php

namespace App\Http\Controllers\Content\Afiliaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horario;
use App\Models\Persona;
use App\Models\LugarTrabajo;
use App\Models\AsignacionHorarioPersona;
use Illuminate\Support\Facades\DB;
use Toastr;

use Carbon\Carbon;

class HorarioController extends Controller
{
    public function __construct()
    {
        // Solo pueden acceder los usuarios con tipo de contrato PERMANENTE
        $this->middleware('can:afiliaciones')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $horarios = Horario::all();
        $personas = Persona::with(['cargo','horarios'])->get();
        $lugarTrabajos = LugarTrabajo::all();

        return view('content.empledos.horarios', ['horarios' => $horarios, 'personas' => $personas, 'lugarTrabajos' => $lugarTrabajos]);
    }

    public function store(Request $request)
    {
        // dd($request);
        DB::beginTransaction();

        // $toleranciaEnMinutos = $request->tolerancia * 60;

        try {
            Horario::create([
                'descripcion' => $request->descripcion,
                'tolerancia' => '00:' . $request->tolerancia . ':00',
                'ingreso1' => Carbon::parse($request->ingreso1),
                'salida1' => Carbon::parse($request->salida1),
                'ingreso2' => isset($request->ingreso2) ? Carbon::parse($request->ingreso2) : null,
                'salida2' => isset($request->salida2) ? Carbon::parse($request->salida2) : null,
                'observaciones' => $request->observaciones ?? '',
            ]);

            DB::commit();
            Toastr::success('Horario Creado Exitosamente :)', 'Success');

            return redirect()->back();
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
            Toastr::error('Add new schedule fail :)', 'Error');
            return redirect()->back();
        }
    }





    public function update(Request $request, $id)
    {
        // Validación de los datos (si es necesario)
        // $validated = $request->validate([
        //     'descripcion' => 'required|string|max:255',
        //     'tolerancia' => 'required|integer',
        //     'ingreso1' => 'required|date_format:H:i',
        //     'salida1' => 'required|date_format:H:i',
        //     'ingreso2' => 'nullable|date_format:H:i',
        //     'salida2' => 'nullable|date_format:H:i',
        //     'observaciones' => 'nullable|string|max:255',
        // ]);
        DB::beginTransaction();

        try {
            // Buscar el horario que se desea actualizar
            $horario = Horario::findOrFail($request->horario_id);

            // Actualizar los campos del horario
            $horario->tolerancia = isset($request->tolerancia)
                ?  '00:' . $request->tolerancia . ':00'
                : $horario->tolerancia;

            $horario->ingreso1 = isset($request->ingre1) ? Carbon::parse($request->ingre1) : $horario->ingreso1;
            $horario->salida1 = isset($request->sali1) ? Carbon::parse($request->sali1) :  $horario->salida1;
            $horario->ingreso2 = isset($request->ingre2) ? Carbon::parse($request->ingre2) : $horario->ingreso2;
            $horario->salida2 = isset($request->sali2) ? Carbon::parse($request->sali2) : $horario->salida2;
           
            // Guardar los cambios
            $horario->save();

            DB::commit();

            Toastr::info('Horario Actualizado Exitosamente :)', 'Info');
            return redirect()->route('horarios.index'); // Redirige a la lista de horarios

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Error al actualizar el horario :(', 'Error');
            return redirect()->back();
        }
    }

    public function updateHorarios(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);

        // Caso: asignar a todos
        if ($request->persona_id === 'todos') {
            // Caso 1: Todos en un lugar específico
            if ($request->lugar_trabajo_id !== 'todos') {
                $personas = Persona::where('lugar_trabajo_id', $request->lugar_trabajo_id)->get();
            } else {
                // Caso 2: Todos en todos los lugares
                $personas = Persona::all();
            }

            foreach ($personas as $persona) {
                AsignacionHorarioPersona::create([
                    'id_persona' => $persona->id,
                    'id_horario' => $request->horario_id,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                ]);
            }
        } else {
            // Caso: persona específica
            AsignacionHorarioPersona::create([
                'id_persona' => $request->persona_id,
                'id_horario' => $request->horario_id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ]);
        }

        DB::commit();
        Toastr::success('Horario asignado exitosamente :)', 'Éxito');
        return redirect()->route('horarios.index');
    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Error al asignar el horario :(', 'Error');
        return redirect()->back()->withInput();
    }
}

 public function asignarHorario(Request $request, $id){
    dd($request);
 }

}
