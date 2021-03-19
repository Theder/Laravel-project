<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Payment\Invoice;
use Illuminate\Support\Facades\Auth;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Show the list of invoices
     * 
     * @return \Illuminate\Http\Responce
     */
    public function index()
    {
        $invoices = Invoice::where('creator_id', Auth::id())->orderBy('created_at')->get();

        return view('panel.invoices.index', compact('invoices'));
    }

    /**
     * Show the specific invoice
     * 
     * @param \App\Models\Payment\Invoice
     * @return \Illuminate\Http\Responce
     */
    public function show(Invoice $invoice)
    {
        if ($invoice->user->id != Auth::id())
            abort(403);

        return view('panel.invoices.show', compact('invoice'));
    }

    /**
     * Download the specific invoice
     * 
     * @param \App\Models\Payment\Invoice
     * @return \Illuminate\Http\Response
     */
    public function download(Invoice $invoice)
    {
        if ($invoice->user->id != Auth::id() || !Auth::user()->isAdmin())
            abort(403);

        $pdf = PDF::loadView('panel.invoices.document-table', compact('invoice'));

        return $pdf->stream();
    }
}
