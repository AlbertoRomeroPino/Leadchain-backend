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
        return [
            'direccion_completa' => 'required|string|max:255',
            'planta' => 'nullable|string|max:20',
            'puerta' => 'nullable|string|max:10',
            'ubicacion' => 'required',
            'id_zona' => 'required|exists:zonas,id',
            'tipo' => 'required|string|max:50',
            'id_cliente' => 'nullable|exists:clientes,id',
        ];
    }
}
