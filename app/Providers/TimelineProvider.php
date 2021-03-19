<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Info\Timeline;

class TimelineProvider extends ServiceProvider
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
                $timelines = Timeline::orderBy('created_at', 'DESC')->limit(10)->get();

                $unreadTimelines = $timelines->diff(Auth::user()->timelines)->count();

                $view->with('timelines', $timelines);
                $view->with('unreadTimelines', $unreadTimelines);
            }
        });
    }
}
