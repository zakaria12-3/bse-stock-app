<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_number' => ['nullable', 'string', 'max:255', 'unique:purchases,invoice_number'],
            'purchase_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string'],
            // 'status' is handled by controller (default: draft)
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.selling_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Veuillez ajouter au moins un article.',
            'items.*.product_id.required' => 'La piece est obligatoire.',
            'items.*.quantity.min' => 'La quantite doit etre au moins egale a 1.',
        ];
    }
}
