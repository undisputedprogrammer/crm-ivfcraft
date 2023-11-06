<?php

namespace App\Models;

use App\Models\User;
use App\Models\InternalChat;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatRoom extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $guarded = [];

    protected $with = [
        'internalChats'
    ];

    protected $appends = [
        'name', 'peer', 'ordered_chats'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'chat_rooms_users',
            'chat_room_id',
            'user_id'
        );
    }

    public function internalChats()
    {
        return $this->hasMany(InternalChat::class, 'chat_room_id', 'id')
            ->orderBy('created_at', 'desc')->limit(20);
    }

    public function chatable()
    {
        if ($this->type != null) {
            return $this->morphTo();
        }
        return null;
    }

    public function orderedChats(): Attribute
    {
        return Attribute::make(
            get: function ($val, $attributes) {
                return array_reverse($this->internalChats->toArray());
            }
        );
    }
    // public function getPeer($id)
    // {
    //     if ($this->type == 'one-to-one') {
    //         return $this->users()->query()->where('users.id', '<>', $id)->get()->first();
    //     }
    //     return false;
    // }

    public function name(): Attribute
    {
        return Attribute::make(
            get: function ($val, $attributes) {
                if ($this->type != 'public') {
                    $users = $this->users;
                    $uid = auth()->user()->id;
                    $thePeer = null;
                    foreach ($users as $u) {
                        if($u->id != $uid) {
                            $thePeer = $u;
                            break;
                        }
                    }
                    return $thePeer->name;
                }

                return $this->chatable->name;
            }
        );
    }

    public function peer(): Attribute
    {
        return Attribute::make(
            get: function ($val, $attributes) {
                if ($this->type != 'public') {
                    $users = $this->users;
                    $uid = auth()->user()->id;
                    $thePeer = null;
                    foreach ($users as $u) {
                        if($u->id != $uid) {
                            $thePeer = $u;
                            break;
                        }
                    }
                    return $thePeer;
                }

                return false;
            }
        );
    }
}
