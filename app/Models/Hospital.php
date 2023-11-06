<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory;

    protected $casts = [
        'main_cols' => 'array'
    ];

    // protected $appends = [
    //     'main_cols'
    // ];

    public function centers(){
        return $this->hasMany(Center::class, 'hospital_id','id');
    }

    public function mainCols(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                return json_decode($val);
            }
        );
    }

    public function leads(){
        return $this->hasMany(Lead::class, 'hospital_id', 'id');
    }

    public function users(){
        return $this->hasMany(User::class, 'hospital_id','id');
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
