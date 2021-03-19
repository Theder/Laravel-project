<?php

namespace App\Http\Controllers\Panel\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proxy;
use App\Models\ProxyNote;
use App\Http\Requests\Proxy\PanelProxyNoteEdit;
use App\Http\Requests\Proxy\PanelProxyExport;

class ProxyListController extends Controller
{
    /**
     * Render list of proxies related to user
     * 
     * @return \Illuminate\Http\Responce
     */
    public function index()
    {
        $proxies = Auth::user()->collectAllProxies();

        return view('panel.proxy.list.index', compact('proxies'));
    }

    /**
     * Create or edit user note about specific proxy
     * 
     * @param \App\Http\Requests\Proxy\PanelProxyNoteEdit  $request
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Responce
     */
    public function noteEdit(PanelProxyNoteEdit $request, Proxy $proxy)
    {
        if ($proxy->user->id != Auth::id())
            abort(403);

        $data = $request->validated();

        Auth::user()->addNotes($proxy, $data['note-text']);

        return redirect()->back()->with(['status' => 'Note successfully edited']);
    }

    /**
     * Download specific proxy data
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Responce
     */
    public function download(Proxy $proxy)
    {
        if (empty($proxy->subscription->user[0]->id) || $proxy->subscription->user[0]->id != Auth::id())
            return abort(403);

        $content = $proxy->creditenialsToShow();

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }

    /**
     * Render info about specific proxy
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Responce
     */
    public function show(Proxy $proxy)
    {
        if (empty($proxy->subscription->user[0]->id) || $proxy->subscription->user[0]->id != Auth::id())
            return abort(403);

        $content = $proxy->creditenialsToShow();

        return $content;
    }

    /**
     * Export selected proxies to file
     * 
     * @param \App\Http\Requests\Proxy\PanelProxyExport  $request
     * @return \Illuminate\Http\Responce
     */
    public function export(PanelProxyExport $request) 
    {
        $data = $request->validated();

        $proxyIds = explode(',', $data['proxy_ids']);
        $content = Proxy::formatProxiesCreditenialsToExport($proxyIds, 
            $data['is_default'],    
            $data['export_type'], 
            $data['template']
        );

        return response($content)->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="export.txt',
        ]);
    }

    /**
     * Validae specific proxy
     * 
     * @param \App\Models\Proxy\Proxy  $proxy
     * @return \Illuminate\Http\Responce
     */
    public function verify(Proxy $proxy)
    {
        $proxy->check();

        return redirect()->back()->with(['status' => 'Proxy #' . $proxy->id . ' successfully checked']);
    }
}
