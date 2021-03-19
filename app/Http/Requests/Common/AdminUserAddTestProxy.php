<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProxyIdsRule;
use Illuminate\Validation\Rule;

class AdminUserAddTestProxy extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'proxy_ids'         => [
                'required', 'string', new ProxyIdsRule
            ],
            'duration_value'    => 'required|numeric|gte:0',
            'duration_type'     => [
                'required', 'string', Rule::in(['Minute', 'Hour', 'Day'])
            ]
        ];
    }
}
