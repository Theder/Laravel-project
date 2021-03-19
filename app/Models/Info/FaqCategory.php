<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'order', 'slug', 'icon'  
    ];

    public function faqs()
    {
        return $this->hasMany('App\Models\Info\Faq', 'category_id');
    }
}
