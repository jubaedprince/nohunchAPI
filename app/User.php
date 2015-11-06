<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

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
    protected $fillable = ['name', 'email', 'password', 'age'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];


    public function friends()
    {
        return $this->belongsToMany('App\User', 'friends_users', 'user_id', 'friend_id');
    }

    public function addFriend(User $user)
    {
        $this->friends()->attach($user->id);
        $user->friends()->attach($this->id);
    }

    public function removeFriend(User $user)
    {
        $this->friends()->detach($user->id);
        $user->friends()->detach($this->id);
    }

    public function getAllFriends(){
        return $this->friends()->get();
    }

    //followers
    public function followers()
    {
        return $this->belongsToMany('App\User', 'followers_users', 'user_id', 'follower_id');
    }

    public function addFollower(User $user){
        $this->followers()->attach($user->id);
    }

    public function removeFollowers(User $user){
        $this->followers()->detach($user->id);
    }

    public function getAllFollowers(){
        return $this->followers()->get();
    }

    //folllowing
    public function followings()
    {
        return $this->belongsToMany('App\User', 'followers_users', 'follower_id', 'user_id');
    }

    public function getAllFollowings(){
        return $this->followings()->get();
        //return $this->followers()->get();
    }

}
