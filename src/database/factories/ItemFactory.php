<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Item ' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->text(200),
            'price' => $this->faker->numberBetween(0, 10000),
            'brand' => $this->faker->optional()->word(),
            'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'status' => 'selling',
            'image_path' => 'test.jpg',
        ];
    }
}