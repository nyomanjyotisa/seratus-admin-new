<?php

namespace App\Http\Requests\Production;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class ProductionStoreRequest extends FormRequest
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
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
        ];
    }
}
