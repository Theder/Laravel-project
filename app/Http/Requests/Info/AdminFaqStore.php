<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;

class AdminFaqStore extends FormRequest
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
            'question'  => 'required|string|min:5|max:255',
            'answer'    => 'required|string|min:10',
            'order'     => 'numeric',
        ];
    }
}
