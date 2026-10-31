<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','event_id','user_id'
    ];
    /*
    * Get Post 
    */
    public function getEvent()
    {
        return $this->belongsTo('App\Models\Event','event_id');
    }
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
