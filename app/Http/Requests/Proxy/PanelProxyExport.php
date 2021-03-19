<?php

namespace App\Http\Requests\Proxy;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ProxyIdsRule;

class PanelProxyExport extends FormRequest
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
            'proxy_ids'     => [
                'required', 'string', new ProxyIdsRule
            ],
            'is_default'    => 'required|numeric|in:0,1',
            'export_type'   => 'required|numeric|in:0,1,2',
            'template'      => 'required_if:is_default, 1|string'
        ];
    }
}
