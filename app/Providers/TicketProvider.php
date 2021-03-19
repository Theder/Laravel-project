<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact\Ticket;

class TicketProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) 
        {
            if (Auth::check()) {                
                if (Auth::user()->isAdmin()) {
                    $tickets = Ticket::all();
                } else {
                    $tickets = Auth::user()->tickets;
                }

                $count = 0;
                foreach ($tickets as $ticket) {
                    if (Auth::user()->isAdmin()) {
                        $count += $ticket->messages()->where('is_unread_by_admin', 1)->count();
                    } else {
                        $count += $ticket->messages()->where('is_unread_by_user', 1)->count();
                    }
                }
                
                $view->with('unreadTicketsCount', $count);
            }
        });
    }
}
