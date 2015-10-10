<?php

use Illuminate\Database\Seeder;

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

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        factory(App\Question::class, 50)->create();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
