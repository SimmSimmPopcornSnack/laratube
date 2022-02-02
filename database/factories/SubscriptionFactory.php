<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Channel;

class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => function() {
                return User::factory()->create()->id;
            },
            "channel_id" => function() {
                return Channel::factory()->create()->id;
            },
        ];
    }
}
