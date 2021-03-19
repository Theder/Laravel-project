<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment\Order;

class AdminOrderUpdate extends FormRequest
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
            'status' => [
                'required', 'string', Rule::in([
                    Order::STATUS_ACTIVE, Order::STATUS_CANCELED, Order::STATUS_FAILED,
                    Order::STATUS_PENDING, Order::STATUS_REFUNDED, 'Refund & Cancel'
                ])
            ]
        ];
    }
}
