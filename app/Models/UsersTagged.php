<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersTagged extends Model
{
    protected $table = 'users_tagged';
    use HasFactory;
}
