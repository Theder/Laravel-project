<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Info\KnowledgeCategory;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'text', 'order', 'category_id', 'slug'
    ];

    public function category()
    {
        return $this->belongsTo(KnowledgeCategory::class);
    }
}
