<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FeriadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparar los datos antes de la validación.
     */
    protected function prepareForValidation()
    {
        // Agregar un campo adicional si es necesario
        $this->merge([
            'request_method' => $this->method(), // Ejemplo de campo adicional
        ]);
    }

    /**
     * Manejar errores de validación personalizados.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Asegurarse de que 'request_method' esté en los datos antiguos (old input)
        $this->merge([
            'request_method' => $this->method(),
        ]);

        // Lanzar la excepción predeterminada de Laravel
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator) // Pasar los errores de validación
                ->withInput($this->all()) // Mantener todos los datos de entrada, incluyendo 'request_method'
        );
    }

    /**
     * Obtener las reglas de validación que se aplican al request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $this->merge([
            'hora_inicio' => substr($this->hora_inicio, 0, 5), // Extrae solo HH:mm
            'hora_fin' => substr($this->hora_fin, 0, 5),       // Extrae solo HH:mm
        ]);

        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            return [
                'tipo' => 'required|exists:motivo_feriado,id',
                'fecha' => 'required|date',
                'descripcion' => 'required|string|max:255',
                'observacion' => 'required|string|max:255', // Permitir nulo en actualización
                'sexo' => 'required|string|in:T,M,F',
                'hora_inicio' => 'required|date_format:H:i', // Formato sin segundos
            'hora_fin' => [
                'required',
                'date_format:H:i', // Formato sin segundos
                'after:hora_inicio', // Asegura que "hora_fin" sea posterior a "hora_inicio"
            ],
            ];
        }

        return [
            'tipo' => 'required|exists:motivo_feriado,id',
            'fecha' => 'required|string',
            'descripcion' => 'required|string|max:255',
            'observacion' => 'required|string|max:255',
            'sexo' => 'required|string|in:T,M,F',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ];
    }

    /**
     * Mensajes personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'tipo.required' => 'El campo "Tipo" es obligatorio.',
            'fecha.required' => 'El campo "Fecha del Feriado" es obligatorio.',
            'fecha.date' => 'El campo "Fecha del Feriado" debe ser una fecha válida.',
            'descripcion.required' => 'El campo "Descripción Día Feriado" es obligatorio.',
            'descripcion.max' => 'El campo "Descripción Día Feriado" no debe superar los 255 caracteres.',
            'observacion.required' => 'El campo "Observación" es obligatorio.',
            'observacion.max' => 'El campo "Observación" no debe superar los 255 caracteres.',
            'sexo.required' => 'El campo "Género" es obligatorio.',
            'sexo.in' => 'El campo "Género" debe ser uno de los valores permitidos (T, M, F).',
            'hora_inicio.required' => 'El campo "Inicio" es obligatorio.',
            'hora_inicio.date_format' => 'El campo "Inicio" debe tener un formato de hora válido (HH:mm).',
            'hora_fin.required' => 'El campo "Fin" es obligatorio.',
            'hora_fin.date_format' => 'El campo "Fin" debe tener un formato de hora válido (HH:mm).',
            'hora_fin.after' => 'El campo "Fin" debe ser una hora posterior al campo "Inicio".',
        ];
    }
}