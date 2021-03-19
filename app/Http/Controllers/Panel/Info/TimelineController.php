<?php

namespace App\Http\Controllers\Panel\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\Timeline;
use Illuminate\Support\Facades\Auth;

class TimelineController extends Controller
{
    /**
     * Update timeline status for authtenticated user
     * 
     * @return void
     */
    public function timelineUpdate()
    {
        $timelines = Timeline::orderBy('created_at', 'DESC')->limit(10)->get();
        $unreadTimelines = $timelines->diff(Auth::user()->timelines);

        Auth::user()->timelines->attach($unreadTimelines->pluck('id'));
    }
}
