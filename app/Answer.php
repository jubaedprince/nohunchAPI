<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use JWTAuth;


class Answer extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['text', 'user_id', 'question_id'];

    protected $dates = ['deleted_at'];

    public function question()
    {
        return $this->belongsTo('App\Question');
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
}
