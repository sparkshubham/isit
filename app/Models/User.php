<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable,HasApiTokens,HasRoles;
    use SoftDeletes;
    protected $appends = ['follower_count','following_count','is_follow','is_block','is_block_by_you','twillio_identity'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','email', 'password','first_name','last_name','password_reset_token','is_verified','verifiy_token','profile_picture','bio_text','is_public_visibility','status','device_id','last_login','username','twilio_s_id','gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /*
    * Get Post 
    */
    public function getPost()
    {
        return $this->hasMany('App\Models\Post','user_id');
    }
    /*
    * Get Follower
    */
    public function getFollower()
    {
        return $this->hasMany('App\Models\UserFollow','follower_id');
    }
    /*
    * Get Follower
    */
    public function getFollowing()
    {
        return $this->hasMany('App\Models\UserFollow','user_id');
    }
    /*
    * Get Follower
    */
    public function getBlockedUser()
    {
        return $this->hasMany('App\Models\BlockUser','block_user_id');
    }
    /*
    * Get Follower
    */
    public function getHobbies()
    {
        return $this->hasMany('App\Models\UserHobby','user_id');
    }
    public function getFollowerCountAttribute(){
        $request = Request();
        $followerId = $this->id;
        $count = $this->getFollower()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('follower_id = '.$followerId.' AND status = 1')->count(); 
        return $count;
        // return (($count == 0) ? 0 : 1);
    }
    public function getFollowingCountAttribute(){
        $request = Request();
        $followerId = $this->id;
        // echo $followerId;exit;
        $count = $this->getFollowing()->whereHas('getFollower', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('users_follows.user_id = '.$followerId.' AND users_follows.status = 1')->count(); 
        return $count;
        // return (($count == 0) ? 0 : 1);
    }
    public function getIsFollowAttribute(){
        $request = Request();
        $followerId = $this->id;        
        $userId = ((Auth::user() != NULL && Auth::user()->id != null) ? Auth::user()->id : 0);
        $arr = $this->getFollower()->select(['user_id','follower_id','status'])->whereRaw("user_id = ".$userId." AND follower_id = ".$followerId)->first(); 
        if($arr == null ){
            return 0;
        }
        $arr = $arr->toArray();
        // prd($arr);
        if(empty($arr)){
            return 0;
        }else if($arr['status'] == 1){
            return 1;
        }else if($arr['status'] == 2){
            return 2;
        }else {
            return 0;
        }
        // return $count;
        // return (($count == 0) ? 0 : 1);
    }
    public function getIsBlockAttribute(){
        $request = Request();
        $blockedUserId = $this->id;
        $userId = ((Auth::user() != NULL && Auth::user()->id != null) ? Auth::user()->id : 0);
        $arr = $this->getBlockedUser()->select(['id','user_id','block_user_id'])->whereRaw("user_id = ".$userId." AND block_user_id = ".$blockedUserId)->first();
        if($arr == null ){
            return 0;
        }else{
            return 1;
        }
    }
    public function getIsBlockByYouAttribute(){
        $request = Request();
        $blockedUserId = $this->id;
        $userId = ((Auth::user() != NULL && Auth::user()->id != null) ? Auth::user()->id : 0);
        $arr = $this->getBlockedUser()->select(['id','user_id','block_user_id','block_by'])->whereRaw("user_id = ".$userId." AND block_user_id = ".$blockedUserId." AND block_by =".$userId)->first();
        if($arr == null ){
            return 0;
        }else{
            return 1;
        }
    }
    public function getTwillioIdentityAttribute(){
        $env = env("APP_ENV");
        $userId = $this->id;
	    return $env.'_'.$userId;
    }
}
