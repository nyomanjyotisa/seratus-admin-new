<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SaleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'transaction_id' => 'nullable',
            'source' => 'nullable',
            'payment_channel' => 'nullable',
            'payment_type' => 'nullable',
            'proof_of_payment' => 'nullable',
            'amount' => 'required',
            'price' => 'nullable',
            'description' => 'required',
            'date' => 'required',
        ];
    }
}
