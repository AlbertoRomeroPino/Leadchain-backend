<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EdificioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'direccion_completa' => 'required|string|max:255',
                'ubicacion' => 'required|array',
                'ubicacion.lat' => 'required|numeric|between:-90,90',
                'ubicacion.lng' => 'required|numeric|between:-180,180',
                'id_zona' => 'required|exists:zonas,id',
                'tipo' => 'required|string|max:50',
                'id_cliente' => 'nullable|exists:clientes,id',
            ];
        }

        return [
            'direccion_completa' => 'sometimes|string|max:255',
            'ubicacion' => 'sometimes|array',
            'ubicacion.lat' => 'required_with:ubicacion,ubicacion.lng|numeric|between:-90,90',
            'ubicacion.lng' => 'required_with:ubicacion,ubicacion.lat|numeric|between:-180,180',
            'id_zona' => 'sometimes|exists:zonas,id',
            'tipo' => 'sometimes|string|max:50',
            'id_cliente' => 'nullable|exists:clientes,id',
        ];
    }
}
