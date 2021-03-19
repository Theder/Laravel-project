<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSettingsPut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;

class ProfileController extends Controller
{
    /**
     * Render profile page
     * 
     * @return \Illuminate\Http\Responce
     */
    public function index()
    {
        return view('panel.profile.index');
    }

    /**
     * Render profile edit page
     * 
     * @return \Illuminate\Http\Responce
     */
    public function edit()
    {
        return view('panel.profile.edit');
    }

    /**
     * Save profile settings
     * 
     * @param \App\Http\Requests\ProfileSettingsPut  $request
     * @return \Illuminate\Http\Responce
     */
    public function update(ProfileSettingsPut $request)
    {
        $user = Auth::user();

        $data = $request->validated();

        if ($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $photo = Image::make($request->file('avatar'))
                ->resize(400, null, function ($constraint) { $constraint->aspectRatio(); } )
                ->encode('jpg',80);

            $filename = time() . '.' . $avatar->getClientOriginalExtension();                

            Storage::disk('public')->put($filename, $photo);

    		$user = Auth::user();
    		$user->avatar = $filename;
    		$user->save();
        }
        
        if (!empty($data['password'])) {
            $user->fill([
                'password'  => Hash::make($data['password'])    
            ]);    
        }

        $user->fill([
            'email'         => $data['email'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'phone'         => $data['phone'],
            'country'       => $data['country'],
            'state'         => $data['state'],
            'zip'           => $data['zip'],
            'city'          => $data['city'],
            'address'       => $data['address']
        ]);

        $user->save();

        return redirect()->back()->with(['settingsStatus' => 'Your account settings successfuly saved.']);
    }
}
