<?php

namespace App\Http\Controllers\Content\Conexion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

use App\Models\Biometrico;
use App\Models\Persona;
use Exception;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class ConexionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $biometricos = Biometrico::all();
        // Enviamos los datos paginados a la vista
        return view('content.conexion.index', ['biometricos' => $biometricos]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'id_bio' => [
                'required',
                'integer', // Ahora es un ID numérico
                'exists:biometricos,id' // Verifica existencia en la tabla biometricos
            ]
        ], [
            // Mensajes para ID
            'id_bio.required' => 'El ID del biométrico es obligatorio.',
            'id_bio.integer' => 'El ID debe ser un número entero.',
            'id_bio.exists' => 'El ID del biométrico no existe en el sistema.'
        ]);

        $file = $request->file('file');
        $id_biometrico = $request->id_bio;

        if ($file && $id_biometrico) {
            $fileContent = file_get_contents($file->getRealPath());  // Lee todo el contenido del archivo

            // Divide el contenido en líneas y elimina líneas vacías
            $lines = array_filter(array_map('trim', explode(PHP_EOL, $fileContent)));

            $noRegistrados = 0;
            $registrados = 0;
            $duplicados = 0;
            $biometrico = Biometrico::find($id_biometrico);
            if (!$biometrico) {
                Toastr::error('Biometrico no encontrado.', 'Error');
                return;
            }
            // Iterar sobre las líneas y procesar
            foreach ($lines as $line) {
                // Dividir la línea en columnas (ajusta el separador según el formato del archivo)
                $columns = explode("\t", $line);

                // Validar que haya suficientes columnas
                if (count($columns) < 6) { // Ajusta el número según el formato esperado
                    $noRegistrados++;
                    continue;
                }

                // Buscar la persona por CI
                $persona = Persona::where('ci', trim($columns[0]))->first();

                if ($persona) {
                    // Verificar si ya existe una marcación para esa persona en la misma fecha y hora
                    $time = explode(" ", $columns[1]);


                    $marcacionExistente = DB::table($biometrico->nombre_base_de_datos)->where('ci', trim($columns[0]))
                        ->whereDate('fecha', '=', date('Y-m-d', strtotime($time[0])))
                        ->whereTime('hora', '=', date('H:i:s', strtotime($time[1])))
                        ->first();

                    if ($marcacionExistente) {
                        $duplicados++;
                    } else {

                        $datetime = explode(" ", $columns[1]);
                        
                        DB::table($biometrico->nombre_base_de_datos)->insert([
                            'fecha' => $datetime[0],
                            'hora' => $datetime[1],
                            'ci' => trim($columns[0]),
                        ]);
                        $registrados++;
                    }
                } else {
                    $noRegistrados++;
                }
            }

            // Mostrar mensajes según los resultados
            if ($registrados > 0) {
                Toastr::success('Marcaciones guardadas correctamente: ' . $registrados, 'Éxito');
            }

            if ($duplicados > 0) {
                Toastr::info('Marcaciones duplicadas detectadas: ' . $duplicados, 'Información');
            }

            if ($noRegistrados > 0) {
                Toastr::error('Personas no registradas en el sistema: ' . $noRegistrados, 'Error');
            }

            return redirect()->route('conexion.index');
        } else {
            Toastr::error('No existe archivo', 'Error');
            return redirect()->route('conexion.index');
        }
    }

    public function crearBiometrico(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombre_biometrico' => [
                'required',
                'string',
                'max:100',
                'unique:biometricos,nombre_biometrico',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/u' // Solo letras y espacios
            ],
            'ip_biometrico' => 'required|ipv4|unique:biometricos,ip_biometrico',
        ], [
            'nombre_biometrico.required' => 'El nombre del biométrico es obligatorio.',
            'nombre_biometrico.string' => 'El nombre debe ser un texto válido.',
            'nombre_biometrico.max' => 'El nombre no debe superar los 100 caracteres.',
            'nombre_biometrico.unique' => 'Este nombre ya está registrado.',
            'nombre_biometrico.regex' => 'El nombre solo puede contener letras y espacios.',

            'ip_biometrico.required' => 'La dirección IP es obligatoria.',
            'ip_biometrico.ipv4' => 'Debe ingresar una dirección IP válida.',
            'ip_biometrico.unique' => 'Esta dirección IP ya está registrada.',
        ]);




        // Realizar un ping al biométrico para verificar su disponibilidad
        $ipBiometrico = $request->ip_biometrico;

        $puerto = 4370; // Cambia al puerto correcto según el modelo

        $conexion = @fsockopen($ipBiometrico, $puerto, $errno, $errstr, 2);

        if (!$conexion) {
            Toastr::error('La IP no pertenece a un biométrico o no está accesible.', 'Error');
            return redirect()->back();
        }

        // Determinar el comando de ping según el sistema operativo
        $pingCommand = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "C:\\Windows\\System32\\ping.exe -n 1 -w 1000 $ipBiometrico"
            : "ping -c 1 -W 1 $ipBiometrico";


        // $pingResult = Process::fromShellCommandline($pingCommand);
        $pingResult = Process::fromShellCommandline($pingCommand);

        $pingResult->run();
        if (!$pingResult->isSuccessful()) {
            Toastr::error('No se pudo conectar al biométrico. Verifique la dirección IP.', 'Error');
            return redirect()->back();
        }

        // Generar el nombre de la base de datos con prefijo "bio"
        $nombreDB = 'bio' . str_replace(' ', '_', strtolower($request->nombre_biometrico));
        try {


            Config::set('database.connections.pgsql.database', 'personal');
            DB::purge('pgsql'); // Limpiar la conexión anterior
            DB::reconnect('pgsql'); // Reconectar con la nueva base de datos

            // Crear la tabla en la nueva base de datos
            Schema::create($nombreDB, function ($table) {
                $table->id(); // Columna autoincremental 'id'
                $table->date('fecha'); // Fecha en formato 'YYYY-MM-DD'
                $table->time('hora');  // Hora en formato 'HH:MM:SS'
                $table->string('ci', 20); // Número de cédula o identificación
                $table->string('ip', 15)->nullable();
                $table->string('estado', 50)->nullable();
                $table->timestamps();
            });
            // dd('pasa');

            // dd($db);
            // Guardar el registro del biometrico en la tabla `biometricos`
            Biometrico::create([
                'nombre_biometrico' => $request->nombre_biometrico,
                'ip_biometrico' => $request->ip_biometrico,
                'nombre_base_de_datos' => $nombreDB,
            ]);


            // Mostrar mensaje de éxito con Toastr
            Toastr::success('Biometrico agregado correctamente: ' . $nombreDB, 'Éxito');

            return redirect()->route('conexion.index');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();

            if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] == 1007) {
                Toastr::error('La base de datos ya existe. Por favor, elija otro nombre.', 'Error');
            } else {
                Toastr::error('Error al crear la base de datos: ' . $e->getMessage(), 'Error');
            }

            return redirect()->route('conexion.index');
        }
    }
}
