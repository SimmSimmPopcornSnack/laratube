<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Channel;

class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "channel_id" => function() {
                return Channel::factory()->create()->id;
            },
            "views" => $this->faker->numberBetween(1, 1000),
            "thumbnail" => $this->faker->imageUrl(),
            "percentage" => 100,
            "title" => $this->faker->sentence(4),
            "description" => $this->faker->sentence(10),
            "path" => $this->faker->word(),
        ];
    }
}
