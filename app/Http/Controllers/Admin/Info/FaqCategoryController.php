<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\FaqCategory;
use Illuminate\Support\Str;
use App\Http\Requests\Info\AdminFaqCategoryStore;
use App\Http\Requests\Info\AdminFaqCategoryUpdate;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqCategories = FaqCategory::all();

        return view('admin.info.faqCategory.index', compact('faqCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.info.faqCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminFaqCategoryStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminFaqCategoryStore $request)
    {
        $data = $request->validated();

        FaqCategory::create([
            'name'  => $data['name'],
            'order' => isset($data['order']) ? $data['order'] : 0,
            'slug'  => Str::slug($data['name'], '-'),
            'icon'  => $data['icon']
        ]);
        
        return redirect()->route('faqCategory.index')
            ->with(['status' => 'New Category Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FaqCategory $faqCategory)
    {
        return view('admin.info.faqCategory.show', compact('faqCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(FaqCategory $faqCategory)
    {
        return view('admin.info.faqCategory.edit', compact('faqCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminFaqCategoryUpdate  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminFaqCategoryUpdate $request, FaqCategory $faqCategory)
    {
        $data = $request->validated();

        $faqCategory->update([
            'name'  => $data['name'],
            'order' => $data['order'],
            'slug'  => $data['slug'],
            'icon'  => $data['icon'] 
        ]);

        return redirect()->route('faqCategory.show', ['faqCategory' => $faqCategory->id])
            ->with(['status' => 'Category successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FaqCategory $faqCategory)
    {
        $faqCategory->delete();

        return redirect()->route('faqCategory.index')
            ->with(['status' => 'Category successfully deleted.']);
    }
}
