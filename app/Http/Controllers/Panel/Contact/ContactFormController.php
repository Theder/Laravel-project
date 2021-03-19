<?php

namespace App\Http\Controllers\Panel\Contact;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Http\Requests\Contact\PanelSendPrivate;
use App\Http\Requests\Contact\PanelSendPublic;

class ContactFormController extends Controller
{
    /**
     * Render form to panel area
     * 
     * @return \Illuminate\Http\Response
     */
    public function formPrivate() 
    {
        return view('panel.contact.form');
    }   

    /**
     * Send message from contact form in panel area
     * 
     * @param \App\Http\Requests\Contact\PanelSendPrivate  $request
     * @return \Illuminate\Http\Responce
     */
    public function sendPrivate(PanelSendPrivate $request)
    {
        $data = $request->validated();

        Mail::to(Setting::get('general_contact_email'))
            ->send(new ContactMail($data['contact_email'], $data['message']));

        return redirect()->back()->with(['status' => 'Your message has been successfully sent']);
    }

    /**
     * Send message from contact form in public area
     * 
     * @param \App\Http\Requests\Contact\PanelSendPublic  $request
     * @return \Illuminate\Http\Responce
     */
    public function sendPublic(PanelSendPublic $request)
    {
        $validated = $request->validated();

        Mail::to(Setting::get('general_contact_email'))
            ->send(new ContactMail($validated['email'], $validated['message']));

        return redirect()->back()->with(['status' => 'Your message successfully sended']);
    }
}
