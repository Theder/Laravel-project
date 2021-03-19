<?php

namespace App\Http\Controllers\Panel\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProxyManagerController extends Controller
{
    public function index() 
    {
        return view('panel.proxy.manager.index');
    }
}
