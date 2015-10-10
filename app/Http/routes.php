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
           'message' => "You are in the api home"
       ]);
   });

    //user authentication & registration
    Route::post('register', 'AuthenticateController@register');
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('user', 'AuthenticateController@getAuthenticatedUser');

    //endpoints accessable by only authenticated users.

    Route::group(['middleware' => 'jwt.auth'], function(){
       Route::get('authenticatedRoute', function(){
            return "You are in authenticated route.";
       });

        Route::resource('question', 'QuestionController');
    });




});
