<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalChat extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;
    public $timestamps = false;

    protected $guarded = [];

    protected $with = ['sender'];

    protected $appends = [
        'chat_pics',
        'display_time'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function getMediaStorage(): array
    {
        return [
            'chat_pics' => [
                'disk' => 'local',
                'folder' => '/images/chat_pics'
            ]
        ];
    }

    public function chatPics(): Attribute
    {
        return Attribute::make(
            get: function($val) {
                return $this->getAllMediaForDisplay('chat_pics');
            }
        );
    }

    public function displayTime(): Attribute
    {
        return Attribute::make(
            get: function($val, $attributes) {
                $datetime = Carbon::createFromTimestamp($this->created_at);
                return [
                    'full' => $datetime->format('Y-M-d H:i:s'),
                    'year' => $datetime->year,
                    'month' => $datetime->shortMonthName,
                    'date' => $datetime->format('d'),
                    'time' => $datetime->format('h:i a')
                ];
            }
        );
    }
}
