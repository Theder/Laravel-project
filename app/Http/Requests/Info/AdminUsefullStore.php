<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;

class AdminUsefullStore extends FormRequest
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
            'title'     => 'required|string|min:5|max:255',
            'text'      => 'required|string|min:5',
            'link'      => 'required|string|min:5|max:255',
            'thumbnail' => 'required|file|image'
        ];
    }
}