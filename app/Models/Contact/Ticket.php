<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    const STATUS_ANSWERD = 'answered';
    const STATUS_CLOSED = 'closed';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'title', 'status', 'creator_id'
    ];

    public function messages() 
    {
        return $this->hasMany('App\Models\Contact\Message');
    }

    public function user() 
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }

    /**
     * Check does user has unread messages in ticket
     * 
     * @return bool
     */
    public function hasUnreadByUser() 
    {
        $count = $this->messages()->where('is_unread_by_user', 1)->count();
        return $count ? true : false;
    }

    /**
     * Count unread messages by normal user in ticket
     * 
     * @return int
     */
    public function countUnreadByUser()
    {
        $count = $this->messages()->where('is_unread_by_user', 1)->count();
        return $count;
    }

    /**
     * Check does admin has unread messages in ticket
     * 
     * @return bool
     */
    public function hasUnreadByAdmin() 
    {
        $count = $this->messages()->where('is_unread_by_admin', 1)->count();
        return $count ? true : false;
    }

    /**
     * Count unread messages by admin in ticket
     * 
     * @return int
     */
    public function countUnreadByAdmin()
    {
        $count = $this->messages()->where('is_unread_by_admin', 1)->count();
        return $count;
    }
}
