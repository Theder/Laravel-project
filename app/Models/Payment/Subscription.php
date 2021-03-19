<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Plan;
use App\Models\User;
use App\Models\Proxy\Proxy;
use App\Models\Payment\Order;
use App\Models\Payment\PayMethod;
use App\Models\Payment\PayPalMethod;

class Subscription extends Model
{
    use HasFactory;

    private PayMethod $payMethod;

    protected $fillable = [
        'plan_id', 'name', 'paymethod_data', 'ends_at', 'paymethod_status', 'amount', 'coupon_id',
        'paymethod_id', 'proxies_history', 'paymethod_name'
    ];

    public function __construct(PayMethod $payMethod)
    {
        $this->payMethod = $payMethod;
        $this->paymethod_name = $payMethod->getMethodName();
    }

    public function getPayMethod()
    {
        if ($this->paymethod_name == 'PayPal')
            return new PayPalMethod();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function proxies()
    {
        return $this->hasMany(Proxy::class, 'subscription_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'subscription_id');
    }

    /**
     * Create subscription
     * 
     * @param array  $data
     * @return array
     */
    public function subscribe($data)
    {
        return $this->getPayMethod()->subscribe($data);
    }

    /**
     * Cancel specific subscription
     * 
     * @return void
     */
    public function cancel() 
    {
        $this->getPayMethod()->cancel($this->paymethod_id);
        $this->paymethod_status = 'Canceled';
        $this->save();
    }

    /**
     * Check is specific subscription expired
     * 
     * @return bool
     */
    public function isExpired() 
    {
        return time() > strtotime($this->ends_at);
    }

    /**
     * Update specific subscription proxies history
     * 
     * @return void
     */
    public function updateProxiesHistory()
    {
        $this->refresh();
        $proxies = $this->proxies;
        $this->proxies_history = json_encode($proxies);
        $this->save();
    }

    /**
     * Remove proxy from subscription
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return void
     */
    public function removeProxy(Proxy $proxy)
    {
        $this->proxies->first(function ($proxyIter) use ($proxy) {
            return $proxyIter->id == $proxy->id;
        })->update([
            'subscription_id'       => null,
            'user_id'               => null,
            'is_action_required'    => true
        ]);
    }

    /**
     * Add proxy to subscription
     * 
     * @param int  $id
     * @return void
     */
    public function addProxy($proxyId)
    {
        Proxy::find($proxyId)->update([
            'subscription_id'   => $this->id,
            'user_id'           => $this->user[0]->id
        ]);
    }
}
