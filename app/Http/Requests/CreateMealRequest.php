<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateMealRequest extends FormRequest
{

    public function authorize(Request $request)
    {
        return $request->user()->type == "waiter";
    }


    public function rules()
    {
        return [
            'table_number' => 'required',
            'responsible_waiter_id' => 'required',
        ];
    }
}