<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            'ഏത്_തരം_കൺസൽട്ടേഷൻ_ആണ്_നോക്കുന്നത്_?',
            'വിവാഹം_കഴിഞ്ഞിട്ട്_എത്ര_വർഷമായി?',
        ];

        foreach($questions as $qn){
            $q = Question::create([
                'question_code'=>'qcode',
                'question'=>$qn,
            ]);
            $q->question_code = 'Q_'.$q->id;
            $q->save();
        }
    }
}
