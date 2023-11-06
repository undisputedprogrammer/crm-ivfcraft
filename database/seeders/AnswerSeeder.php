<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Lead;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = Lead::all();
        $questions = Question::all();

        foreach($leads as $lead){

            foreach($questions as $question) {
                Answer::create([
                    'question_id'=>$question->id,
                    'lead_id'=>$lead->id,
                    'question_code'=>$question->question_code,
                    'answer'=>fake()->sentence(),
                ]);
            }
        }
    }
}
