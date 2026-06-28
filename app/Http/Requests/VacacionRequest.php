<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use DateTime;
use Carbon\Carbon;

class VacacionRequest extends FormRequest
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
        // Agregar un campo adicional 'request_method' para identificar el método HTTP
        $this->merge([
            'request_method' => $this->method(), // Ejemplo de campo adicional
        ]);

    
    if ($this->has('fecha_inicio')) {
           
        $fecha_inicio = \DateTime::createFromFormat('d M Y', $this->fecha_inicio);
        // Parseamos con Carbon usando el formato localizado
            
            if ($fecha_inicio) {
                $this->merge([
                    'fecha_inicio' => $fecha_inicio->format('Y-m-d'), // Formato para BD
                ]);
            }
       
    }

    if ($this->has('fecha_fin')) {

        $fecha_fin = \DateTime::createFromFormat('d M Y', $this->fecha_fin);

            
        if ($fecha_fin) {
            $this->merge([
                'fecha_fin' => $fecha_fin->format('Y-m-d'), // Formato para BD
            ]);
        }
   
    }
    }

    protected function failedValidation(Validator $validator)
    {
        // Asegurar que request_method esté en los datos antiguos
        $this->merge([
            'request_method' => $this->method(),
        ]);

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput($this->all())
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_persona' => 'required|exists:personas,id',
            'observacion' => 'nullable|string|max:500',
        ];

        // Si es una actualización, puedes agregar reglas específicas aquí
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            // Por ejemplo, validar que la fecha no sea anterior a hoy en actualizaciones
            $rules['fecha_inicio'] = 'required|date';
            $rules['fecha_fin'] = 'required|date|after_or_equal:fecha_inicio';
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',

            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'id_persona.required' => 'Debe seleccionar un empleado.',
            'id_persona.exists' => 'El empleado seleccionado no existe.',

            'observacion.string' => 'La observación debe ser texto.',
            'observacion.max' => 'La observación no puede exceder los 500 caracteres.',
        ];
    }
}
