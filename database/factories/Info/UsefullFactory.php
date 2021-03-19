<?php

namespace Database\Factories\Info;

use App\Models\Info\Usefull;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsefullFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Usefull::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'text'  => $this->faker->text(1000),
            'slug'  => Str::slug($title, '-'),
            'link'  => 'https://example.com',
        ];
    }
}
