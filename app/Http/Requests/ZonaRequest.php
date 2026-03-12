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
        return [
            'nombre_zona' => 'required|string|max:100',
            'esquina_noroeste' => 'required|array',
            'esquina_noroeste.lat' => 'required|numeric|between:-90,90',
            'esquina_noroeste.lng' => 'required|numeric|between:-180,180',
            'esquina_noreste' => 'required|array',
            'esquina_noreste.lat' => 'required|numeric|between:-90,90',
            'esquina_noreste.lng' => 'required|numeric|between:-180,180',
            'esquina_suroeste' => 'required|array',
            'esquina_suroeste.lat' => 'required|numeric|between:-90,90',
            'esquina_suroeste.lng' => 'required|numeric|between:-180,180',
            'esquina_sureste' => 'required|array',
            'esquina_sureste.lat' => 'required|numeric|between:-90,90',
            'esquina_sureste.lng' => 'required|numeric|between:-180,180',
        ];
    }
}
