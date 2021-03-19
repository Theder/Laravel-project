<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment\Coupon;
use App\Models\Payment\Plan;

class AdminCouponUpdate extends FormRequest
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
            'key' => [
                'required', 'string', 'min:3', 'max:255', Rule::in(Coupon::pluck('id'))
            ],
            'type' => [
                'required', 'string', Rule::in([Coupon::TYPE_DISCOUNT, Coupon::TYPE_TRIAL])
            ],
            'value' => 'required',
            'plan_id' => [
                'required', 'numeric', Rule::in(Plan::pluck('id'))
            ]
        ];
    }
}
