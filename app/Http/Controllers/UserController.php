<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
 //TODO:: add this to local code and push later
use App\User;
class UserController extends Controller
{
    public function setLocation(Request $request){

        $validator = Validator::make($request->all(), array(
            'location' => 'string',
        ));

        if ($validator->fails()) {
            return response()->json([
                'success'   =>  false,
                'message'   => "Validation error.",
                'error'     => $validator->errors()->all(),
            ]);
        } else {

            $user = JWTAuth::parseToken()->authenticate();
            $user->setLocation($request->location);

            return response()->json([
                'success' => true,
                'message' => "Location changed successfully",
                'location' => $request->location,
            ]);
        }

    }

    public function getAllUsers(){

        $users = User::all();
//        return $users;
        $filtered = $users->filter(function ($item) {
            return !$item->is_following;
        });

        $filtered->all();
         return response()->json([
             'success' => true,
             'message' => "Users are found",
             'users' => $filtered,
            ]);
    }

    public function addPoint(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $user->points = $user->points + $request->input('amount');
        $user->save();
        return response()->json([
            'success' => true,
            'message' => "Users points added",
            'users' => $user,
        ]);
    }


}
