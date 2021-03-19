<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Info\FaqCategory;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'answer', 'order', 'category_id'];

    public function category() 
    {
        return $this->belongsTo(FaqCategory::class);
    }
}
