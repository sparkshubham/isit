<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;
class Event extends Model
{
    use HasFactory;
    protected $appends = ['is_saved'];
    /*
    * Get User 
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    /*
    * Get user who saved the post 
    */
    public function getSavedevent()
    {
        return $this->hasMany('App\Models\SavedEvent','event_id');
    }
    public function getIsSavedAttribute()
    {
        $request = Request();
        $userId = isset(Auth::user()->id) ? Auth::user()->id : NULL;
        if($userId == NULL){
            $userId = 0;
        }
        $eventId = $this->id;
        $count = $this->getSavedevent()->whereRaw('event_id = '.$eventId.' AND user_id ='.$userId)->count(); 
        return (($count == 0) ? 0 : 1);
    }
    public function getStartTimeAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['start_time']);
    }
    public function getEndTimeAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['end_time']);
    }
    public function getDateAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['date']);
    }
}
