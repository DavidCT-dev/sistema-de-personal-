<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestEmpleado extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
    {
        // Agregar un campo adicional si es necesario
        $this->merge([
            'request_method' => $this->method(), // Ejemplo de campo adicional
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Asegurarse de que 'request_method' esté en los datos antiguos (old input)
        $this->merge([
            'request_method' => $this->method(),
        ]);

        // Lanzar la excepción predeterminada de Laravel
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
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
    public function rules()
    {
        $id = $this->route('empleado'); // Obtener el ID del empleado en la actualización

        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            return [
                'nombres' => 'required|string|max:255',
                'apellido_pat' => 'required|string|max:255',
                'apellido_mat' => 'required|string|max:255',
                'fecha_nac' => 'nullable|date|before:today',
                'direccion' => 'nullable|string|max:500',
                'antiguedad' => 'nullable|numeric',
                'ci' => 'required|string|max:10|unique:personas,ci,' . $id, 
                'celular' => 'nullable|string|max:8',
                'fech_ing' => 'required|date|after_or_equal:fecha_nac',
                'fech_baj' => 'nullable|date|after:fech_ing',
                'item' => 'required|string|max:8',
                'tipo_contrato_id' => 'required|exists:tipo_contratos,id',
                'horarios' => 'required|array',
                'horarios.*' => 'exists:horarios,id',
                'lugar_trabajo_id' => 'required|exists:lugar_trabajos,id',
                'cargo_id' => 'required|exists:cargos,id',
                'genero_id' => 'required|exists:generos,id',
                'kardex_visible' => 'boolean',
                'auto_sabados' => 'boolean',
                'biometrico_registro' => 'boolean',
                'biometricos' => 'nullable|required_if:biometrico_registro,true|array',
                'biometricos.*' => 'exists:biometricos,id',
            ];
        }
        return [
            'nombres' => 'required|string|max:255',
            'apellido_pat' => 'required|string|max:255',
            'apellido_mat' => 'required|string|max:255',
            'fecha_nac' => 'nullable|date|before:today',
            'direccion' => 'nullable|string|max:500',
            'ci' => 'required|string|unique:personas,ci|max:10',
            'antiguedad' => 'nullable|numeric',
            'celular' => 'nullable|string|max:8',
            'fech_ing' => 'required|date|after_or_equal:fecha_nac',
            'fech_baj' => 'nullable|date|after:fech_ing',
            'item' => 'required|string|max:8',
            'tipo_contrato_id' => 'required|exists:tipo_contratos,id',
            'horarios' => 'required|array',
            'horarios.*' => 'exists:horarios,id',
            'lugar_trabajo_id' => 'required|exists:lugar_trabajos,id',
            'cargo_id' => 'required|exists:cargos,id',
            'genero_id' => 'required|exists:generos,id',
            'kardex_visible' => 'boolean',
            'auto_sabados' => 'boolean',
            'biometrico_registro' => 'boolean',
            'biometricos' => 'nullable|required_if:biometrico_registro,true|array',
            'biometricos.*' => 'exists:biometricos,id',
        ];
        // validaciones backend
    }

    public function messages()
    {
        return [
            // Validaciones para nombres y apellidos
            'nombres.required' => 'El campo "Nombre" es obligatorio.',
            'nombres.max' => 'El campo "Nombre" no debe superar los 255 caracteres.',
            'apellido_pat.required' => 'El campo "Apellido Paterno" es obligatorio.',
            'apellido_pat.max' => 'El campo "Apellido Paterno" no debe superar los 255 caracteres.',
            'apellido_mat.required' => 'El campo "Apellido Materno" es obligatorio.',
            'apellido_mat.max' => 'El campo "Apellido Materno" no debe superar los 255 caracteres.',

            // Validaciones para la fecha de nacimiento
            'fecha_nac.date' => 'El campo "Fecha de Nacimiento" debe ser una fecha válida.',
            'fecha_nac.before' => 'La "Fecha de Nacimiento" debe ser anterior a la fecha actual.',

            // Validaciones para la dirección
            'direccion.max' => 'El campo "Dirección" no debe superar los 500 caracteres.',

            'antiguedad.numeric' => 'el campo antiguedad debe se un número',
            // Validaciones para CI
            'ci.required' => 'El campo "CI" es obligatorio.',
            'ci.unique' => 'El "CI" ya ha sido registrado.',
            'ci.max' => 'El campo "CI" no debe superar los 10 caracteres.',

            // Validaciones para el celular
            'celular.max' => 'El campo "Celular" no debe superar los 8 caracteres.',

            // Validaciones para fechas de ingreso y baja
            'fech_ing.required' => 'El campo "Fecha de Ingreso" es obligatorio.',
            'fech_ing.date' => 'El campo "Fecha de Ingreso" debe ser una fecha válida.',
            'fech_ing.after_or_equal' => 'La "Fecha de Ingreso" debe ser igual o posterior a la "Fecha de Nacimiento".',
            'fech_baj.date' => 'El campo "Fecha de Baja" debe ser una fecha válida.',
            'fech_baj.after' => 'La "Fecha de Baja" debe ser posterior a la "Fecha de Ingreso".',

            // Validaciones para otros campos
            'item.required' => 'El campo "Ítem" es obligatorio.',
            'item.max' => 'El campo "Ítem" no debe superar los 8 caracteres.',
            'tipo_contrato_id.required' => 'El campo "Tipo de Contrato" es obligatorio.',
            'tipo_contrato_id.exists' => 'El "Tipo de Contrato" seleccionado no es válido.',
            'horario_id.required' => 'El campo "Horario" es obligatorio.',
            'horario_id.exists' => 'El "Horario" seleccionado no es válido.',
            'lugar_trabajo_id.required' => 'El campo "Lugar de Trabajo" es obligatorio.',
            'lugar_trabajo_id.exists' => 'El "Lugar de Trabajo" seleccionado no es válido.',
            'cargo_id.required' => 'El campo "Cargo" es obligatorio.',
            'cargo_id.exists' => 'El "Cargo" seleccionado no es válido.',
            'genero_id.required' => 'El campo "Género" es obligatorio.',
            'genero_id.exists' => 'El "Género" seleccionado no es válido.',

            // Validaciones para campos booleanos
            'kardex_visible.boolean' => 'El campo "Kardex Visible" debe ser verdadero o falso.',
            'auto_sabados.boolean' => 'El campo "Auto Sábados" debe ser verdadero o falso.',
            'biometrico_registro.boolean' => 'El campo "Registro Biométrico" debe ser verdadero o falso.',

            'biometrico_id.required_if' => 'Debe seleccionar un biométrico cuando el registro está activado',
            'biometrico_id.exists' => 'El biométrico seleccionado no existe en el sistema'
        ];
    }
}
