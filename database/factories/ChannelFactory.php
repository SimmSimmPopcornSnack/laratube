<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->sentence(3),
            "user_id" => function() {
                return User::factory()->create()->id;
            },
            "description" => $this->faker->sentence(30),
        ];
    }
}
