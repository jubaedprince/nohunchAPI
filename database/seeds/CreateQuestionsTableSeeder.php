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
            ['text' => 'hi?', 'user_id' => 2, 'location'=>'Dhaka'],
            ['text' => 'what up?', 'user_id' => 1, 'location'=>'Dhaka'],
            ['text' => 'kire?', 'user_id' => 2, 'location'=>'Dhaka'],
            ['text' => 'ASL?', 'user_id' => 1, 'location'=>'Dhaka'],

        );

        // Loop through each user above and create the record for them in the database
        foreach ($questions as $question)
        {
            Question::create($question);
        }
    }
}
