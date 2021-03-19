<?php

namespace App\Http\Controllers\Panel\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\Usefull;

class UsefullController extends Controller
{
    /**
     * Render usefull pages 
     * 
     * @return \Illuminate\Http\Response
     */
    public function usefull() 
    {
        $usefulls = Usefull::all();

        return view('panel.info.usefull', compact('usefulls'));
    }
}
