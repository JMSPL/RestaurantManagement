<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|regex:/^[a-zA-Z ]+$/',
            'nif' => 'string|required|size:9|regex:/^[0-9]{9}$/',
            'num_customers' => 'required|numeric',
        ];
    }
}
