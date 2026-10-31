<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'users_notifications';
    use HasFactory;
    /*
    * Get Sender
    */
    public function getSender()
    {
        return $this->belongsTo('App\Models\User','sender_id');
    }
    /*
    * Get Receiver
    */
    public function getReceiver()
    {
        return $this->belongsTo('App\Models\User','receiver_id');
    }
    /*
    * Get Post
    */
    public function getPost()
    {
        return $this->belongsTo('App\Models\Post','other_id')->whereRaw('notification_type IN (1,2,4,8,3)');
    }
    /*
    * Get Event
    */
    public function getEvent()
    {
        return $this->belongsTo('App\Models\Event','other_id')->whereRaw('notification_type IN (6,7)');
    }
    /*
    * Get notification
    */
    public function getNotification()
    {
        return $this->belongsTo('App\Models\Notification','notification_type');
    }
    public function getCreatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['created_at']);
    }
}
