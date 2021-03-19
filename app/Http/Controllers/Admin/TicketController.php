<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact\Ticket;
use App\Models\User;
use App\Models\Contact\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Common\AdminTicketStore;
use App\Http\Requests\Common\AdminTicketUpdate;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::orderBy('created_at', 'ASC')->get();

        return view('admin.contact.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();

        return view('admin.contact.tickets.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Common\AdminTicketStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminTicketStore $request)
    {
        $data = $request->validated();
        
        $ticket = Ticket::create([
            'title'                 => $data['subject'],
            'status'                => Ticket::STATUS_ANSWERD,
            'creator_id'            => $data['creator_id'],
        ]);
    
        Message::create([
            'message'               => $data['message'],
            'is_unread_by_user'     => true,
            'is_unread_by_admin'    => false,
            'creator_id'            => Auth::id(),
            'ticket_id'             => $ticket->id,
        ]);


        return redirect()->route('admin.tickets.index')
            ->with(['status' => 'New Ticket Successfully created']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {
        $ticket->messages()->where('is_unread_by_admin', 1)->update(['is_unread_by_admin' => 0]);

        return view('admin.contact.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Common\AdminTicketUpdate  $request
     * @param  \App\Models\Contact\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(AdminTicketUpdate $request, Ticket $ticket)
    {
        $data = $request->validated();

        if (!empty($data['close'])) {
            $ticket->status = Ticket::STATUS_CLOSED;
            $ticket->save();

            return redirect()->route('admin.tickets.index')
                ->with(['status' => 'Ticket successfully closed']);
        }

        $ticket->status = Ticket::STATUS_ANSWERD;
        $ticket->save();

        Message::create([
            'message'               => $data['message'],
            'is_unread_by_user'     => true,
            'is_unread_by_admin'    => false,
            'creator_id'            => Auth::id(),
            'ticket_id'             => $ticket->id
        ]);

        return redirect()->back();
    }

    /**
     * Close ticket
     *
     * @param  \App\Models\Contact\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function close(Ticket $ticket)
    {
        $ticket->status = Ticket::STATUS_CLOSED;
        $ticket->save();

        return redirect()->route('admin.tickets.index')
            ->with(['status' => 'Ticket successfully closed']);
    }
}
