<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Center extends Model
{
    use HasFactory;

    public function hospital(){
        return $this->hasOne(Hospital::class, 'hospital_id', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'user_has_centers');
    }

    public function doctors(){
        return $this->hasMany(Doctor::class, 'center_id','id');
    }

    public function agents()
    {
        $arr = [];
        foreach ($this->users as $u) {
            if($u->hasRole('agent')){
                $arr[] = $u;
            }
        }
        return $arr;
    }

    public function chatRoom(): MorphOne
    {
        return $this->morphOne(ChatRoom::class, 'chatable');
    }
}
