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

    //endpoints accessable by only authenticated users.

    Route::group(['middleware' => 'jwt.auth'], function(){
       Route::get('authenticatedRoute', function(){
            return "You are in authenticated route.";
       });
        //TODO:: add this to local code and push later

        //get all users
        Route::get('users', 'UserController@getAllUsers');
            
        Route::group(['prefix' => 'user'], function(){
            //get current user information
            Route::get('/', 'AuthenticateController@getAuthenticatedUser');
            //set location
            Route::post('location', 'UserController@setLocation');
            //get all photos of a user
            Route::get('{user}/photo', 'PhotoController@index');
            //upload a photo
            Route::post('photo','PhotoController@store');
        });

        //question
        Route::resource('question', 'QuestionController');
        //get all answers of a question
        Route::get('question/{question}/answers','QuestionController@getAllAnswers');
        //add friend
        Route::get('friend/{user_id}', 'AuthenticateController@addFriend');
        //get all friends of logged in user
        Route::get('friends', 'AuthenticateController@getAllFriend');
        //add follower
        Route::get('follower/{follower}','AuthenticateController@addFollower');
        //get all followers
        Route::get('followers','AuthenticateController@getAllFollower');
        //get all questions by logged in user
        Route::get('questions', 'QuestionController@getAll');


        //answer
        Route::resource('answer', 'AnswerController');
    });




});

