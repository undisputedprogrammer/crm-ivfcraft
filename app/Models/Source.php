<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['hospital_id','code','name', 'is_enabled'];

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }
}
