<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\InternalChat;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternalChat>
 */
class InternalChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = InternalChat::class;
    public function definition(): array
    {
        return [
            'sender_id' => User::all()->random()->id,
            'chat_room_id' => ChatRoom::all()->random()->id,
            'message' => $this->faker->sentence(),
            'created_at' => Carbon::now()->subDays(random_int(1, 60))->timestamp
        ];
    }
}
