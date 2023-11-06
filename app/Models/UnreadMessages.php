<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnreadMessages extends Model
{
    protected $table = 'unread_messages';
    use HasFactory;
    protected $fillable = ['chat_id','lead_id','count'];
}
