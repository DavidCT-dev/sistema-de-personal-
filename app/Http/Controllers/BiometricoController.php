<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\Persona;

class BiometricoController extends Controller
{
   public function procesarAsistencia(Request $request)
{
    $asistencias = $request->data; // array de registros
    $nombreDB = $request->nombreDB;

    if (!is_array($asistencias) || empty($nombreDB)) {
        return response()->json([
            'error' => 'Datos inválidos o incompletos.',
        ], 400);
    }

    $registrados = 0;
    $duplicados = 0;
    $noRegistrados = 0;

    DB::beginTransaction();

    try {
        foreach ($asistencias as $data) {
            $userSn = $data['sn'] ?? null;
            $deviceUserId = $data['user_id'] ?? null;
            $recordTime = $data['record_time'] ?? null;
            $ip = $data['ip'] ?? null;

            if (!$deviceUserId || !$recordTime) {
                continue;
            }

            try {
                $recordTimeCleaned = preg_replace('/\(.*\)$/', '', $recordTime);
                $fecha = \Carbon\Carbon::parse($recordTimeCleaned)->toDateString();
                $hora = \Carbon\Carbon::parse($recordTimeCleaned)->toTimeString();
            } catch (\Exception $e) {
                continue; // ignorar si falla el formato de fecha
            }

            // Evitar duplicados
            $existing = DB::table($nombreDB)
                ->where('ci', $deviceUserId)
                ->where('fecha', $fecha)
                ->where('hora', $hora)
                ->first();

            if ($existing) {
                $duplicados++;
                continue;
            }

            $persona = Persona::where('ci', $deviceUserId)->first();

            if (!$persona) {
                $noRegistrados++;
                continue;
            }

            // Insertar marcación
            DB::table($nombreDB)->insert([
                'fecha' => $fecha,
                'hora' => $hora,
                'ci' => $deviceUserId,
                'ip' => $ip,
            ]);

            $registrados++;
        }

        DB::commit();

        return response()->json([
            'message' => 'Sincronización completada.',
            'registrados' => $registrados,
            'duplicados' => $duplicados,
            'noRegistrados' => $noRegistrados
        ], 200);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'error' => 'Error al procesar asistencias.',
            'details' => $e->getMessage()
        ], 500);
    }
}

    


    public function registrarEmpleado(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'userId' => 'required|string|max:255', // Este corresponde al campo 'ci'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'details' => $validator->errors(),
            ], 422);
        }



        // Verificar si ya existe un registro con el mismo 'ci', incluyendo los eliminados
        // $personaExistenteDele = Persona::withTrashed()->where('ci', $request->userId)->first();

        // if ($personaExistenteDele->trashed()) {

        //         return response()->json([
        //             'message' => 'El empleado esta eliminado.',
        //         ], 200); // Código HTTP 200: OK
        // }



        try {

            // Verificar si ya existe un registro con el mismo 'ci'
            $personaExistente = Persona::where('ci', $request->userId)->first();

            if($personaExistente && !$personaExistente->uid_bio){
                $personaExistente->uid_bio = $request->uid;
                $personaExistente->save();
            }

            if ($personaExistente) {
                // Si existe, no hacer nada o actualizar si es necesario
                return response()->json(['message' => 'El empleado con este CI ya está registrado.'], 200);
            }

            // Iniciar la transacción
            DB::beginTransaction();

            // Crear la nueva persona
            $persona = new Persona();
            $persona->nombres = $request->name;
            $persona->ci = $request->userId;
            $persona->fech_ing = now(); // Asignar la fecha de ingreso actual
            $persona->uid_bio = $request->uid;

            // Guardar el registro en la base de datos
            $persona->save();
            $persona->uid_bio = $request->uid;
            $persona->save();

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'message' => 'Empleado registrado con éxito.'
            ], 201); // Código HTTP 201: Creado

        } catch (QueryException $e) {
            // Revertir la transacción en caso de error de base de datos
            DB::rollback();

            return response()->json([
                'error' => 'Error en la base de datos.',
                'details' => $e->getMessage(),
            ], 500); // Código HTTP 500: Error interno del servidor

        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otra excepción
            DB::rollback();

            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage(),
            ], 500); // Código HTTP 500: Error interno del servidor
        }
    }


}
