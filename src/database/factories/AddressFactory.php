<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class AddressFactory extends Factory
{
    public function definition()
    {
        $faker = Faker::create('ja_JP');

        $raw = $faker->postcode();

        $postal = preg_replace(
            '/^(\d{3})-?(\d{4})$/',
            '$1-$2',
            $raw
        );
        return [
            'postal_code' => $postal,
            'address'     => $this->faker->prefecture()
                            . $this->faker->city()
                            . $this->faker->streetAddress(),

            'building'    => $this->faker->secondaryAddress(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
