<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Validator;


class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get user id
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $questions = Question::where('user_id',$user_id)->where('is_published',true)->get();

        return response()->json([
            'success'   =>  true,
            'question'  => $questions,
        ]);
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


        //validate data
        $validator = Validator::make($request->all(), array(
            'question' => 'required|max:255',
        ));

        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            Question::create([
                'question' => $request['question'],
                'user_id' => $user_id,
                'is_published' => true,
            ]);

            return response()->json([
                'success'   =>  true,
                'message'   => "Question Added Successfully",
                'question'  => $request['question'],
            ]);
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
        //authenticate and get user id
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $question = Question::findOrFail($id);
        $question_owner = $question->user_id;

        if($user_id == $question_owner){
            $question->delete();

            return response()->json([
                'success'   =>  true,
                'message'   => "Question Deleted Successfully.",
            ]);
        }else {
            return response()->json([
                'success'   =>  false,
                'message'   => "This Question is not Yours.",
            ]);
        }
    }

    //get all the questions of the logged in user
    public function getAll(Request $request)
    {
        //get user id
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $questions = Question::where('user_id',$user_id)->get();
        return response()->json([
            'question'   =>  $questions,
        ]);
    }

    //get all answers of a question
    public function getAllAnswers($question_id)
    {
        //get user id
        $question = Question::find($question_id);
        if($question==null){
            return response()->json([
                'success'   => true,
                'message'  =>  'nothing to show',
            ]);
        }
        if($question->currentUserIsOwner()){
            return response()->json([
                'success'   =>  true,
                'question'  =>  $question,
                'answers'   =>  $question->answers()->get(),
            ]);
        }
        else{
            return response()->json([
                'message'  =>  'You are not Authorized',
                'success'   => false,
            ]);
        }

    }

    public function setActiveQuestion($id){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $question_exists = Question::where('user_id',$user_id)->get();
        if(!$question_exists->isEmpty()){
            $published_questions = Question::where('is_published', true)->first();
            if($published_questions!=null){
                $published_questions->is_published = false;
                $published_questions->save();
            }
        }

        $question_exist = Question::where('id',$id)->get();
        if(!($question_exist->isEmpty())){
            $active_question = $question_exist[0];
            if($active_question->currentUserIsOwner()){
                $active_question->is_published = true;
                $active_question->save();

                return response()->json([
                    'success'   =>  true,
                    'message'   =>  'Question activated successfully.'
                ]);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'message'   =>  'Not your question!'
                ]);
            }
        }
        return response()->json([
            'success'   =>  false,
            'message'   =>  'Something went wrong!'
        ]);

    }

}
