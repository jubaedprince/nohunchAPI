<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->integer('user_id')->unsigned();
            $table->boolean('is_published');
            $table->softDeletes();
            $table->timestamps();

//            $table->foreign('user_id')
//                ->references('id')->on('users')
//                ->onDelete('cascade');
//
//            $table->foreign('answer_id')
//                ->references('id')->on('answers')
//                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('questions');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
