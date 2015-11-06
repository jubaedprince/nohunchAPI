<?php

use Illuminate\Database\Seeder;
use App\User;

class CreateUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $users = array(
            ['name' => 'Ryan Chenkie', 'email' => 'ryanchenkie@gmail.com', 'username'=>'username1','age'=>23, 'password' => Hash::make('secret')],
            ['name' => 'Chris Sevilleja', 'email' => 'chris@scotch.io', 'username'=>'username2', 'age'=>28, 'password' => Hash::make('secret')],
            ['name' => 'Holly Lloyd', 'email' => 'holly@scotch.io', 'username'=>'username3', 'age'=>45, 'password' => Hash::make('secret')],
            ['name' => 'Adnan Kukic', 'email' => 'jubaedprince@hotmail.com', 'username'=>'username4', 'age'=>22, 'password' => Hash::make('secret')],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}
