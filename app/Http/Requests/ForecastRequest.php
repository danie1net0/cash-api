<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForecastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'size:2'],
        ];
    }
}
