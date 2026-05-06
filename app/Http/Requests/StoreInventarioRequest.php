<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioRequest extends FormRequest
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
        $rules = [
            'tipo' => 'required|string',
            'status' => 'required|string',
            'origen' => 'required|string|in:IMPORTADO,NACIONAL',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'serial' => 'nullable|string',
        ];

        // Conditional for Imported items
        if ($this->input('origen') === 'IMPORTADO') {
            $rules['container_id'] = 'required|exists:containers,id';
            $rules['codInv'] = 'nullable|string|unique:inventarios,codInv';
            $rules['expediente'] = 'required|string';
        } else {
            // National: These are nullable.
            $rules['container_id'] = 'nullable';
            $rules['codInv'] = 'nullable';
            $rules['expediente'] = 'nullable';
        }

        if ($this->input('tipo') === 'AUTOPARTE') {
            $rules['categorie'] = 'required|string';
            $rules['cantidad'] = 'required|integer|min:1';
            $rules['costo'] = 'required';
            $rules['price'] = 'required';
        } else {
            // Motores / Cámaras / Cajas
            $rules['item'] = 'required|string';
            $rules['año'] = 'required|string';
            $rules['condicion'] = 'required|string';
            $rules['price'] = 'required';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $tipo = $this->input('tipo');
        if ($tipo === 'AUTOPARTE') {
            $this->merge([
                'item' => trim(($tipo ?? '') . ' ' . ($this->input('categorie') ?? '')),
            ]);
        } else {
            $this->merge([
                'item' => trim(($tipo ?? '') . ' ' . ($this->input('marca') ?? '') . ' ' . ($this->input('modelo') ?? '')),
            ]);
        }
    }

    public function messages()
    {
        return [
            'codInv.unique' => 'El Código de Inventario ya existe.',
            'container_id.required' => 'El contenedor es obligatorio para este tipo de ítem.',
            'categorie.required' => 'La descripción/categoría es obligatoria para autopartes.',
        ];
    }
}
