<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403, 'Access denied');
        }

        $errors = Auth::user()->validateBussinessInfo();

        if (!empty($errors)) {
            $errors['bussiness_data_empty_error'] = 'To receive invoices, fill your bussiness details.';
            return redirect()->route('profile-settings')->with($errors)->send();
        }

        return $next($request);
    }
}
