<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;
class UserStory extends Model
{
    protected $table = 'users_stories';
    use HasFactory;
    protected $appends = ['is_liked','total_likes','is_viewed','total_views'];
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
    /*
    * Get Sender
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    /*
    * Get user who liked the story 
    */
    public function getLikedstory()
    {
        return $this->hasMany('App\Models\UserStoryLike','story_id');
    }
    /*
    * Get user who liked the story 
    */
    public function getViewedstory()
    {
        return $this->hasMany('App\Models\UserStoryView','story_id');
    }
    public function getIsLikedAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $storyId = $this->id;
        $count = $this->getLikedstory()->whereRaw('story_id = '.$storyId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    public function getTotalLikesAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $storyId = $this->id;
        $count = $this->getLikedstory()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('story_id = '.$storyId)->count(); 
        return $count;
    }
    public function getIsViewedAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $storyId = $this->id;
        $count = $this->getViewedstory()->whereRaw('story_id = '.$storyId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    public function getTotalViewsAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $storyId = $this->id;
        $count = $this->getViewedstory()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('story_id = '.$storyId)->count(); 
        return $count;
    }
}
