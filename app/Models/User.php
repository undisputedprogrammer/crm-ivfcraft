<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use App\Models\Journal;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\AccessControl\Traits\WithRoles;
use Ynotz\MediaManager\Contracts\MediaOwner;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MediaOwner
{
    use HasApiTokens, HasFactory, Notifiable, WithRoles, OwnsMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'designation',
        'hospital_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'user_picture'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function getMediaStorage(): array
    {
        return ['user_picture' => [
            'disc' => 'local',
            'folder' => '/images/user_picture'
        ]];
    }

    public function userPicture(): Attribute
    {
        return Attribute::make(
            get: function(){
                return $this->getSingleMediaForDisplay('user_picture');
            }
        );
    }

    public function leads(){
        return $this->hasMany(Lead::class,'assigned_to','id');
    }

    public function centers(){
        return $this->belongsToMany(Center::class, 'user_has_centers');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function journals(){
        return $this->hasMany(Journal::class, 'user_id', 'id');
    }

    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_rooms_users', 'user_id', 'chat_room_id')->withPivot('last_viewed_at');
    }

    public function publicChatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_rooms_users', 'user_id', 'chat_room_id')->where('type', 'public');
    }

    public function chatFriends()
    {
        if ($this->hasPermissionTo('Chat: Enter Own Hospital')) {
            $users = $this->hospital->users;
        } elseif($this->hasPermissionTo('Chat: Enter Own Center')) {
            $users = $this->centers[0]->users;
        }
        $friends = [];
        foreach ($users as $f) {
            if ($f->id != $this->id) {
                $friends[] = $f;
            }
        }
        return $friends;
    }
}
