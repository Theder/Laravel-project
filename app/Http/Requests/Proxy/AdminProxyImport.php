<?php

namespace App\Http\Requests\Proxy;

use Illuminate\Foundation\Http\FormRequest;

class AdminProxyImport extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'proxies' => 'required|file|mimes:txt'
        ];
    }
}
