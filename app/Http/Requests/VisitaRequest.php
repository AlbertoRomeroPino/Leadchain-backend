<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitaRequest extends FormRequest
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
        $isPatch = $this->isMethod('patch');

        return [
            'id_usuario' => ($isPatch ? 'sometimes' : 'required') . '|exists:users,id',
            'id_cliente' => ($isPatch ? 'sometimes' : 'required') . '|exists:clientes,id',
            'fecha_hora' => ($isPatch ? 'sometimes' : 'required') . '|date',
            'id_estado' => ($isPatch ? 'sometimes' : 'required') . '|exists:estados_visita,id',
            'observaciones' => 'nullable|string',
        ];
    }
}
