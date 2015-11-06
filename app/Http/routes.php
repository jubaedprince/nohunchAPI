<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return "Welcome to NoHunch. Please go to /api to access the api.";
});

//Write all the API Endpoints in the below group.
Route::group(['prefix'=>'/api'], function(){
    Route::get('/', function(){
       return response()->json([
           'success' => true,
           'message' => "hey there! You are in the api home"
       ]);
   });

    //user authentication & registration
    Route::post('register', 'AuthenticateController@register');
    Route::post('authenticate', 'AuthenticateController@authenticate');
    //get current user information
    Route::get('user', 'AuthenticateController@getAuthenticatedUser');
    //add friend
    Route::get('user/friend/{user_id}', 'AuthenticateController@addFriend');
    //get all friends of logged in user
    Route::get('user/friends', 'AuthenticateController@getAllFriend');


    //add follower
    Route::get('user/follower/{follower}','AuthenticateController@addFollower');
    //get all followers
    Route::get('user/followers','AuthenticateController@getAllFollower');


    //endpoints accessable by only authenticated users.

    Route::group(['middleware' => 'jwt.auth'], function(){
       Route::get('authenticatedRoute', function(){
            return "You are in authenticated route.";
       });

        //question
        Route::resource('question', 'QuestionController');
        //set active question
        Route::get('user/question/active/{question}', 'QuestionController@setActiveQuestion');
        //get all answers of a question
        Route::get('question/{question}/answers','QuestionController@getAllAnswers');
        //get all questions by logged in user
        Route::get('user/questions', 'QuestionController@getAll');

        //answer
        Route::resource('answer', 'AnswerController');
    });




});

