<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Validator;


class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //return all answers by logged in user
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        $answers = Answer::where('user_id',$user_id)->get();

        //add the question to each answer
        for($i=0;$i<count($answers);$i++){
            array_add($answers[$i],'question',Question::where('id',$answers[$i]->question_id)->get());
        }

        return response()->json([
            'answers'   =>  $answers,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $question = Question::find($request['question_id']);
        $question_owner = $question->owner();

        //validate data
        $validator = Validator::make($request->all(), array(
            'text' => 'required',
            'question_id' => 'required',
        ));

        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            $previousAnswer = Answer::where('question_id',$request['question_id'])->where('user_id',$user_id)->get();
            if(count($previousAnswer)>0){
                return response()->json([
                    'success'   =>  false,
                    'message'   => "You've already answered this question before.",
                    'answer'  => $request['text'],
                ]);
            }else{
                Answer::create([
                    'text' => $request['text'],
                    'user_id' => $user_id,
                    'question_id' => $request['question_id'],
                ]);
                $user->addFollowing($question_owner);
            }

            return response()->json([
                'success'   =>  true,
                'message'   => "Answer Added Successfully",
                'answer'  => $request['text'],
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

        $validator = Validator::make($request->all(), array(
            'text' => 'required|max:255',
        ));

        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            $answer = Answer::find($id);
            if($answer==null){
                return response()->json([
                    'success'   =>  false,
                    'message'  =>  'Answer not found',
                ]);
            }
            if($answer->currentUserIsOwner()){
                $answer->text = $request['text'];
                $answer->save();
                return response()->json([
                    'success'   =>  true,
                    'message'  =>  'Answer updated successfully',
                ]);
            }
            else{
                return response()->json([
                    'success'   =>  false,
                    'message'  =>  'You are not authorized',
                ]);
            }
        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $answer = Answer::find($id);
        if($answer==null){
            return response()->json([
                'message'  =>  'Answer not found',
            ]);
        }
        if($answer->currentUserIsOwner()){
            $answer->delete();
            return response()->json([
                'success'   =>  true,
                'message'   => "Answer Deleted Successfully.",
            ]);
        }
        else{
            return response()->json([
                'success'   =>  false,
                'message'   => "Answer was not deleted.",
            ]);
        }
    }

}
