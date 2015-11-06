<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\User;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json([
            'success'   =>  true,
            'message'   => "Successful",
            'token'      => $token
        ]);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json([
            'success'   =>  true,
            'message'   => "Successful",
            'user'      => $user
        ]);
    }

    public function register(Request $request){
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'age' => 'integer|required|min:18|max:120'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response()->json([
                'success'   =>  false,
                'message'   => "Failed",
                'error'     => $validator->errors()->all()
            ]);
        }

        return response()->json([
            'success'   =>  true,
            'message'   => "Success",
            'user'     => $this->create($request->all())
        ]);

    }


    public function addFriend($user_id){
        $friend = User::find($user_id);
        $user = JWTAuth::parseToken()->authenticate();
        //TODO: check if they are already friends and then add
        JWTAuth::parseToken()->authenticate()->addFriend($friend);

    }

    public function getAllFriend(){
        $user = JWTAuth::parseToken()->authenticate();
        return $user->getAllFriends();
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'age' => $data['age'],
            'photo_count'   => 0,
        ]);
    }


    //followers


    public function addFollower($user_id){
        $follower = User::find($user_id);
        $user = JWTAuth::parseToken()->authenticate();
        //TODO: check if he/she already follows and then add
        JWTAuth::parseToken()->authenticate()->addFollower($follower);

    }

    public function getAllFollower(){
        $user = JWTAuth::parseToken()->authenticate();
        $temp = User::find(19);
        return response()->json([
            'followers'   =>  $temp->getAllFollowings(),
        ]);
        //return $user->getAllFollowers();
    }


}
