<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Controller;
use App\Models\Info\Usefull;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\Info\AdminUsefullStore;
use App\Http\Requests\Info\AdminUsefullUpdate;

class UsefullController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usefulls = Usefull::all();

        return view('admin.info.usefull.index', compact('usefulls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.info.usefull.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminUsefullStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUsefullStore $request)
    {
        $data = $request->validated();
        
        $fileLink = "usefull/" . Str::random(20);
        $storagePath = Storage::put($fileLink, $data['thumbnail']);

        Usefull::create([
            'title'     => $data['title'],
            'text'      => $data['text'],
            'link'      => $data['link'],
            'slug'      => Str::slug($data['title']),
            'thumbnail' => $storagePath,
        ]);

        return redirect()->route('usefull.index')
            ->with(['status' => 'New Sponsored Link Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Info\Usefull  $usefull
     * @return \Illuminate\Http\Response
     */
    public function show(Usefull $usefull)
    {
        return view('admin.info.usefull.show', compact('usefull'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Info\Usefull  $usefull
     * @return \Illuminate\Http\Response
     */
    public function edit(Usefull $usefull)
    {
        return view('admin.info.usefull.edit', compact('usefull'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Info\AdminUsefullUpdate  $request
     * @param  \App\Models\Info\Usefull  $usefull
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUsefullUpdate $request, Usefull $usefull)
    {
        $data = $request->validated();

        if (!empty($request->file('thumbnail'))) {
            $fileLink = "usefull/" . Str::random(20);
            $storagePath = Storage::put($fileLink, $data['thumbnail']);

            $usefull->update([
                'thumbnail' => $storagePath,
            ]);
        }

        $usefull->update([
            'title'     => $data['title'],
            'text'      => $data['text'],
            'link'      => $data['link'],
        ]);

        return redirect()->route('usefull.show', ['usefull' => $usefull->id])
            ->with(['status' => 'Sponsored link successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Info\Usefull  $usefull
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usefull $usefull)
    {
        $usefull->delete();

        return redirect()->route('usefull.index')
            ->with(['status' => 'Sponsored link successfully deleted']);
    }
}
