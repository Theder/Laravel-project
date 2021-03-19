<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'price', 'payment_status', 'creator_id'];
	
	public function user() 
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }
}
