<?php

use Illuminate\Database\Seeder;

class CreateFriendsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('friends_users')->delete();

        $answers = array(
            ['text' => 'hello', 'user_id' => 2, 'question_id'=>1],
            ['text' => 'cool', 'user_id' => 1, 'question_id'=>2],
            ['text' => 'eytto', 'user_id' => 2, 'question_id'=>2],
            ['text' => 'hiiii', 'user_id' => 1, 'question_id'=>1],

        );

        // Loop through each user above and create the record for them in the database
        foreach ($answers as $answer)
        {
            Question::create($answer);
        }
    }
}
