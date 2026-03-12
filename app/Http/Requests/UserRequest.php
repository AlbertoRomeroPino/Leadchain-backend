<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'email' => 'required|email|max:255|unique:users,email,' . $this->route('user'),
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'sometimes|string|min:8',
            'rol' => 'required|string|max:50',
            'id_responsable' => 'nullable|exists:users,id',
            'id_zona' => 'nullable|exists:zonas,id',
        ];
    }
}
