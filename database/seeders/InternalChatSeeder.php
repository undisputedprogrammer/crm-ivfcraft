<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Center;
use App\Models\ChatRoom;
use App\Models\Hospital;
use App\Helpers\ChatHelper;
use App\Models\InternalChat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InternalChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create rooms for hospitals, add chats
        foreach (Hospital::all() as $h) {
            $hRoom = ChatHelper::createRoom(
                $h->id,
                get_class($h)
            );
            foreach ($h->users as $u) {
                $hRoom->users()->attach([$u->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
            }
            $msgTime = Carbon::now()->subDays(90)->timestamp;
            for($i = 0; $i < 30; $i++) {
                InternalChat::factory()->create([
                    'sender_id' => $h->users->random()->id,
                    'chat_room_id' => $hRoom->id,
                    'created_at' => $msgTime
                ]);
                $msgTime += 60 * 60 * random_int(1, 20);
            }
        }

        //create rooms for centers, add chats
        foreach (Center::all() as $c) {
            $cRoom = ChatHelper::createRoom(
                $c->id,
                get_class($c)
            );
            foreach ($c->users as $u) {
                $cRoom->users()->attach([$u->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
            }
            $msgTime = Carbon::now()->subDays(90)->timestamp;
            for($i = 0; $i < 30; $i++) {
                InternalChat::factory()->create([
                    'sender_id' => $c->users->random()->id,
                    'chat_room_id' => $cRoom->id,
                    'created_at' => $msgTime
                ]);
                $msgTime += 60 * 60 * random_int(1, 20);
            }
        }

        $adminEmails = [
            'craftadmin@demo.com',
            'craftagent@demo.com',
        ];

        $agentEmails = [
            'aradmin@demo.com',
            'aragent@demo.com'
        ];

        foreach ($adminEmails as $e) {
            $admin = User::where('email', $e)->get()->first();

            foreach ($admin->chatFriends() as $u) {
                $x = random_int(0, 4);
                $room = ChatRoom::factory()->create(['type' => 'one-to-one']);
                $room->users()->attach([$u->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
                $room->users()->attach([$admin->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
                $msgTime = Carbon::now()->subDays(90)->timestamp;
                for($i = 0; $i < $x; $i++) {
                    for($n = 0; $n < 30; $n++) {
                        $self = random_int(0,1);
                        InternalChat::factory()->create([
                            'sender_id' => $self ? $admin->id : $u->id,
                            'chat_room_id' => $room->id,
                            'created_at' => $msgTime
                        ]);
                        $msgTime += 60 * 60 * random_int(1, 20);
                    }
                }
            }
        }

        foreach ($agentEmails as $e) {
            $agent = User::where('email', $e)->get()->first();

            foreach ($agent->chatFriends() as $u) {
                $x = random_int(1, 5);
                $room = ChatRoom::factory()->create(['type' => 'one-to-one']);
                $room->users()->attach([$u->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
                $room->users()->attach([$agent->id => ['last_viewed_at' => Carbon::now()->timestamp]]);
                $msgTime = Carbon::now()->subDays(90)->timestamp;
                for($i = 0; $i < $x; $i++) {
                    for($n = 0; $n < 30; $n++) {
                        $self = random_int(0,1);
                        InternalChat::factory()->create([
                            'sender_id' => $self ? $agent->id : $u->id,
                            'chat_room_id' => $room->id,
                            'created_at' => $msgTime
                        ]);
                        $msgTime += 60 * random_int(1, 5);
                    }
                }
            }
        }
    }
}
