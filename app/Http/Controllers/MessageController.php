<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use JWTAuth;

class MessageController extends Controller
{
    /**
     * Show all of the message threads to the user
     *
     * @return mixed
     */
    public function index()
    {
        $currentUserId = JWTAuth::parseToken()->authenticate()->id;
        // All threads, ignore deleted/archived participants
//        $threads = Thread::getAllLatest()->get();
        // All threads that user is participating in
         $threads = Thread::forUser($currentUserId)->latest('updated_at')->get()->unique();
        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages($currentUserId)->latest('updated_at')->get();
//        return view('messenger.index', compact('threads', 'currentUserId'));
        return response()->json([
            'success'   =>  true,
            'message'   => "Successful",
            'threads'      => $threads
        ]);
    }
    /**
     * Shows a message thread
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
//            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return response()->json([
                'success'   =>  'false',
                'message'   =>  'The thread with ID: ' . $id . ' was not found.'
            ]);
        }
        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();
        // don't show the current user in list
        $userId = JWTAuth::parseToken()->authenticate()->id;

        $user_has = $thread->participantsUserIds()->unique()->search($userId);

        if($user_has!==false){
            //check if current user is  a participant of the thread
            $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();
            $thread->markAsRead($userId);
            $friend_name = $users->first();
            //add participant names to thread.

            return response()->json([
                'success'   =>  true,
                'message'   => "Successful",
                'messages'  => $thread->messages,
                'friend'    => $friend_name,
                'my_name'   =>  JWTAuth::parseToken()->authenticate()->name
            ]);
        }else{
            return response()->json([
                'success'   =>  'false',
                'message'   =>  'You are not permitted to view this thread.'
            ]);
        }


    }

    /**
     * Stores a new message thread
     *
     * @return mixed
     */
    public function store()
    {
        $input = Input::all();
        $recipient_id = $input['recipient'];
        $sender_id = JWTAuth::parseToken()->authenticate()->id;

        if($sender_id==$recipient_id){
            return response()->json([
                'success'   =>  'false',
                'message'   =>  'Error. Your ID and receipient ID cannot be same.'
            ]);
        }

        if($sender_id>$recipient_id){
            $subject = $recipient_id . ' ' . $sender_id;
            $thread = Thread::firstOrCreate(
                [
                    'subject' => $subject
                ]
            );
        }else{
            $subject = $sender_id . ' ' . $recipient_id;
            $thread = Thread::firstOrCreate(
                [
                    'subject' => $subject
                ]
            );
        }


        // Message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $sender_id,
                'body'      => $input['message'],
            ]
        );

        if(!$thread->participantsUserIds()->unique()->search($recipient_id)){ //if first time thread is created, no record of participants in thread
            // Sender
            Participant::create(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => $sender_id,
                    'last_read' => new Carbon
                ]
            );
            // Recipients
            $thread->addParticipants([$recipient_id]);
        }

        return response()->json([
        'success'   =>  'true',
        'message'   =>  'Message sent.'
    ]);
    }
//    /**
//     * Adds a new message to a current thread
//     *
//     * @param $id
//     * @return mixed
//     */
//    public function update($id)
//    {
//        try {
//            $thread = Thread::findOrFail($id);
//        } catch (ModelNotFoundException $e) {
//            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
//            return redirect('messages');
//        }
//        $thread->activateAllParticipants();
//        // Message
//        Message::create(
//            [
//                'thread_id' => $thread->id,
//                'user_id'   => Auth::id(),
//                'body'      => Input::get('message'),
//            ]
//        );
//        // Add replier as a participant
//        $participant = Participant::firstOrCreate(
//            [
//                'thread_id' => $thread->id,
//                'user_id'   => Auth::user()->id
//            ]
//        );
//        $participant->last_read = new Carbon;
//        $participant->save();
//        // Recipients
//        if (Input::has('recipients')) {
//            $thread->addParticipants(Input::get('recipients'));
//        }
//        return redirect('messages/' . $id);
//    }
}
