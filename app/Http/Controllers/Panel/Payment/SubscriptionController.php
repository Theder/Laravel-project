<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Cancel specific subscription
     * 
     * @param \Illuminate\Http\Request  $request
     * @return Illuminate\Http\Responce 
     */
    public function cancel(Request $request)
    {
        $subscription = Subscription::find($request->input('subscription_id'));
        $user = $subscription->user[0];

        if (Auth::id() != $user->id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $subscription->cancel();

        return redirect()->back()->with('success', 'Your subscription has been successfully canceled.');
    }
}
