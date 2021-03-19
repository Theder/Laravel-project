<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;

class PanelController extends Controller
{
    /**
     * Render dashboard page
     * 
     * @return \Illuminate\Http\Responce
     */
    public function index()
    {
        return view('panel.dashboard');
    }

    /**
     * Render welcome page
     * 
     * @return \Illuminate\Http\Request
     */
    public function welcome()
    {
        return view('panel.welcome');
    }
}
