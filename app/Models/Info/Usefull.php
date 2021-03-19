<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usefull extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'text', 'link', 'thumbnail', 'slug'
    ];
}
