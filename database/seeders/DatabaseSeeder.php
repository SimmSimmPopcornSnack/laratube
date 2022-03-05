<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Channel;
use App\Models\Subscription;
use App\Models\Video;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::factory()->create([
            "email" => "taro@example.com"
        ]);
        $user2 = User::factory()->create([
            "email" => "hanako@example.com"
        ]);

        $channel1 = Channel::factory()->create([
            "user_id" => $user1->id
        ]);
        $channel2 = Channel::factory()->create([
            "user_id" => $user2->id
        ]);

        $channel2->subscriptions()->create([
            "user_id" => $user1->id,
        ]);
        $channel1->subscriptions()->create([
            "user_id" => $user2->id,
        ]);

        Subscription::factory()->count(100)->create([
            "channel_id" => $channel1->id,
        ]);
        Subscription::factory()->count(100)->create([
            "channel_id" => $channel2->id,
        ]);
        $video = Video::factory()->create([
            "channel_id" => $channel1->id
        ]);
        Comment::factory()->count(50)->create([
            "video_id" => $video->id,
        ]);

        $comment = Comment::first();
        Comment::factory()->count(50)->create([
            "video_id" => $video->id,
            "parent_comment_id" => $comment->id,
        ]);

    }
}
