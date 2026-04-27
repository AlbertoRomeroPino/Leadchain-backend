<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZonaRequest extends FormRequest
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
            'nombre' => ($isPatch ? 'sometimes' : 'required') . '|string|max:50',
            'area' => ($isPatch ? 'sometimes' : 'required') . '|array|min:4',
            'area.*' => ($isPatch ? 'sometimes' : 'required') . '|array',
            'area.*.lat' => ($isPatch ? 'sometimes' : 'required') . '|numeric|between:-90,90',
            'area.*.lng' => ($isPatch ? 'sometimes' : 'required') . '|numeric|between:-180,180',
        ];
    }
}
