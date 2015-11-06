<?php

use Illuminate\Database\Seeder;
use App\Question;

class CreateQuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->delete();

        $questions = array(
            ['question' => 'question1?', 'user_id' => 3,],
            ['question' => 'question2?', 'user_id' => 2,],
            ['question' => 'question3?', 'user_id' => 4,],
            ['question' => 'question4?', 'user_id' => 1,],

        );

        // Loop through each user above and create the record for them in the database
        foreach ($questions as $question)
        {
            Question::create($question);
        }
    }
}
