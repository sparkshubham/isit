<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','post_id','user_id'
    ];
    /*
    * Get Post 
    */
    public function getPost()
    {
        return $this->belongsTo('App\Models\Post','post_id');
    }
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
    
}
