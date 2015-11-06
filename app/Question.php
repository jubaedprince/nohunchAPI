<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Facades\JWTAuth;

class Question extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['text', 'user_id', 'location', 'is_published'];

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
}
