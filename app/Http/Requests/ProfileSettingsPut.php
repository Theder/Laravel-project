<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Enum\CountryList;
use Illuminate\Support\Facades\Auth;

class ProfileSettingsPut extends FormRequest
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
            'avatar' => [
                'nullable', 'image', 'max:2048'
            ], 
            'password' => [
                'nullable', 'confirmed', 'string', 'min:8'
            ],
            'first_name' => [
                'nullable', 'string', 'max:255'
            ],
            'last_name' => [
                'nullable', 'string', 'max:255'
            ],
            'email' => [
                'nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())
            ],
            'phone' => [
                'nullable', 'string', 'max:255'
            ],
            'country' => [
                'nullable', Rule::in(array_keys(CountryList::$countries))
            ],
            'state' => [
                'nullable', 'string', 'max:255'
            ],
            'zip' => [
                'nullable', 'string', 'max:255'
            ], 
            'address' => [
                'nullable', 'string', 'max:255'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() 
    {
        return [

        ];
    }
}
