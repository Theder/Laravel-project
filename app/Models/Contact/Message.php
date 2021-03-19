<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message', 'is_unread_by_user', 'is_unread_by_admin', 'creator_id', 'ticket_id'
    ];

    public function ticket() 
    {
        return $this->belongsTo('App\Models\Contact\Ticket');
    }

    public function author() 
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }

    public function getDate()
    {
        $date = $this->created_at;
        $date = date('d.m, H:m', strtotime($date));

        return $date;
    }
}
