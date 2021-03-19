<?php

namespace App\Http\Controllers\Panel\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\KnowledgeCategory;
use App\Models\Info\KnowledgeArticle;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    /**
     * Get the list of knowledge base articles in panel area
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function private(Request $request)
    {
        $search = $request->input('search');

        $knowledgeCategories = KnowledgeCategory::orderBy('order', 'ASC')->get();

        return view('panel.info.knowledge', compact('knowledgeCategories', 'search'));
    }

    /**
     * Render knowledge base articles in category in panel area
     * 
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @return \Illuminate\Http\Response
     */
    public function privateCategory(Request $request, KnowledgeCategory $knowledgeCategory)
    {
        $search = $request->input('search');

        return view('panel.info.knowledgeCategory', compact('knowledgeCategory', 'search'));
    }

    /** 
     * Render knowledge base article in panel area
     * 
     * @param \App\Models\Info\KnowledgeCategory  $knowledgeCategory
     * @param \App\Models\Info\KnowledgeArticle  $knowledgeArticle
     * @return \Illuminate\Http\Response
     */
    public function privateArticle(KnowledgeCategory $knowledgeCategory, KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeCategories = KnowledgeCategory::all();

        return view('panel.info.knowledgeArticle', 
            compact('knowledgeCategory', 'knowledgeArticle', 'knowledgeCategories'));
    }
}
