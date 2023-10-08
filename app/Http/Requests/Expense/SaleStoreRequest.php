<?php

namespace App\Http\Requests\Sale;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
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
            'transaction_id' => 'required',
            'source' => 'required',
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
