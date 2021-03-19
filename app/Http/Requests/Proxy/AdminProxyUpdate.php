<?php

namespace App\Http\Requests\Proxy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Proxy\Proxy;

class AdminProxyUpdate extends FormRequest
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
            'ip_port'       => 'required|string|min:9|max:21',
            'login'         => 'required|string|min:5|min:40',
            'password'      => 'required|string|min:5|min:40',
            'type'          => [
                'required', 'string', Rule::in([Proxy::TYPE_LIMITED, Proxy::TYPE_UNLIMITED])
            ],
            'rotation_time' => 'numeric|gte:0',
            'latency'       => 'numeric|gte:0'
        ];
    }
}
