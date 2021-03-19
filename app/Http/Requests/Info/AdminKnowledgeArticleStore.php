<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Info\KnowledgeCategory;

class AdminKnowledgeArticleStore extends FormRequest
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
            'title'         => 'required|string|min:5|max:255',
            'text'          => 'required|string|min:5',
            'order'         => 'numeric',
            'category_id'   => [
                'required', 'numeric', Rule::in(KnowledgeCategory::pluck('id')),
            ]
        ];
    }
}
