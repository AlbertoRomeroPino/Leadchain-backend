<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
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
        $cliente = $this->route('cliente');
        $clienteId = $cliente ? $cliente->id : $cliente;
        return [
            'nombre' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:150',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('clientes', 'email')->ignore($clienteId)
            ],
            'telefono' => 'sometimes|string|max:20',
        ];
    }
}
