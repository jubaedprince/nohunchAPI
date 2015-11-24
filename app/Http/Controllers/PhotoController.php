<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Storage;
use App\Photo;
use PhpSpec\Exception\Exception;
use Validator;
use File;
use Response;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user_id,$photo_number)
    {
        $user  = User::find($user_id);
        $photos = $user->photos()->get();

        if((count($photos)<=$photo_number-1)||$photo_number-1<0){
            return response()->json([
                'success'   =>  false,
                'message'   => "Photo not found",
            ]);
        }else{
            return $photos;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //authenticate and get user id
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $validator = Validator::make($request->all(), array(
            'photo' => 'mimes:jpg,jpeg,png',
        ));

        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            if($user->photo_count <5){
                $photo = $request->file('photo');
                $random_string = md5(microtime());
                $name = 'photos/'.$user->id . '-' . $random_string . '.' . $request->file('photo')->getClientOriginalExtension();


                Storage::put(
                    $name,
                    file_get_contents($request->file('photo')->getRealPath())
                );


                //Storage::disk('local')->put($name,File::get($photo));

                $photo = Photo::create([
                    'photo_location' => $name,
                    'user_id'        => $user_id
                ]);

                $user->photo_count = $user->photo_count + 1;

                $user->save();

                return response()->json([
                    'success'   =>  true,
                    'message'   => "Successfully uploaded",
                ]);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'message'   => "More than 5 photos are not allowed",
                ]);
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($photo_number)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $photos = $user->photos()->get();

        if((count($photos)<=$photo_number-1)||$photo_number-1<0){
            return response()->json([
                'success'   =>  false,
                'message'   => "Photo not found",
            ]);
        }else{
            try{
                $photo = $photos[$photo_number-1];
                $photo->delete();
                return response()->json([
                    'success'   =>  true,
                    'message'   => "Photo deleted successfully",
                ]);
            }catch (Exception $e){
                return response()->json([
                    'success'   =>  false,
                    'message'   => "Something went wrong",
                ]);
            }

        }
    }

    public function photoCount($user_id){
        $user  = User::find($user_id);
        $photos = $user->photos()->get();
        return count($photos);
    }
}
