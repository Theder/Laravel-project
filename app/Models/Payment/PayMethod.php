<?php

namespace App\Models\Payment;

interface PayMethod 
{    
    /**
     * Create subscription 
     * 
     * @param array  $data
     */
    public function subscribe($data);

    /**
     * Cancel spesific subscription by paymethod subscription id
     * 
     * @param int  $subscriptionId
     */
    public function cancel($subscriptionId);

    /**
     * Refund payment 
     * 
     * @param int  $subscriptionId
     * @param int  $amount
     * @return array
     */
    public function refund($subscriptionId, $amount);

    /**
     * Get pay method name
     * 
     * @return string
     */
    public function getMethodName();
}