<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    private $mailto;
    private $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailto, $message)
    {
        $this->mailto = $mailto;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@dev.sharktarget.com')
                    ->subject('New message from Contact form')
                    ->view('emails.contact')
                    ->with([
                        'message_text'   => $this->message,
                        'mailto'    => $this->mailto
                    ]);
    }
}
