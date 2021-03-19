<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Proxy\Proxy;

class AdminPlanUpdate extends FormRequest
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
            'name'              => 'required|string|min:5|min:255',
            'description'       => 'required|string|min:5|min:255',
            'badge_text'        => 'required|string|min:5|min:255',
            'price'             => 'required|numeric|integer|gt:0',
            'proxy_type'        => [
                'required', 'string', Rule::in([Proxy::TYPE_LIMITED, Proxy::TYPE_UNLIMITED])
            ],
            'additional_text'   => 'required|string|min:5|min:255',
            'icon'              => 'string|min:5|min:255'
        ];
    }
}
