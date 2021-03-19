<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\KnowledgeCategory;
use Illuminate\Support\Str;
use App\Http\Requests\Info\AdminKnowledgeCategoryStore;
use App\Http\Requests\Info\AdminKnowledgeCategoryUpdate;

class KnowledgeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $knowledgeCategory = KnowledgeCategory::all();

        return view('admin.info.knowledgeCategory.index', compact('knowledgeCategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.info.knowledgeCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminKnowledgeCategoryStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminKnowledgeCategoryStore $request)
    {
        $data = $request->validated();

        KnowledgeCategory::create([
            'name'  => $data['name'],
            'order' => $data['order'] ? $data['order'] : 0,
            'slug'  => Str::slug($data['name'], '-'),
            'icon'  => $data['icon']
        ]);

        return redirect()->route('knowledgeCategory.index')
            ->with(['status' => 'New Category Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @return \Illuminate\Http\Response
     */
    public function show(KnowledgeCategory $knowledgeCategory)
    {
        return view('admin.info.knowledgeCategory.show', compact('knowledgeCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(KnowledgeCategory $knowledgeCategory)
    {
        return view('admin.info.knowledgeCategory.edit', compact('knowledgeCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminKnowledgeCategoryUpdate  $request
     * @param  \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(AdminKnowledgeCategoryUpdate $request, KnowledgeCategory $knowledgeCategory)
    {
        $data = $request->validated();

        $knowledgeCategory->update([
            'name'  => $data['name'],
            'order' => $data['order'],
            'slug'  => $data['slug'],
            'icon'  => $data['icon']
        ]);

        return redirect()->route('knowledgeCategory.show', ['knowledgeCategory' => $knowledgeCategory->id])
            ->with(['status' => 'Category successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(KnowledgeCategory $knowledgeCategory)
    {
        $knowledgeCategory->delete();

        return redirect()->route('knowledgeCategory.index')
            ->with(['status' => 'Category successfully deleted.']);
    }
}
