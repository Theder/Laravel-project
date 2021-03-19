<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Plan;
use App\Models\Proxy;
use App\Models\Payment\Coupon;
use App\Helpers\Payment\PayPalGateway;
use App\Models\Payment\Subscription;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Payment\PanelPlanActivate;
use App\Models\Payment\PayPalMethod;

class PlanController extends Controller
{
    /**
     * Render page with list of plans
     * 
     * @return \Illuminate\Http\Responce
     */
    public function index()
    {
        $plans = Plan::all();
        $proxies = Proxy::all();

        return view('panel.plans.list', compact('plans', 'proxies'));
    }

    /**
     * Activate selected plan
     * 
     * @param \App\Http\Requests\Payment\PanelPlanActivate  $request
     * @param \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Responce
     */
    public function activate(PanelPlanActivate $request, Plan $plan)
    {
        $data = $request->validated();

        $availableProxiesCount = Proxy::where('type', $plan->proxy_type)
            ->where('subscription_id', null)
            ->where('user_id', null)
            ->count();
        
        if ($data['amount'] > $availableProxiesCount) {
            return redirect()->back()->withErrors(['msg' => 'Current availiable amount of proxy is ' . $availableProxiesCount]);
        }

        $coupon = null;
        if (!empty($requestData['with_coupon'])) {
            $code = $requestData['coupon_code'];

            $coupon = Coupon::where('key', $code)->first();

            if ($coupon->plan->id != $plan->id) {
                return redirect()->back()->withErrors([
                    'not_valid_for_plan' => 
                        'Coupon not valid for plan ' . $plan->name . '. Try it for ' . $coupon->plan->name,
                ]);
            }
        }

        $data = [
            'plan_id'           => $plan->id,
            'plan_name'         => $plan->name,
            'plan_description'  => $plan->description,
            'plan_price'        => $plan->price,
            'amount'            => $data['amount']
        ];

        if (!empty($coupon)) {
            $data['coupon_id'] = $coupon->id;
        }

        $subscription = new Subscription(new PayPalMethod());
        $gateWayData = $subscription->subscribe($data);

        return redirect($gateWayData['redirect_url']);
    }

    /**
     * Success return url handler
     * 
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Responce
     */
    public function success(Request $request, Plan $plan)
    {
        $data = $request->all();

        if (empty($data['token']))
            abort(404);

        $payPalGateWay = new PayPalGateway();

        $token = $data['token'];
        $agreementId = $payPalGateWay->executeAgreement($token);

        $subscription = new PayPalSubscription();
        $subscription->fill([
            'name'          => $plan->name,
            'paypal_id'     => $agreementId,
            'paypal_status' => 'Pending payment',
            'plan_id'       => $plan->id,
            'amount'        => $data['amount']
        ]);

        if (!empty($data['coupon_id']))
            $subscription->coupon_id = $data['coupon'];

        $subscription->save();

        Auth::user()->payPalSubscriptions()->save($subscription);

        return redirect(route('plans.list'));
    }
}
