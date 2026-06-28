<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection; // Importa Collection

class ExcelDataReaderController extends Controller
{
    /**
     * Muestra el formulario para subir el archivo Excel.
     *
     * @return \Illuminate\View\View
     */
    public function showUploadForm()
    {
        return view('upload_excel_ci');
    }

    /**
     * Procesa la subida del archivo Excel y extrae el CI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extractCi(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'excel_file' => 'required|mimes:xlsx,xls|max:2048', // Asegúrate de que sea un archivo Excel y no más grande de 2MB
        // ], [
        //     'excel_file.required' => 'Por favor, selecciona un archivo Excel.',
        //     'excel_file.mimes'    => 'El archivo debe ser de tipo XLSX o XLS.',
        //     'excel_file.max'      => 'El archivo no debe exceder los 2MB.',
        // ]);

        try {
            // Leer el archivo Excel en una colección
            // El método toCollection() devuelve una colección de colecciones,
            // donde cada colección interna representa una fila.
            $data = Excel::toCollection(null, $request->file('excel_file'))->first();

            // La imagen muestra que el CI está en la celda B4.
            // En una colección, esto se traduce a:
            // - Fila 4 es el índice 3 (colección base 0)
            // - Columna B es el índice 1 (colección base 0)
            // Por lo tanto, data[3][1]
            $ci = $data[3][1] ?? null; // Usamos null coalescing operator por si la celda está vacía o el índice no existe

            if ($ci) {
                return back()->with('success', 'CI rescatado: ' . $ci);
            } else {
                return back()->with('error', 'No se pudo encontrar el valor del CI en la celda B4.');
            }

        } catch (\Maatwebsite\Excel\ReaderException $e) {
                        dd($e->getMessage());

            return back()->with('error', 'Error al leer el archivo Excel: ' . $e->getMessage());
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }
}