<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Coupon;
use App\Models\Payment\Plan;
use App\Http\Requests\Payment\AdminCouponStore;
use App\Http\Requests\Payment\AdminCouponUpdate;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::all();

        return view('admin.payment.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plans = Plan::all();

        return view('admin.payment.coupons.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Payment\AdminCouponStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminCouponStore $request)
    {
        $data = $request->validated();

        Coupon::create([
            'key'       => $data['key'],
            'type'      => $data['type'],
            'value'     => $data['value'],
            'plan_id'   => $data['plan_id']
        ]);

        return redirect()->route('coupons.index')
            ->with(['status' => 'New coupon successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return view('admin.payment.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $plans = Plan::all();

        return view('admin.payment.coupons.edit', compact('coupon', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Payment\AdminCouponUpdate  $request
     * @param  \App\Models\Payment\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(AdminCouponUpdate $request, Coupon $coupon)
    {
        $data = $request->validated();

        $coupon->update([
            'key'       => $data['key'],
            'type'      => $data['type'],
            'value'     => $data['value'],
            'plan_id'   => $data['plan_id']
        ]); 

        return redirect()->route('coupons.show', ['coupon' => $coupon->id])
            ->with(['status' => 'Coupon #' . $coupon->id . ' successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupons.index')
            ->with(['status' => 'Coupon #' . $coupon->id . ' successfully deleted']);
    }
}
