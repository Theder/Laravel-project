<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Order;
use App\Models\User;
use App\Http\Requests\Payment\AdminOrderUpdate;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();

        return view('admin.order.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $user = $order->user;

        return view('admin.order.show', compact('order', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Payment\AdminOrderUpdate  $request
     * @param  \App\Models\Payment\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(AdminOrderUpdate $request, Order $order)
    {
        $data = $request->validated();

        $order->subscription->paypal_status = $data['status'];
        $order->subscription->save();

        if ($data['status'] == Order::STATUS_CANCELED) {
            $order->subscription->cancel();
        }

        if ($data['status'] == Order::STATUS_REFUNDED) {
            $order->refund();
        }

        if ($data['status'] == 'Refund & Cancel') {
            $order->refund();
            $order->subscription->cancel();
        }
 
        return redirect()->back()->with(['status' => 'Status successfully changed.']);
    }

    /**
     * Return rendered user's order history
     * 
     * @param  \App\Models\Payment\Order  $order
     * @return string
     */
    public function orderHistory(User $user)
    {
        $orders = $user->orders;

        return view('admin.order.history', compact('orders'))->render();
    }

    /**
     * Refund the specific order
     * 
     * @param  \App\Models\Payment\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function refund(Order $order) 
    {
        $order->refund();

        return redirect()->back()->with(['status' => 'Order #' . $order->id . ' successfully refunded']);
    }

    /**
     * Refund and cancel the specific order
     * 
     * @param  \App\Models\Payment\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function refundAndCancel(Order $order)
    {
        $order->refund();
        $order->subscription->cancel();

        return redirect()->back()->with([
            'status' => 'Order #' . $order->id . ' successfully refunded. 
            Subscription #' . $order->subscription->id . ' successfully canceled'
        ]);
    }
}
