<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function ($table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('answer_id')
                ->references('id')->on('answers')
                ->onDelete('cascade');
        });

        Schema::table('answers', function ($table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('question_id')
                ->references('id')->on('questions')
                ->onDelete('cascade');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function ($table) {
            $table->dropForeign('answer_id_user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');


        });

        Schema::table('answers', function ($table) {
            $table->dropForeign('user_id_question_id')
                ->references('id')->on('users')
                ->onDelete('cascade');


        });
    }
}
