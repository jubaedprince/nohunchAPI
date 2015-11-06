<?php

use Illuminate\Database\Seeder;
use App\Answer;

class CreateAnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('answers')->delete();

        $answers = array(
            ['text' => 'answer1', 'user_id' => 3, 'question_id'=>1],
            ['text' => 'answer2', 'user_id' => 2, 'question_id'=>2],
            ['text' => 'answer3', 'user_id' => 1, 'question_id'=>2],
            ['text' => 'answer4', 'user_id' => 4, 'question_id'=>1],

        );

        // Loop through each user above and create the record for them in the database
        foreach ($answers as $answer)
        {
            Answer::create($answer);
        }
    }
}
