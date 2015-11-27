<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'age', 'location','photo_count', 'points'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['question','photos', 'is_following'];

    public function getQuestionAttribute(){
        $questions = Question::where('user_id',$this->id)->where('is_published',true)->first();
         return $questions ;
    }


    public function friends()
    {
        return $this->belongsToMany('App\User', 'friends_users', 'user_id', 'friend_id');
    }

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function addFriend(User $user)
    {
        $this->friends()->attach($user->id);
        $user->friends()->attach($this->id);
    }

    public function removeFriend(User $user)
    {
        $this->friends()->detach($user->id);
        $this->followings()->detach($user->id);
        $user->friends()->detach($this->id);
        $user->followings()->detach($this->id);
    }

    public function getAllFriends(){
        return $this->friends()->get();
    }

    public function getPhotosAttribute(){
        $photos = $this->photos()->get();
        return $photos ;
     }

    public function getIsFollowingAttribute(){
        $user = JWTAuth::parseToken()->authenticate();
        if($user->followings()->find($this->id)!=null){
            return true;
        }else{
            return false;
        }
    }

    //followers
    public function followers()
    {
        return $this->belongsToMany('App\User', 'followers_users', 'user_id', 'follower_id');
    }

    public function getAllFollowers(){
        return $this->followers()->get();
    }

    public function removeFollower(User $user){
        $this->followers()->detach($user->id);

        try{
            $answers = Answer::where('follower_user',$user->id.'_'.$this->id)->get();
            if($answers){
                foreach($answers as $ans){
                    $ans->delete();
                }
            }
        }catch (Exception $ex){

        }
    }

    //folllowing
    public function followings()
    {
        return $this->belongsToMany('App\User', 'followers_users', 'follower_id', 'user_id');
    }

    public function addFollowing(User $user){
        $this->followings()->attach($user->id);
    }

    public function removeFollowing(User $user){
        $this->followings()->detach($user->id);
        try{
            $answers = Answer::where('follower_user',$user->id.'_'.$this->id)->get();
            if($answers){
                foreach($answers as $ans){
                    $ans->delete();
                }
            }
        }catch (Exception $ex){

        }
    }

    public function getAllFollowings(){
        return $this->followings()->get();
    }

    public function isFollowing($user){
        if($this->followings()->find($user->id)!=null){
            return true;
        }else{
            return false;
        }
    }


    //location
    public function setLocation($location){
        if ($location == ""){
            $this->location = null;
            $this->save();
        }
        else{
            $this->location = $location;
            $this->save();
        }

    }

    public function removeAnswersBy($user){
        try{
            $answers = Answer::where('follower_user',$user->id.'_'.$this->id)->get();
            if($answers){
                foreach($answers as $ans){
                    $ans->delete();
                }
            }
        }catch (Exception $ex){

        }
    }
}
