<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proxy\Proxy;
use App\Models\Setting;
use App\Http\Requests\Proxy\AdminProxyStore;
use App\Http\Requests\Proxy\AdminProxyUpdate;
use App\Http\Requests\Proxy\AdminProxyBulkVerify;
use App\Http\Requests\Proxy\AdminProxyBulkDestroy;
use App\Http\Requests\Proxy\AdminProxyImport;
use App\Http\Requests\Proxy\AdminProxyExport;
use App\Http\Requests\Proxy\AdminProxyCheckerUrl;
use App\Http\Requests\Proxy\AdminProxyUpdateType;

class ProxyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proxies = Proxy::all();

        return view('admin.proxy.index', compact('proxies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Proxy\AdminProxyStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminProxyStore $request)
    {
        $data = $request->validated();

        Proxy::create([
            'ip_port'       => $data['ip_port'],
            'login'         => $data['login'],
            'password'      => $data['password'],
            'type'          => $data['type'],
            'rotation_time' => !empty($data['rotation_time']) ? $data['rotation_time'] : 0,
            'latency'       => !empty($data['latency']) ? $data['latency'] : 0,
        ]);

        return redirect()->route('proxies.index')->with(['status' => 'New proxy successfully added.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function edit(Proxy $proxy)
    {
        return view('admin.proxy.edit', compact('proxy'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Proxy\AdminProxyUpdate  $request
     * @param  \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function update(AdminProxyUpdate $request, Proxy $proxy)
    {
        $data = $request->validated();

        $proxy->update([
            'ip_port'   => $data['ip_port'],
            'login'     => $data['login'],
            'password'  => $data['password']
        ]);

        return redirect()->route('proxies.index')
            ->with(['status' => 'Proxy #' . $proxy->id . ' successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proxy $proxy)
    {
        $proxy->delete();

        return redirect()->route('proxies.index')
            ->with(['status' => 'Proxy #' . $proxy->id . ' successfully deleted.']);
    }

    /**
     * Validate proxy
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Response
     */
    public function verify(Proxy $proxy)
    {
        $proxy->check();

        return redirect()->back()->with(['status' => 'Proxy #' . $proxy->id . ' successfully verified']);
    }

    /**
     * Validate selected proxies
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyBulkVerify  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkVerify(AdminProxyBulkVerify $request) 
    {
        $data = $request->validated();
        
        $proxyIds = explode(',', $data['proxy_ids']);

        foreach ($proxyIds as $proxyId) {
            $proxy = Proxy::find($proxyId);
            $proxy->check();
        }

        return redirect()->back()->with(['status' => 'Selected proxies successfully verified']);
    }

    /**
     * Validate all proxies in DataBase
     * 
     * @return \Illuminate\Http\Response
     */
    public function verifyAll()
    {
        Proxy::checkAll();

        return redirect()->back()->with(['status' => 'Proxies successfully verified']);
    }

    /**
     * Delete selected proxies
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyBulkDestroy  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(AdminProxyBulkDestroy $request)
    {
        $data = $request->validated();

        $proxyIds = explode(',', $data['proxy_ids']);

        foreach ($proxyIds as $proxyId) {
            $proxy = Proxy::find($proxyId);
            $proxy->delete();
        }

        return redirect()->route('proxies.index')->with(['status' => 'Proxies successfully deleted']);
    }

    /**
     * Import and save proxies from file
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyImport  $request
     * @return \Illuminate\Http\Response
     */
    public function import(AdminProxyImport $request)
    {
        $request->validated();
        $file = $request->file('proxies');
        $content = $file->getContent();

        Proxy::importFile($content);

        return redirect()->route('proxies.index')
            ->with(['status' => 'File successfully imported']);
    }

    /**
     * Export selected proxies to txt file
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyExport  $request
     * @return \Illuminate\Http\Response
     */
    public function export(AdminProxyExport $request)
    {
        $data = $request->validated();
        $proxyIds = explode(',', $data['proxy_ids']);

        $content = '';
        foreach ($proxyIds as $proxyId) {
            $content .= Proxy::findAndCreditenialsToString($proxyId);
        }

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }

    /**
     * Export all proxies to a file
     * 
     * @return \Illuminate\Http\Response
     */
    public function exportAll() 
    {
        $content = '';
        Proxy::all()->each(function ($proxy) use (&$content) {
            $content .= $proxy->creditenialsToString();
        });

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }

    /**
     * Set the url for checker
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyCheckerUrl  $request
     * @return void
     */
    public function checkerUrl(AdminProxyCheckerUrl $request)
    {
        $data = $request->validated();
        Setting::set('url_to_proxy_check', $data['checkerLink']);
    }

    /**
     * Update proxy type
     * 
     * @param \App\Http\Requests\Proxy\AdminProxyUpdateType
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return void
     */
    public function updateType(AdminProxyUpdateType $request, Proxy $proxy)
    {
        $data = $request->validated();

        $proxy->type = $data['newType'];
        $proxy->save();
    }
}
