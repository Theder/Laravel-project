<?php

namespace App\Http\Controllers\Panel\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact\Ticket;
use App\Models\Contact\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\TicketAccess;
use App\Http\Requests\Contact\PanelTicketStore;
use App\Http\Requests\Contact\PanelTicketUpdate;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(TicketAccess::class, ['only' => ['show', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $tickets = Ticket::where('creator_id', Auth::id())->orderBy('updated_at', 'DESC')->get();

        return view('panel.contact.tickets', compact('tickets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Contact\PanelTicketStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PanelTicketStore $request) 
    {
        $data = $request->validated();

        $ticket = Ticket::create([
            'title'                 => $data['title'],
            'status'                => Ticket::STATUS_PENDING,
            'creator_id'            => Auth::id()
        ]);

        Message::create([
            'message'               => $data['message'],
            'is_unread_by_user'     => false,
            'is_unread_by_admin'    => true,
            'creator_id'            => Auth::id(),
            'ticket_id'             => $ticket->id
        ]);

        return redirect()->back()->with(['status' => 'Ticket successfully created']);
    }

    /**
     * Show the specified resource
     * 
     * @param  \App\Models\Contact\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket) 
    {
        $ticket->messages()->where('is_unread_by_user', 1)->update(['is_unread_by_user' => 0]);

        return view('panel.contact.ticket', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Contact\PanelTicketUpdate  $request
     * @param  \App\Models\Contact\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(PanelTicketUpdate $request, Ticket $ticket)
    {
        $data = $request->validated();

        $ticket->status = Ticket::STATUS_PENDING;
        $ticket->save();

        Message::create([
            'message'               => $data['message'],
            'is_unread_by_user'     => false,
            'is_unread_by_admin'    => true,
            'creator_id'            => Auth::id(),
            'ticket_id'             => $ticket->id
        ]);

        return redirect()->back();
    }
}
