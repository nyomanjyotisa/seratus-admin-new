<?php

namespace App\Http\Requests\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'unique_code' => 'nullable|string|max:255|unique:' . Transaction::class,
            'description' => 'required|string',
            'date' => 'required',
            'source' => 'required',
        ];
    }
}
