<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\Timeline;
use App\Http\Requests\Info\AdminTimelineStore;
use App\Http\Requests\Info\AdminTimelineUpdate;

class TimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timelines = Timeline::all();

        return view('admin.info.timeline.index', compact('timelines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.info.timeline.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminTimelineStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminTimelineStore $request)
    {
        $data = $request->validated();

        Timeline::create([
            'timeline' => $data['timeline'],
        ]);

        return redirect()->route('timeline.index')
            ->with(['status' => 'New Timeline Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Info\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function show(Timeline $timeline)
    {
        return view('admin.info.timeline.show', compact('timeline'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Info\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function edit(Timeline $timeline)
    {
        return view('admin.info.timeline.edit', compact('timeline'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminTimelineUpdate  $request
     * @param  \App\Models\Info\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function update(AdminTimelineUpdate $request, Timeline $timeline)
    {
        $data = $request->validated();

        $timeline->update([
            'timeline' => $data['timeline']
        ]);

        return redirect()->route('timeline.show', ['timeline' => $timeline->id])
            ->with(['status' => 'Timeline successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Info\Timeline  $timeline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timeline $timeline)
    {
        $timeline->delete();

        return redirect()->route('timeline.index')
            ->with(['status' => 'Timeline successfully deleted.']);
    }
}
