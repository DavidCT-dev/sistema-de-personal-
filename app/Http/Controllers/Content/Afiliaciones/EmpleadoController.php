<?php

namespace App\Http\Controllers\Content\Afiliaciones;

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
use App\Models\User;
use Carbon\Carbon;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\RequestEmpleado;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
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
        $biometricos = Biometrico::all();
        $cargos = Cargos::all();
        $horarios = Horario::all();
        $generos = Genero::all();
        $lugaresTrabajo = LugarTrabajo::all();
        $tipoContratos = TipoContrato::all();

        // Incluimos la relación biometricos
        $empleados = Persona::with([
            'tipoContrato',
            'horarios',
            'lugarTrabajo',
            'cargo',
            'genero',
            'biometricos'
        ])->get();
        return view('content.empledos.empleados', compact(
            'empleados',
            'cargos',
            'horarios',
            'generos',
            'lugaresTrabajo',
            'tipoContratos',
            'biometricos'
        ));
    }


    public function crearUsuarioDesdePersona(Persona $persona)
    {
        // Crear el usuario en la tabla users
        $user = User::create([
            'name' => trim($persona->ci),
            'join_date' => now(),
            'last_login' => null,
            'phone_number' => $persona->celular,
            'status' => 'active',
            'password' => Hash::make($persona->ci),
        ]);

        // Relacionar usuario con persona (si la tabla `users` tiene `persona_id`)
        $user->persona_id = $persona->id;
        $user->save();

        return $user;
    }


    public function calcularVacacion($antiguedad)
    {
        if ($antiguedad >= 0 && $antiguedad <= 4) {
            return 15;
        } elseif ($antiguedad >= 5 && $antiguedad <= 9) {
            return 20;
        } elseif ($antiguedad >= 10) {
            return 30;
        }

        return 0;
    }


    public function store(RequestEmpleado $request)
    {
        DB::beginTransaction();

        try {
            // Crear nueva persona
            $persona = Persona::create([
                'nombres' => $request->nombres,
                'apellido_pat' => $request->apellido_pat,
                'apellido_mat' => $request->apellido_mat,
                'fecha_nac' => Carbon::parse($request->fecha_nac),
                'direccion' => $request->direccion,
                'ci' => $request->ci,
                'antiguedad' => $request->antiguedad,
                'celular' => $request->celular,
                'fech_ing' => isset($request->fech_ing) ? Carbon::parse($request->fech_ing) : null,
                'fech_baj' => isset($request->fech_baj) ? Carbon::parse($request->fech_baj) : null,
                'item' => $request->item,
                'tipo_contrato_id' => $request->tipo_contrato_id,
                'lugar_trabajo_id' => $request->lugar_trabajo_id,
                'cargo_id' => $request->cargo_id,
                'genero_id' => $request->genero_id,
                'kardex_visible' => isset($request->kardex_visible),
                'auto_sabados' => isset($request->auto_sabados),
                'biometrico_registro' => isset($request->biometrico_registro),
            ]);

            // Asignar horarios (relación muchos a muchos)
            if ($request->has('horarios')) {
                $persona->horarios()->sync($request->horarios);
            }

            // Crear usuario si no existe
            if (!User::where('persona_id', $persona->id)->exists()) {
                $this->crearUsuarioDesdePersona($persona);
            }

            // Calcular vacaciones
            $persona->total_dias_vacacion = $this->calcularVacacion($persona->antiguedad);
            $persona->save();

            // Manejo biométrico
            if ($persona->biometrico_registro && $request->has('biometricos')) {
                $persona->biometricos()->sync($request->biometricos);

                foreach ($request->biometricos as $biometricoId) {
                    $biometrico = Biometrico::find($biometricoId);

                    $payload = [
                        'uid' => $persona->id,
                        'name' => trim($persona->nombres . ' ' . $persona->apellido_pat . ' ' . ($persona->apellido_mat ?? '')),
                        'userId' => $persona->ci,
                        'password' => $persona->ci,
                        'ip_biometrico' => $biometrico->ip_biometrico,
                    ];

                    $response = Http::post(env('BIOMETRIC_API_URL') . '/insertar-usuario-biometrico', $payload);

                    if (!$response->successful()) {
                        throw new \Exception("Error al registrar en el biométrico {$biometrico->nombre_biometrico}");
                    }
                }

                $persona->uid_bio = $persona->id;
                $persona->save();
            }

            DB::commit();

            $message = isset($response) ? ($response->json('message') ?? 'Persona creada exitosamente :)') : 'Persona creada exitosamente :)';
            Toastr::success($message, 'Success');

            return redirect()->route('empleados.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Fallo al crear la persona: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }








    public function update(RequestEmpleado $request, $id)
    {
        DB::beginTransaction();

        try {
            $empleado = Persona::with(['biometricos'])->findOrFail($id);

            $originalCi = $empleado->ci;
            $originalBiometricoRegistro = $empleado->biometrico_registro;
            $biometricosOriginales = $empleado->biometricos;

            // Actualización del empleado
            $empleado->update([
                'nombres' => $request->nombres,
                'apellido_pat' => $request->apellido_pat,
                'apellido_mat' => $request->apellido_mat,
                'fecha_nac' => Carbon::parse($request->fecha_nac),
                'direccion' => $request->direccion,
                'ci' => $request->ci,
                'celular' => $request->celular,
                'fech_ing' => $request->filled('fech_ing') ? Carbon::parse($request->fech_ing) : null,
                'fech_baj' => $request->filled('fech_baj') ? Carbon::parse($request->fech_baj) : null,
                'item' => $request->item,
                'tipo_contrato_id' => $request->tipo_contrato_id,
                'lugar_trabajo_id' => $request->lugar_trabajo_id,
                'cargo_id' => $request->cargo_id,
                'genero_id' => $request->genero_id,
                'kardex_visible' => $request->has('kardex_visible'),
                'auto_sabados' => $request->has('auto_sabados'),
                'biometrico_registro' => $request->has('biometrico_registro'),
            ]);

            // ✅ Sincronización de horarios
            if ($request->has('horarios')) {
                $empleado->horarios()->sync($request->horarios);
            }

            $nuevoBiometricoRegistro = $request->has('biometrico_registro');
            $nuevoCi = $request->ci;
            $nuevosBiometricoIds = $request->biometricos ?? [];

            // Registrar en biométricos si corresponde
            if ($nuevoBiometricoRegistro) {
                $empleado->biometricos()->sync($nuevosBiometricoIds);

                foreach ($nuevosBiometricoIds as $biometricoId) {
                    $biometrico = Biometrico::findOrFail($biometricoId);
                    $payload = [
                        'uid' => $empleado->id,
                        'name' => trim($empleado->nombres . ' ' . $empleado->apellido_pat . ' ' . ($empleado->apellido_mat ?? '')),
                        'userId' => $empleado->ci,
                        'password' => $empleado->ci,
                        'ip_biometrico' => $biometrico->ip_biometrico,
                    ];

                    $yaRegistrado = $biometricosOriginales->contains('id', $biometrico->id);

                    if (!$yaRegistrado || $originalCi != $nuevoCi) {
                        if ($yaRegistrado && $originalCi != $nuevoCi) {
                            Http::delete(env('BIOMETRIC_API_URL') . '/eliminar-usuario-biometrico/' . $empleado->uid_bio . '/' . $biometrico->ip_biometrico);
                        }

                        $response = Http::post(env('BIOMETRIC_API_URL') . '/insertar-usuario-biometrico', $payload);

                        if (!$response->successful()) {
                            throw new Exception('Error al insertar o actualizar usuario en el biométrico');
                        }
                    }
                }

                $empleado->update(['uid_bio' => $empleado->id]);

                Toastr::success('Empleado y dispositivos biométricos actualizados', 'Éxito');
                Toastr::info('Se recomienda registrar huellas digitales en el dispositivo', 'Atención');
            }

            // Si se desactiva el registro biométrico
            if (!$nuevoBiometricoRegistro && $originalBiometricoRegistro) {
                foreach ($biometricosOriginales as $biometrico) {
                    $data = [
                        'nombreDB' => $biometrico->nombre_base_de_datos,
                        'ip_biometrico' => $biometrico->ip_biometrico,
                        'DB_HOST' => env('DB_HOST'),
                        'DB_PORT' => env('DB_PORT'),
                        'DB_DATABASE' => env('DB_DATABASE'),
                        'DB_USERNAME' => env('DB_USERNAME'),
                        'DB_PASSWORD' => env('DB_PASSWORD'),
                    ];

                    Http::post(env('BIOMETRIC_API_URL') . '/sincronizar-asistencias', $data);

                    Http::delete(env('BIOMETRIC_API_URL') . '/eliminar-usuario-biometrico/' . $empleado->uid_bio . '/' . $biometrico->ip_biometrico);
                }

                $empleado->biometricos()->detach();
                $empleado->update(['uid_bio' => null]);

                Toastr::success('Empleado eliminado de todos los dispositivos biométricos', 'Éxito');
            }

            DB::commit();
            return redirect()->route('empleados.index');
        } catch (Exception $e) {
            DB::rollback();
            Toastr::error('Error al actualizar empleado: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }




    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $empleado = Persona::with('biometricos')->find($request->empleadoId);

            if (!$empleado) {
                throw new Exception('No existe empleado.');
            }

            $biometricos = $empleado->biometricos;

            // ✅ Eliminar soft-delete
            $empleado->delete();

            // ✅ Desvincular relaciones
            $empleado->biometricos()->detach();
            $empleado->horarios()->detach(); // <-- desvincula los horarios

            // ✅ Confirmar transacción antes de las llamadas externas
            DB::commit();

            // ✅ Procesar eliminación en los dispositivos biométricos
            foreach ($biometricos as $biometrico) {
                $data = [
                    'nombreDB' => $biometrico->nombre_base_de_datos,
                    'ip_biometrico' => $biometrico->ip_biometrico,
                    'DB_HOST' => env('DB_HOST'),
                    'DB_PORT' => env('DB_PORT'),
                    'DB_DATABASE' => env('DB_DATABASE'),
                    'DB_USERNAME' => env('DB_USERNAME'),
                    'DB_PASSWORD' => env('DB_PASSWORD'),
                ];

                Http::post(env('BIOMETRIC_API_URL') . '/sincronizar-asistencias', $data);

                $response = Http::delete(
                    env('BIOMETRIC_API_URL') . '/eliminar-usuario-biometrico/' .
                        $empleado->uid_bio . '/' .
                        $biometrico->ip_biometrico
                );

                Toastr::success($response->json()['message'] ?? 'Empleado eliminado correctamente del biométrico.', 'Success');
            }

            if ($biometricos->isEmpty()) {
                Toastr::success('Empleado eliminado correctamente.', 'Success');
            }

            return redirect()->route('empleados.index');
        } catch (Exception $e) {
            DB::rollBack();

            Toastr::error($e->getMessage(), 'Error');
            return redirect()->route('empleados.index');
        }
    }
}
