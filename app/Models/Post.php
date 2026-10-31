<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'description',
        'post_type'
      ];
    protected $appends = ['is_saved','is_liked','total_likes','is_commented','total_comments','is_reported'];
    /*
    * Get Country
    */
    public function Country()
    {
      return $this->belongsTo('App\Models\Country','country_id');
    }
    /*
    * Get Category
    */
    public function Category()
    {
        return $this->belongsTo('App\Models\Category','category_id');
    }
    /*
    * Get Media 
    */
    public function getMedia()
    {
        return $this->hasMany('App\Models\PostMedia','post_id');
    }
    /*
    * Get User 
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
    public function getUpdatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['updated_at']);
    }
    /*
    * Get user who saved the post 
    */
    public function getSavedpost()
    {
        return $this->hasMany('App\Models\SavedPost','post_id');
    }
    /*
    * Get user who liked the post 
    */
    public function getLikedpost()
    {
        return $this->hasMany('App\Models\PostLike','post_id');
    }

    /*
    * Get user who commented the post 
    */
    public function getCommentedpost()
    {
        return $this->hasMany('App\Models\PostComment','post_id');
    }
    /*
    * Get user who liked the post 
    */
    public function getReportedpost()
    {
        return $this->hasMany('App\Models\ReportedPost','post_id');
    }
    public function hasSharedPost()
    {
        return $this->getSavedpost()->exists();
    }
    public function getIsSavedAttribute()
    {
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getSavedpost()->whereRaw('post_id = '.$postId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    public function getIsLikedAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getLikedpost()->whereRaw('post_id = '.$postId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    
    public function getTotalLikesAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getLikedpost()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('post_id = '.$postId)->count(); 
        return $count;
    }
    public function getIsCommentedAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getCommentedpost()->whereRaw('post_id = '.$postId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    public function getTotalCommentsAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getCommentedpost()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('post_id = '.$postId.' AND parent_comment_id IS NULL')->count(); 
        return $count;
    }
    public function getIsReportedAttribute()
    {
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $postId = $this->id;
        $count = $this->getReportedpost()->whereRaw('post_id = '.$postId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
}
