<?php

namespace Database\Factories\Info;

use App\Models\Info\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FaqCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FaqCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(5);

        return [
            'name' => $title,
            'order' => 0,
            'slug' => Str::slug($title, '-'),
            'icon' => 'icon-coffee'
        ];
    }
}
