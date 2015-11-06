<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
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
}
