<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Plan;
use App\Http\Requests\Payment\AdminPlanStore;
use App\Http\Requests\Payment\AdminPlanUpdate;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::all();

        return view('admin.payment.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payment.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Payment\AdminPlanStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminPlanStore $request)
    {
        $data = $request->validated();

        Plan::create([
            'name'              => $data['name'],
            'description'       => $data['description'],
            'badge_text'        => $data['badge_text'],
            'price'             => $data['price'],
            'proxy_type'        => $data['proxy_type'],
            'additional_text'   => $data['additional_text'],
            'icon'              => $data['icon']
        ]);

        return redirect()->route('plans.index')
            ->with(['status' => 'New plan successfully created.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        return view('admin.payment.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        return view('admin.payment.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Payment\AdminPlanUpdate  $request
     * @param  \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(AdminPlanUpdate $request, Plan $plan)
    {
        $data = $request->validated();

        $plan->update([
            'name'              => $data['name'],
            'description'       => $data['description'],
            'badge_text'        => $data['badge_text'],
            'price'             => $data['price'],
            'proxy_type'        => $data['proxy_type'],
            'additional_text'   => $data['additional_text'],
            'icon'              => $data['icon'],
        ]);

        return redirect()->route('plans.show', ['plan' => $plan->id])
            ->with(['status' => 'Plan #' . $plan->id . ' successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('plans.index')  
            ->with(['status' => 'Plan #' . $plan->id . ' successfully deleted.']);
    }
}
