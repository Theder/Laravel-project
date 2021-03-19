<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Subscription;
use App\Models\User;
use App\Models\Payment\Coupon;
use App\Models\Payment\Invoice;
use Carbon\Carbon;
use App\Enums\MonthList;

class Order extends Model
{
    use HasFactory;

    /**
     * Order statuses
     */
    const STATUS_ACTIVE     = 'Active';
    const STATUS_CANCELED   = 'Canceled';
    const STATUS_FAILED     = 'Failed';
    const STATUS_PENDING    = 'Pending payment';
    const STATUS_REFUNDED   = 'Refund';

    protected $fillable = [
        'subscription_id', 'user_id', 'total_price', 'coupon_id', 'invoice_id', 'trunsaction_id',
        'status'
    ];

    public function subscription()
    {
        return $this->belongsTo(PayPalSubscription::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);   
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get order's user total spends
     * 
     * @return int
     */
    public function totalValue()
    {
        $orders = $this->user->orders;

        $sum = 0;
        foreach ($orders as $order) {
            $sum += $order->total_price;
        }

        return $sum;
    }

    /**
     * Order refund
     * 
     * @return void
     */
    public function refund() 
    {
        $refundDetails = $this->subscription->refund($this->trunsaction_id, $this->total_price);

        if (!empty($refundDetails)) {
            $this->status = 'Refunded';
            $this->save();
        }
    }

    /**
     * Group orders by month for currenct year
     * 
     * @return array
     */
    public static function getOrdersGroupByMonth() 
    {
        $ordersGroupByMonth = self::select('id', 'created_at')
            ->whereRaw('year(`created_at`) = ?', array(date('Y')))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $ordersCount = [];
        $orderArr = [];
        foreach ($ordersGroupByMonth as $key => $value) {
            $ordersCount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($ordersCount[$i])) {
                $orderArr[$i]['count'] = $ordersCount[$i];
            } else {
                $orderArr[$i]['count'] = 0;
            }
            $orderArr[$i]['month'] = MonthList::$months[$i - 1];
        }

        return $orderArr;
    }
}
