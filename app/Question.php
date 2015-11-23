<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Facades\JWTAuth;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'questions';


    protected $fillable = ['question', 'user_id', 'is_published'];

    protected $dates = ['deleted_at'];

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function currentUserIsOwner(){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        if ($this->user_id == $user_id){
            return true;
        }else{
            return false;
        }
    }

    public function owner(){
        $user_id = $this->user_id;
        $user = User::find($user_id);
        return $user;
    }
}
