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
            ['name' => 'Ryan Chenkie', 'email' => 'ryanchenkie@gmail.com','age'=>23, 'gender' => 'male', 'password' => Hash::make('secret')],
            ['name' => 'Chris Sevilleja', 'email' => 'chris@scotch.io', 'age'=>28, 'gender' => 'female', 'password' => Hash::make('secret')],
            ['name' => 'Holly Lloyd', 'email' => 'holly@scotch.io', 'age'=>45, 'gender' => 'female',  'password' => Hash::make('secret')],
            ['name' => 'Jubaed Prince', 'email' => 'jubaed@prince.com','age'=>22, 'gender' => 'male', 'password' => Hash::make('secret')],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}
