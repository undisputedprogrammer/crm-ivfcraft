<?php

namespace App\Models;

use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;
    protected $fillable = ['question_id','lead_id','question_code','answer'];
    protected $with = ['question'];

    public function question(){
        return $this->hasOne(Question::class,'id','question_id');
    }
}
