<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model
{
    use HasFactory;
    protected $table = 'users_hobbies';
    /*
    * Get Sender
    */
    public function getHobby()
    {
        return $this->belongsTo('App\Models\Hobby','hobby_id');
    }

}
