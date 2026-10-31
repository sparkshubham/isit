<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedPost extends Model
{
    use HasFactory;
    /*
    * Get User 
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    /*
    * Get Post 
    */
    public function getPost()
    {
        return $this->belongsTo('App\Models\Post','post_id');
    }
}
