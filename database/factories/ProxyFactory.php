<?php

namespace Database\Factories;

use App\Models\Proxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProxyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proxy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ip_port'   => '127.0.0.1:8000',
            'login'     => Str::random(10),
            'password'  => Str::random(10),
            'type'      => rand(0, 1) ? Proxy::TYPE_UNLIMITED : Proxy::TYPE_LIMITED,
            'rotation_time' => rand(0, 1) ? 300 : 0,
            'check_status'  => 'Valid',
            'latency'       => rand(100, 300)
        ];
    }
}
