<?php

namespace App\Providers;

use App\Question;
use Illuminate\Support\ServiceProvider;
use JWTAuth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         Question::creating( function ($attributes){
             $user = JWTAuth::parseToken()->authenticate();
             $user_id = $user->id;
             $question_exists = Question::where('user_id',$user_id)->get();

             if(!$question_exists->isEmpty()){
                 $published_questions = Question::where('user_id', $user_id)->where('is_published', true)->first();
                     if($published_questions!=null){
                         $published_questions->is_published = false;
                         $published_questions->save();
                     }
             }
        
         });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
