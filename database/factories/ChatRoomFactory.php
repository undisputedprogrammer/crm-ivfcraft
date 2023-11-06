<?php

namespace Database\Factories;

use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatRoom>
 */
class ChatRoomFactory extends Factory
{
    private $types = [
        'public',
        'one-to-one'
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = ChatRoom::class;
    public function definition(): array
    {
        return [
            'type' => $this->types[random_int(0,1)],
            'chatable_id' => null,
            'chatable_type' => null
        ];
    }
}
