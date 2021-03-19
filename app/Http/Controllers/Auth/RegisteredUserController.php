<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\IpHelper;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $ipAddress = $request->ip() == '127.0.0.1' ? '95.161.246.91' : $request->ip();
        $ipHelper = new IpHelper();

        try {
            $ipDetails = $ipHelper->ipCheck($ipAddress);
        } catch(\Exception $ex) {
            $ipDetails['abuseConfidenceScore'] = - 1;
            $ipDetails['countryCode'] = 'NONE';
        }
        

        Auth::login($user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'ip'            => $ipAddress,
            'ip_country'    => $ipDetails['countryCode']
        ]));

        if ($ipDetails['abuseConfidenceScore'] > 0) {
            $user->abusing_score = $ipDetails['abuseConfidenceScore'];
            $user->abusing_reason = 'IP has reports in AbuseIPDB';
            $user->save();
        }

        if ($ipDetails['abuseConfidenceScore'] == -1) {
            $user->abusing_score = 100;
            $user->abusing_reason = 'AbuseIPDB error. Daily rate limit.';
            $user->save();
        }

        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }
}
