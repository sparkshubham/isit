<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStoryView extends Model
{
    use HasFactory;
    protected $table = 'users_stories_views';
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
}
