<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\Payment\PayPalGateway;
use App\Models\Payment\PayPalSubscription;
use App\Models\Payment\Invoice;
use App\Models\Payment\Order;
use App\Models\Proxy;

class PaymentController extends Controller
{
    /**
     * Paypal webhook handler
     */
    public function paypalNotify(Request $request)
    {
        $data = $request->all();
        
        $payPalGateway = new PayPalGateway();
        $verifyResult = $payPalGateway->webhookVerify($data);

        
        if ($verifyResult->verification_status != 'SUCCESS') {
            Log::error($data);
            abort(500);
        }
        

        switch ($data['event_type']) {
            case PayPalGateway::SUBSCRIPTION_CREATED: 
                $this->subscriptionCreated($data);
                break;
            case PayPalGateway::SUBSCRIPTION_CANCELED:
                $this->subscriptionCanceled($data);
                break;
            case PayPalGateway::SUBSCRIPTION_FAILED:
                $this->subscriptionFailed($data);
                break;
            case PayPalGateway::PAYMENT_SALE_COMPLETED:
                $this->subscriptionPaid($data);
                break;
            case PayPalGateway::PAYMENT_SALE_DENIED:
                $this->subscriptionPaymentDenied($data);
                break;
        }
    }

    protected function subscriptionCreated($data) 
    {

    }

    protected function subscriptionCanceled($data)
    {

    }

    protected function subscriptionFailed($data)
    {
        
    }

    protected function subscriptionPaid($data)
    {
        
    }

    protected function subscriptionPaymentDenied($data)
    {
        
    }   
}
