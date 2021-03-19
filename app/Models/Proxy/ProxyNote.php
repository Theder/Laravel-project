<?php

namespace App\Models\Proxy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'note', 'proxy_id', 'user_id'
    ];
}
