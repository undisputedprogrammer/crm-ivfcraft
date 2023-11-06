<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['lead'];

    public function lead(){
        return $this->hasOne(Lead::class,'id','lead_id');
    }

    public function remarks(){
        return $this->morphMany(Remark::class,'remarkable')->orderBy('created_at');
    }

    // public function followups(){
    //     return $this->hasMany(Followup::class,'lead_id','lead_id');
    // }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
