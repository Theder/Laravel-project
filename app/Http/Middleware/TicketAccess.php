<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Contact\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketAccess
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
        $ticketId = explode('/', $request->path())[2];
        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->user->id != Auth::id())
            abort(403);

        return $next($request);
    }
}
