<?php

namespace App\Models;

use App\Models\User;
use App\Models\Remark;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $casts = [
        'qnas' => 'array'
    ];

    protected $guarded = [];

    public function remarks(){
        return $this->morphMany(Remark::class,'remarkable');
    }

    public function followups(){
        return $this->hasMany(Followup::class, 'lead_id')->orderBy('created_at');
    }

    // public function answers(){
    //     return $this->hasMany(Answer::class, 'lead_id');
    // }

    public function assigned(){
        return $this->hasOne(User::class,'id','assigned_to');
    }

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function appointment(){
        return $this->hasOne(Appointment::class, 'lead_id', 'id');
    }

    public function chats(){
        return $this->hasMany(Chat::class, 'lead_id');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function center(){
        return $this->belongsTo(Center::class, 'center_id', 'id');
    }

    public function scopeForHospital($query, $hospitalId)
    {
        $query->where('hospital_id', $hospitalId);
    }

    public function scopeForAgent($query, $agentId)
    {
        $query->where('assigned_to', $agentId);
    }
}
