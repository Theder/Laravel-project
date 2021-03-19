<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Enum\CountryList;

class AdminUserBussinessUpdate extends FormRequest
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
            'email'         => 'required|string|email',
            'first_name'    => 'string|max:255',
            'last_name'     => 'string|max:255',
            'phone'         => 'string',
            'country'       => [
                'string', Rule::in(CountryList::$countries),
            ],
            'state'         => 'string|max:255',
            'zip'           => 'numeric',
            'city'          => 'string|max:255',
            'address'       => 'string|max:255'
        ];
    }
}
