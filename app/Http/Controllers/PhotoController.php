<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Storage;
use App\Photo;
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
    public function index($user_id)
    {
        $user  = User::find($user_id);
        $photos = $user->photos()->get();

        foreach($photos as $photo){
            $file = Storage::disk('local')->get($photo->photo_location);
            return Response::make($file, 200, ['Content-Type'=>'image/jpg']);
        }

        return ;

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
                Storage::disk('local')->put($name,File::get($photo));
                $photo = Photo::create([
                    'photo_location' => $name,
                    'user_id'        => $user_id
                ]);

                $user->photo_count = $user->photo_count + 1;

                $user->save();

                return "Done";
            }else{
                return "More than 5 photos";
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
    public function destroy($id)
    {
        //
    }
}
