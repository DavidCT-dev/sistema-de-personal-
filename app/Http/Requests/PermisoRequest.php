<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PermisoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cambia a `false` si necesitas restringir el acceso
    }

    /**
     * Preparar los datos antes de la validación.
     */
    protected function prepareForValidation()
{
    // Agregar un campo adicional 'request_method' para identificar el método HTTP
    $this->merge([
        'request_method' => $this->method(), // Ejemplo de campo adicional
    ]);

    // Asegurarse de que las horas solo tengan el formato HH:mm
    if ($this->has('hora_inicio')) {

        $this->merge([
            'hora_inicio' => substr($this->hora_inicio, 0, 5), // Extrae solo HH:mm
        ]);
    }

    if ($this->has('hora_fin')) {
        $this->merge([
            'hora_fin' => substr($this->hora_fin, 0, 5), // Extrae solo HH:mm
        ]);
    }
     // Convertir 'fecha_permiso' a formato de fecha (Y-m-d)
     if ($this->has('fecha_permiso')) {
       
        $fechaPermiso = \DateTime::createFromFormat('d M Y', $this->fecha_permiso);
        if ($fechaPermiso) {
            $this->merge([
                'fecha_permiso' => $fechaPermiso->format('Y-m-d'), // Formato válido para la base de datos
            ]);
        }
    }

    // Convertir 'fecha_fin_varios_dias' a formato de fecha (Y-m-d)
    if ($this->has('fecha_fin_varios_dias')) {
        $fechaFinVariosDias = \DateTime::createFromFormat('d M Y', $this->fecha_fin_varios_dias);
        if ($fechaFinVariosDias) {
            $this->merge([
                'fecha_fin_varios_dias' => $fechaFinVariosDias->format('Y-m-d'), // Formato válido para la base de datos
            ]);
        }
    }
}

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            return [
                'fecha_permiso' => 'required|date',
                'id_persona' => 'required|exists:personas,id',
                'motivo' => 'required|exists:motivo_permiso,id',
                'observacion' => 'nullable|string|max:500',
                'duracion_permiso' => 'required|in:dia_completo,medio_dia,varios_dias',
                'hora_inicio' => [
                    'nullable',
                    'required_if:duracion_permiso,medio_día',
                    'date_format:H:i', // Formato sin segundos
                ],
                'hora_fin' => [
                    'nullable',
                    'required_if:duracion_permiso,medio_día',
                    'date_format:H:i', // Formato sin segundos
                    'after:hora_inicio', // Asegura que "hora_fin" sea posterior a "hora_inicio"
                ],
                'fecha_fin_varios_dias' => [
                    'nullable',
                    'required_if:duracion_permiso,varios_días',
                    'date',
                    'after:fecha_permiso', // Asegura que "fecha_fin_varios_dias" sea igual o posterior a "fecha_permiso"
                ],
            ];
        }

        return [
            'fecha_permiso' => 'required|date',
            'id_persona' => 'required|exists:personas,id',

              'motivo' => 'required|exists:motivo_permiso,id',
            'observacion' => 'nullable|string|max:500',
            'duracion_permiso' => 'required|in:dia_completo,medio_dia,varios_dias',
            'hora_inicio' => [
                'nullable',
                'required_if:duracion_permiso,medio_día',
                'date_format:H:i', // Formato sin segundos
            ],
            'hora_fin' => [
                'nullable',
                'required_if:duracion_permiso,medio_día',
                'date_format:H:i', // Formato sin segundos
                'after:hora_inicio', // Asegura que "hora_fin" sea posterior a "hora_inicio"
            ],
            'fecha_fin_varios_dias' => [
                'nullable',
                'required_if:duracion_permiso,varios_días',
                'date',
                'after:fecha_permiso', // Asegura que "fecha_fin_varios_dias" sea igual o posterior a "fecha_permiso"
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fecha_permiso.required' => 'La fecha del permiso es obligatoria.',
            'fecha_permiso.date' => 'La fecha del permiso debe ser una fecha válida.',
           
            'motivo.required' => 'El motivo del permiso es obligatorio.',
            'observacion.max' => 'La observación no puede exceder los 500 caracteres.',
            'duracion_permiso.required' => 'La duración del permiso es obligatoria.',
            'duracion_permiso.in' => 'La duración del permiso debe ser uno de los siguientes: día completo, medio día o varios días.',
            'hora_inicio.required_if' => 'La hora de inicio es obligatoria cuando se selecciona "medio día".',
            'hora_inicio.date_format' => 'La hora de inicio debe tener un formato válido (HH:mm).',
            'hora_fin.required_if' => 'La hora de fin es obligatoria cuando se selecciona "medio día".',
            'hora_fin.date_format' => 'La hora de fin debe tener un formato válido (HH:mm).',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'fecha_fin_varios_dias.required_if' => 'La fecha de fin es obligatoria cuando se selecciona "varios días".',
            'fecha_fin_varios_dias.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin_varios_dias.after' => 'La fecha de fin debe ser posterior a la fecha del permiso.',
        ];
    }
}
