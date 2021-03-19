<?php

namespace Database\Factories\Info;

use App\Models\Info\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Faq::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question' => $this->faker->sentence(3),
            'answer' => $this->faker->text(300),
            'order' => 0
        ];
    }
}
