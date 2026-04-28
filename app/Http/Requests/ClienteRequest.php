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

        if ($this->isMethod('post')) {
            return [
                'nombre' => 'required|string|max:50',
                'apellidos' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'max:100',
                    Rule::unique('clientes', 'email')
                ],
                'telefono' => 'required|string|max:15',
            ];
        }

        return [
            'nombre' => 'sometimes|string|max:50',
            'apellidos' => 'sometimes|string|max:100',
            'email' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('clientes', 'email')->ignore($clienteId)
            ],
            'telefono' => 'sometimes|string|max:15',
        ];
    }
}
