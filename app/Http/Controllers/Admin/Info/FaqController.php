<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\Faq;
use App\Models\Info\FaqCategory;
use App\Http\Requests\Info\AdminFaqStore;
use App\Http\Requests\Info\AdminFaqUpdate;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqCategories = FaqCategory::all();

        return view('admin.info.faq.index', compact('faqCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faqCategories = FaqCategory::all();

        return view('admin.info.faq.create', compact('faqCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminFaqStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminFaqStore $request)
    {
        $data = $request->validated();
        
        Faq::create([
            'question'      => $data['question'],
            'answer'        => $data['answer'],
            'order'         => isset($data['order']) ? $data['order'] : 0,
            'category_id'   => $data['category_id']
        ]);

        return redirect()->route('faq.index')->with(['status' => 'New FAQ Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Info\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        return view('admin.info.faq.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Info\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $faq)
    {
        $faqCategories = FaqCategory::all();

        return view('admin.info.faq.edit', compact('faq', 'faqCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminFaqUpdate  $request
     * @param  \App\Models\Info\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(AdminFaqUpdate $request, Faq $faq)
    {
        $data = $request->all();

        $faq->update([
            'question'      => $data['question'],
            'answer'        => $data['answer'],
            'order'         => $data['order'],
            'category_id'   => $data['category_id']
        ]);

        return redirect()->route('faq.show', ['faq' => $faq->id])
            ->with(['status' => 'FAQ successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Info\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('faq.index')->with(['status' => 'FAQ successfully deleted']);
    }
}
