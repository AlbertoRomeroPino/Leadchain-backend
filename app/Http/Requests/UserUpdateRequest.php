<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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

        $user = $this->route('user');
        $userId = $user ? $user->id : $user;

        return [

            'nombre' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:150',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:8',
            'rol' => 'sometimes|string|max:50',
            'id_responsable' => 'nullable|exists:users,id',
            'id_zona' => 'nullable|exists:zonas,id',
        ];
    }
}
