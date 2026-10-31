<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHelp extends Model
{
    use HasFactory;
    /*
    * Get User 
    */
    public function getUser()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
