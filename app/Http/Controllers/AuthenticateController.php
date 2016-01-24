<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\User;
use Mail;

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

    public function inviteUser(Request $request){
        if ($this_user = JWTAuth::parseToken()->authenticate()) {
            $rules = [
                'email' => 'required|email|max:255|unique:users',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {

                return response()->json([
                    'success'   =>  false,
                    'message'   => "Enter a valid email",
                    'error'     => $validator->errors()->all()
                ]);
            }else{
                //TODO:: send email here
                $data = [
                    'name' => 'Sunny',
                    'email' => '',
                    'code' => ''
                ];

                $user = [
                    'name' => 'Sunny',
                    'email' => 'rudrozzal@gmail.com'
                ];

                Mail::queue('welcome', $data, function($message) use ($user){
                    $message->to($user->email, $user->name)->subject($user->name.', Please confirm your account @ Product Zap');
                });
            }
        }else{
            return response()->json([
                'success'   =>  false,
                'message'   => "please log in to invite someone.",
            ]);
        }
    }

    public function getUser($user_id){
        if (!$this_user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success'   =>  false,
                'message'   => "please log in to view this profile",
            ]);
        }else{
            $user = User::find($user_id);
            return $user;
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
            'age' => 'integer|required|min:18|max:120',
            'gender' => ''
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response()->json([
                'success'   =>  false,
                'message'   => "Failed",
                'error'     => $validator->errors()->all()
            ]);
        }
        $user = $this->create($request->all());
        $user->points = 10;
        $user->save();
        return response()->json([
            'success'   =>  true,
            'message'   => "Success",
            'user'     => $user
        ]);

    }

    //friend
    public function addFriend($user_id){
        $friend = User::find($user_id);
        JWTAuth::parseToken()->authenticate()->addFriend($friend);
        return response()->json([
            'success'   =>  true
        ]);
    }

    public function removeFriend($user_id){
        $friend = User::find($user_id);
        JWTAuth::parseToken()->authenticate()->removeFriend($friend);
        return response()->json([
            'success'   =>  true
        ]);
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
            'gender' => $data['gender']
        ]);
    }


    //followers
    public function addFollowing($user_id){
        $following = User::find($user_id);
        $this_user = JWTAuth::parseToken()->authenticate();

        if($this_user->isFollowing($following)){
            return response()->json([
                'success'   =>  true,
                'message'   => "you are already following."
            ]);
        }else{
            $this_user->addFollowing($following);
            if($following->isFollowing($this_user)){//both follow each other
                $this_user->addFriend($following);
                return response()->json([
                    'success'   =>  true,
                    'message'   => "you are now friends."
                ]);
            }
            return response()->json([
                'success'   =>  false
            ]);
        }
    }

    public function getAllFollowing(){
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json([
            'success'   =>  true,
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
        $followers = $user->getAllFollowers();

        foreach($followers as $follower){
            $ans = Answer::where('follower_user',$follower->id.'_'.$user->id)->orderBy('created_at', 'desc')->first()->text;
            array_add($follower, 'answer', $ans);
        }
        return response()->json([
            'success'   =>  true,
            'follower'   => $followers,
        ]);
    }

    //remove follower
    public function removeFollower($user_id){
        $user = User::find($user_id);
        $this_user = JWTAuth::parseToken()->authenticate();
        $this_user->removeFollower($user);

        return response()->json([
            'success'   =>  true
        ]);
    }


}
