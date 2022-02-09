<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Channel;
use App\Models\Subscription;

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
    }
}
