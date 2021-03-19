<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Plan;

class Coupon extends Model
{
    use HasFactory;

    const TYPE_DISCOUNT = 'discount';
    const TYPE_TRIAL    = 'trial';

    protected $fillable = [
        'key', 'type', 'value', 'plan_id'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
