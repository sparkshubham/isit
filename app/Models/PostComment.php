<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
class PostComment extends Model
{
    use HasFactory;
    protected $table = 'posts_comments';
    protected $appends = ['total_replied','total_likes','is_liked'];
    protected $fillable = ['user_id', 'post_id', 'parent_comment_id', 'comment'];
    /*
    * Get User
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    /*
    * Get ParentComment
    */
    public function replies()
    {
        return $this->hasMany(PostComment::class,'parent_comment_id');
    }
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
    public function getTotalRepliedAttribute(){
        $request = Request();
        $commentId = $this->id;
        $count = $this->replies()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('parent_comment_id = '.$commentId)->count(); 
        return $count;
    }
    /*
    * Get user who liked the comment 
    */
    public function getLikedcomment()
    {
        return $this->hasMany('App\Models\CommentLike','comment_id');
    }
    public function getTotalLikesAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $commentId = $this->id;
        $count = $this->getLikedcomment()->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->whereRaw('comment_id = '.$commentId)->count(); 
        return $count;
    }
    public function getIsLikedAttribute(){
        $request = Request();
        $userId = Auth::user()->id;
        if($userId == NULL){
            $userId = 0;
        }
        $commentId = $this->id;
        $count = $this->getLikedcomment()->whereRaw('comment_id = '.$commentId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
}
