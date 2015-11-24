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

    public function getUser(){
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success'   =>  false,
                'message'   => "please log in to view this profile",
            ]);
        }
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

    //friend
    public function addFriend($user_id){
        $friend = User::find($user_id);
        JWTAuth::parseToken()->authenticate()->addFriend($friend);
        return "success";
    }

    public function removeFriend($user_id){
        $friend = User::find($user_id);
        JWTAuth::parseToken()->authenticate()->removeFriend($friend);
        return "success";
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
    public function addFollowing($user_id){
        $following = User::find($user_id);
        $this_user = JWTAuth::parseToken()->authenticate();

        if($this_user->isFollowing($user_id)){
            return "you are already following";
        }else{
            $this_user->addFollowing($following);
            if($following->isFollowing($this_user->id)){//both follow each other
                $this_user->addFriend($following);
                return "you are now friends.";
            }
            return "success";
        }
    }

    public function getAllFollowing(){
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json([
            'following'   =>  $user->getAllFollowings(),
        ]);
    }

    public function isFollowing($user){
        $this_user = JWTAuth::parseToken()->authenticate();
        return $this_user->isFollowing($user);
    }

    //follower
    public function getAllFollower(){
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json([
            'follower'   =>  $user->getAllFollowers(),
        ]);
    }

    //remove follower
    public function removeFollower($user_id){
        $user = User::find($user_id);
        $this_user = JWTAuth::parseToken()->authenticate();
        $this_user->removeFollower($user);
        $this_user->removeAnswersBy($user);
        return "success";
    }


}
