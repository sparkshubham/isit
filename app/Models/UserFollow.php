<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasFactory;
    protected $table = 'users_follows';
    protected $appends = ['follower_count','post_count','following_count','post_follower_count'];
    /*
    * Get Follower
    */
    public function getFollower()
    {
        return $this->belongsTo('App\Models\User','follower_id');
    }
    /*
    * Get User
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    /*
    * Get User post
    */
    public function getUserPost()
    {
        return $this->hasMany('App\Models\Post','user_id', 'user_id')->where('deleted_at','=', NULL)->whereRaw('post_type NOT IN(4,5)');
    }
    /*
    * Get User post
    */
    public function getFollowerPost()
    {
        return $this->hasMany('App\Models\Post','user_id', 'follower_id')->where('deleted_at','=', NULL)->WhereRaw('post_type NOT IN(4,5)');
    }
    public function getFollowerCountAttribute()
    {
        $request = Request();
        $followerId = $this->user_id;
        $count = $this->whereRaw('follower_id = '.$followerId.' AND status = 1')->count(); 
        return $count;
        // return (($count == 0) ? 0 : 1);
    }
    public function getFollowingCountAttribute()
    {
        $request = Request();
        $followerId = $this->follower_id;
        $count = $this->whereRaw('follower_id = '.$followerId.' AND status = 1')->count(); 
        return $count;
        // return (($count == 0) ? 0 : 1);
    }
    public function getPostCountAttribute()
    {
        $request = Request();
        $followerId = $this->user_id;
        $count = $this->getUserPost()->count(); 
        return $count;
    }
    public function getPostFollowerCountAttribute()
    {
        $request = Request();
        $followerId = $this->user_id;
        $count = $this->getFollowerPost()->count(); 
        return $count;
    }
    /*
    * Get Follower Stories
    */
    public function getFollowerStories()
    {
        return $this->hasMany('App\Models\UserStory','user_id', 'follower_id')->where('deleted_at','=', NULL);
    }
    /*
    * Get User Stories
    */
    public function getUserStory()
    {
        return $this->hasMany('App\Models\UserStory','user_id', 'user_id')->where('deleted_at','=', NULL);
    }
}
