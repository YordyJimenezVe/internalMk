<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventarioRequest extends FormRequest
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
        $id = $this->route('id'); // Retrieve 'id' parameter from route

        $rules = [
            'tipo' => 'required|string',
            'status' => 'required|string',
            'origen' => 'required|string|in:IMPORTADO,NACIONAL',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'serial' => 'required|string',
            'costo_importacion_unitario' => 'nullable|numeric',
            'price_sale' => 'nullable|string',
            'observation' => 'nullable|string',
            'serial_file' => 'nullable|image|max:2048',
        ];

        // Conditional for Imported items
        if ($this->input('origen') === 'IMPORTADO') {
            $rules['container_id'] = 'required|exists:containers,id';
            $rules['codInv'] = 'nullable|string|unique:inventarios,codInv,' . $id;
            $rules['expediente'] = 'required|string';
        } else {
            $rules['container_id'] = 'nullable';
            $rules['codInv'] = 'nullable';
            $rules['expediente'] = 'nullable';
        }

        if ($this->input('tipo') === 'AUTOPARTE') {
            $rules['categorie'] = 'required|string';
            $rules['cantidad'] = 'required|integer|min:1';
            $rules['costo'] = 'required';
            $rules['price'] = 'nullable';
        } else {
            // Motores / Cámaras / Cajas
            $rules['item'] = 'required|string';
            $rules['año'] = 'required|string';
            $rules['condicion'] = 'required|string';
            $rules['price'] = 'nullable';
        }

        return $rules;
    }

    private function cleanCurrency($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(' ', '', $value);

        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            $lastDot = strrpos($value, '.');
            $lastComma = strrpos($value, ',');
            if ($lastDot > $lastComma) {
                $value = str_replace(',', '', $value);
            } else {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            }
        } else {
            if (strpos($value, ',') !== false) {
                if (preg_match('/,\d{2}$/', $value)) {
                    $value = str_replace(',', '.', $value);
                } else {
                    $value = str_replace(',', '', $value);
                }
            }
            if (strpos($value, '.') !== false) {
                if (substr_count($value, '.') > 1) {
                    $value = str_replace('.', '', $value);
                } else {
                    if (preg_match('/\.\d{2}$/', $value)) {
                        // Keep single dot as decimal separator
                    } else {
                        $value = str_replace('.', '', $value);
                    }
                }
            }
        }

        return $value;
    }

    protected function prepareForValidation()
    {
        $tipo = $this->input('tipo');

        if ($this->has('marca')) {
            $this->merge(['marca' => strtoupper($this->input('marca'))]);
        }
        if ($this->has('modelo')) {
            $this->merge(['modelo' => strtoupper($this->input('modelo'))]);
        }
        if ($this->has('serial')) {
            $this->merge(['serial' => strtoupper($this->input('serial'))]);
        }

        if ($this->has('price')) {
            $this->merge(['price' => $this->cleanCurrency($this->input('price'))]);
        }
        if ($this->has('price_sale')) {
            $this->merge(['price_sale' => $this->cleanCurrency($this->input('price_sale'))]);
        }
        if ($this->has('costo_importacion_unitario')) {
            $this->merge(['costo_importacion_unitario' => $this->cleanCurrency($this->input('costo_importacion_unitario'))]);
        }

        // Default price if not provided
        if (!$this->has('price') || $this->input('price') === '' || $this->input('price') === null) {
            $this->merge(['price' => '0.00']);
        }

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
