<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Info\FaqCategory;
use App\Models\Info\Faq;
use App\Models\Info\KnowledgeCategory;
use App\Models\Info\KnowledgeArticle;
use App\Models\Info\Usefull;
use App\Models\Info\Timeline;
use App\Models\Proxy;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();

        FaqCategory::factory(3)->create()->each(function ($category) {
            $category->faqs()->saveMany(Faq::factory(5)->make());
        });
        
        KnowledgeCategory::factory(9)->create()->each(function ($category) {
            $category->articles()->saveMany(KnowledgeArticle::factory(20)->make());
        });

        Usefull::factory(20)->create();
        Timeline::factory(10)->create();
        Proxy::factory(150)->create();
    }
}
