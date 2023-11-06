<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;

    protected $fillable = ['remarkable_type','remarkable_id','remark','user_id'];

    public function remarkable()
    {
        return $this->morphTo();
    }

}
