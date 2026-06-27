<?php

namespace App\Http\Requests;

use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'build_name' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'status' => ['nullable', Rule::enum(SaleStatus::class)],
            'notes' => ['nullable', 'string'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'change' => ['nullable', 'numeric', 'min:0'],
            'global_discount' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.labor_hours' => ['nullable', 'numeric', 'min:0'],
            'items.*.labor_rate' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.product_id.exists' => 'La piece selectionnee n existe pas.',
            'items.*.quantity.min' => 'La quantite doit etre au moins egale a 1.',
            'items.*.unit_price.min' => 'Le prix unitaire doit etre au moins egal a 0.',
            'items.*.discount.min' => 'La remise doit etre au moins egale a 0.',
            'items.*.labor_hours.min' => 'Les heures de pose doivent etre au moins egales a 0.',
            'items.*.labor_rate.min' => 'Le tarif horaire doit etre au moins egal a 0.',
        ];
    }
}
