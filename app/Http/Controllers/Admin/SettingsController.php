<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Show the form of settings
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $tabs = Setting::select('tab')->distinct()->get();
        $settings = Setting::all();

        return view('admin.settings', compact('settings', 'tabs'));
    }

    /**
     * Update settings
     * 
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $name => $value) {
            if ($name === '_token')
                continue;
            
            Setting::set($name, $value);
        }

        return redirect()->back()->with(['status' => 'Settings successfully updated']);
    }
}
