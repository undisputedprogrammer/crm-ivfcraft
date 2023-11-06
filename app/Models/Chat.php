<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['message','type','direction','lead_id','status','wamid','expiration_time'];

    public function template() {
        return $this->belongsTo(Message::class,'template_id','id');
    }

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }
}
