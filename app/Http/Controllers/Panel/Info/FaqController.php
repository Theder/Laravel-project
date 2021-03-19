<?php

namespace App\Http\Controllers\Panel\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Get faq list for panel area
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function private(Request $request)
    {
        $search = $request->input('search');

        $faqCategories = FaqCategory::orderBy('order', 'ASC')->get();

        return view('panel.info.faq', compact('faqCategories', 'search'));
    }
}
