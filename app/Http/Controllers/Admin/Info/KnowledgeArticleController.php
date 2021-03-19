<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\KnowledgeArticle;
use App\Models\Info\KnowledgeCategory;
use Illuminate\Support\Str;
use App\Http\Requests\Info\AdminKnowledgeArticleStore;
use App\Http\Requests\Info\AdminKnowledgeArticleUpdate;

class KnowledgeArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $knowledgeCategories = KnowledgeCategory::all();

        return view('admin.info.article.index', compact('knowledgeCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $knowledgeCategories = KnowledgeCategory::all();

        return view('admin.info.article.create', compact('knowledgeCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminKnowledgeArticleStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminKnowledgeArticleStore $request)
    {
        $data = $request->validated();
        
        KnowledgeArticle::create([
            'title'         => $data['title'],
            'text'          => $data['text'],
            'order'         => $data['order'] ? $data['order'] : 0,
            'slug'          => Str::slug($data['title'], '-'),
            'category_id'   => $data['category_id']
        ]);

        return redirect()->route('article.index')->with(['status' => 'New Article Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Info\KnowledgeArticle  $knowledgeArticle
     * @return \Illuminate\Http\Response
     */
    public function show(KnowledgeArticle $knowledgeArticle)
    {
        return view('admin.info.article.show', compact('knowledgeArticle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Info\KnowledgeArticle  $knowledgeArticle
     * @return \Illuminate\Http\Response
     */
    public function edit(KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeCategories = KnowledgeCategory::all();

        return view('admin.info.article.edit', compact('knowledgeArticle', 'knowledgeCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminKnowledgeArticleUpdate  $request
     * @param  \App\Models\Info\KnowledgeArticle  $knowledgeArticle
     * @return \Illuminate\Http\Response
     */
    public function update(AdminKnowledgeArticleUpdate $request, KnowledgeArticle $knowledgeArticle)
    {
        $data = $request->validated();

        $knowledgeArticle->update([
            'title'         => $data['title'],
            'text'          => $data['text'],
            'slug'          => $data['slug'],
            'order'         => $data['order'],
            'category_id'   => $data['category_id']
        ]);

        return redirect()->route('knowledgeArticle.show', ['knowledgeArticle' => $knowledgeArticle->id])
            ->with(['status' => 'Article successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Info\KnowledgeArticle  $knowledgeArticle
     * @return \Illuminate\Http\Response
     */
    public function destroy(KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeArticle->delete();

        return redirect()->route('knowledgeArticle.index')
            ->with(['status' => 'Article successfully deleted']);
    }
}
