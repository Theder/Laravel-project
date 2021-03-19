<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminFaqCategoryUpdate extends FormRequest
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
            'name'  => 'required|string|min:5|max:255',
            'order' => 'numeric',
            'icon'  => 'string',
            'slug'  => [
                'required', 'string', 'min:5', 'max:255', Rule::unique('faq_categories')
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
