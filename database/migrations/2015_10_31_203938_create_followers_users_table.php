<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersUsersTable extends Migration
{

    public function up()
    {
        Schema::create('followers_users', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('follower_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('follower_id')->references('id')->on('users');

//            $table->primary(array('user_id', 'follower_id'));
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('followers_users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
