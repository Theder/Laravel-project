<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'order', 'slug', 'icon'
    ];

    public function articles()
    {
        return $this->hasMany('App\Models\Info\KnowledgeArticle', 'category_id');
    }

    public function knowledgeArticles()
    {
        return $this->articles();
    }
}
