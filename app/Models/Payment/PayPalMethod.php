<?php

namespace App\Models\Payment;

use Exception;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use \PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\AgreementStateDescriptor;
use Illuminate\Support\Facades\Log;
use App\Models\Payment\Coupon;
use App\Models\Setting;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PayPal\Api\Amount;

class PayPalMethod implements PayMethod
{
    private $apiContext;
    private $webhookId;

    /**
     * Webhook events
     */
    const SUBSCRIPTION_CREATED      = 'BILLING.SUBSCRIPTION.CREATED';
    const SUBSCRIPTION_CANCELED     = 'BILLING.SUBSCRIPTION.CANCELLED';
    const SUBSCRIPTION_FAILED       = 'BILLING.SUBSCRIPTION.PAYMENT.FAILED';
    const PAYMENT_SALE_COMPLETED    = 'PAYMENT.SALE.COMPLETED';
    const PAYMENT_SALE_DENIED       = 'PAYMENT.SALE.DENIED';

    public function __construct()
    {
        $mode = Setting::where('name', 'paypal_mode')->first()->value;

        if ($mode == 'sandbox') {
            $client_id  = Setting::where('name', 'paypal_sandbox_client_id')->first()->value;
            $secret     = Setting::where('name', 'paypal_sandbox_secret')->first()->value;

            $this->apiContext = new ApiContext(new OAuthTokenCredential($client_id, $secret));
        }
        else if ($mode == 'live') {
            $client_id  = Setting::where('name', 'paypal_live_client_id')->first()->value;
            $secret     = Setting::where('name', 'paypal_live_secret')->first()->value;

            $this->apiContext = new ApiContext(new OAuthTokenCredential($client_id, $secret));
        }

        $this->webhookId = Setting::where('name', 'paypal_webhook_id')->first()->value;
    }

    /**
     * Create subscription 
     * 
     * @param array  $data
     */
    public function subscribe($data)
    {
        $createdPlan = $this->createBillingPlan($data);
        $redirectUrl = $this->activateBillingPlan($createdPlan, $data);
        
        $returnData = [
            'paypal_id'     => $createdPlan->getId(),
            'redirect_url'  => $redirectUrl,
            'amount'        => $data['amount']
        ];
        return $returnData;
    }

    /**
     * Cancel spesific subscription by paymethod subscription id
     * 
     * @param int  $subscriptionId
     */
    public function cancel($paypalId)
    {
        $agreementStateDescriptor = new AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("Canceling the agreement");

        try {        
            $agreement = Agreement::get($paypalId, $this->apiContext);

            $agreement->cancel($agreementStateDescriptor, $this->apiContext);
        } catch (Exception $ex) {        
            dd($ex);
        }
    }

    /**
     * Refund payment 
     * 
     * @param int  $subscriptionId
     * @param int  $amount
     * @return array
     */
    public function refund($paypalId, $amount)
    {
        $sale = new Sale();
        $sale->setId($paypalId);

        $amt = new Amount();
        $amt->setCurrency('USD')->setTotal($amount);

        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($amt);

        try {
            $refundedSale = $sale->refundSale($refundRequest, $this->apiContext);
            
            return $refundedSale;
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Fill up the basic information that is required for the plan
     * 
     * @param array  $data
     * @return \PayPal\Api\Plan
     */
    private function createBillingPlan($data)
    {
        $plan = new Plan();
        $totalPrice = $data['plan_price'] * $data['amount'];

        $plan->setName($data['plan_name'])
            ->setDescription($data['plan_description'])
            ->setType('INFINITE');

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName($data['plan_name'])
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval("1")
            ->setAmount(new Currency(array('value' => $totalPrice, 'currency' => 'USD')));

        $merchantPreferences = new MerchantPreferences();

        $successUrlData = [
            'plan'      => $data['plan_id'],
            'amount'    => $data['amount'],
        ];

        if (!empty($data['coupon_id'])) {
            $successUrlData['coupon'] = $data['coupon_id'];
        }

        $merchantPreferences->setReturnUrl(route('plans.activate.success', $successUrlData))
            ->setCancelUrl(route('plans.list', ['cancel' => 1]))
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("0");
        
        $coupon = null;
        if (isset($data['coupon_id'])) {
            $coupon = Coupon::find($data['coupon_id']);
        }

        if (!empty($coupon) && $coupon->type == 'discount') {
            $price = $totalPrice - ($totalPrice * ($coupon->value / 100));
            $price = number_format($price, 2);

            $merchantPreferences->setSetupFee(new Currency(array(
                'value' => $price,
                'currency' => 'USD'
            )));
        } 
        else if (empty($coupon)) {
            $merchantPreferences->setSetupFee(new Currency(array(
                'value' => $totalPrice,
                'currency' => 'USD'
            )));
        }
        
        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);

        try {
            $createdplan = $plan->create($this->apiContext);

            return $createdplan;
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Retrieving the Plan from the Create Update Sample. 
     * 
     * @param \PayPal\Api\Plan  $createdPlan
     * @param array  $data
     * @return null|string
     */
    private function activateBillingPlan($createdPlan, $data)
    {
        try {
            $patch = new Patch();
            $value = new PayPalModel('{"state":"ACTIVE"}');
            $patch->setOp('replace')
                ->setPath('/')
                ->setValue($value);
            $patchRequest = new PatchRequest();
            $patchRequest->addPatch($patch);
            $createdPlan->update($patchRequest, $this->apiContext);
            $patchedPlan = Plan::get($createdPlan->getId(), $this->apiContext);
            
            $redirectUrl = $this->createBillingAgreement($patchedPlan, $data);
            return $redirectUrl;
        } catch (PayPalConnectionException $ex) {
            dd($ex);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Retrieving the Agreement object from Create Agreement
     * 
     * @param \PayPal\Api\Plan  $createdPlan
     * @param array  $data
     * @return null|string
     */
    private function createBillingAgreement($createdPlan, $data)
    {
        $agreement = new Agreement();
        $agreement->setName('Base Agreement')
            ->setDescription('Basic Agreement');

        $coupon = null;
        if (isset($data['coupon_id'])) {
            $coupon = Coupon::find($data['coupon_id']);
        }

        if (!empty($coupon) && $coupon->type == 'trial') {
            $startDate = date('c', time() + 86400 * $coupon->value);
            $agreement->setStartDate($startDate);
        } 
        else {
            $agreement->setStartDate(date('c', time() + 86400 * 30));
        }        

        $plan = new Plan();
        $plan->setId($createdPlan->getId());
        $agreement->setPlan($plan);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        try {
            $agreement = $agreement->create($this->apiContext);
            $approvalUrl = $agreement->getApprovalLink();

            return $approvalUrl;
        } catch(Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Validate and execute the PayPal Agreement 
     * 
     * @param mixed  $token
     * @return string
     */
    public function executeAgreement($token) 
    {
        $agreement = new \PayPal\Api\Agreement();

        try {
            $agreement->execute($token, $this->apiContext);
            return $agreement->getId();
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Verify webhook request
     * 
     * @return \PayPal\Api\VerifyWebhookSignatureResponse
     */
    public function webhookVerify()
    {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId($this->webhookId);
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);
        $bodyReceived = file_get_contents('php://input');

        $signatureVerification->setRequestBody($bodyReceived);

        try {
            $output = $signatureVerification->post($this->apiContext);
            return $output;
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }

    /**
     * Update PayPal Billing Plan price
     * 
     * @param int  $planId
     * @param int  $price
     * @return void
     */
    public function updateBillingPlanPrice($planId, $price)
    {
        try {
            $plan = Plan::get($planId, $this->apiContext);

            $patch = new Patch();

            $paymentDefinitions = $plan->getPaymentDefinitions();
            $paymentDefinitionId = $paymentDefinitions[0]->getId();
            $patch->setOp('replace')
                ->setPath('/payment-definitions/' . $paymentDefinitionId)
                ->setValue(json_decode(
                    '{
                            "name": "Updated Payment Definition",
                            "frequency": "Day",
                            "amount": {
                                "currency": "USD",
                                "value": "'. $price .'"
                            }
                    }'
                ));
            $patchRequest = new PatchRequest();
            $patchRequest->addPatch($patch);

            $plan->update($patchRequest, $this->apiContext);
        } catch (Exception $ex) {        
            dd($ex);
        }
    }

    /**
     * Get pay method name
     * 
     * @return string
     */
    public function getMethodName()
    {
        return 'PayPal';
    }
}