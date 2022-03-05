<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Video;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "body" => $this->faker->sentence(6),
            "user_id" => function() {
                return User::factory()->create()->id;
            },
            "video_id" => function() {
                return Video::factory()->create()->id;
            },
            "parent_comment_id" => null,
        ];
    }
}
