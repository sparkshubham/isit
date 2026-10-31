<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    /*
    * Get Country
    */
    public function Country()
    {
      return $this->belongsTo('App\Models\Country','country_id');
    }
    /*
    * Get Category
    */
    public function Category()
    {
        return $this->belongsTo('App\Models\Category','category_id');
    }
    /*
    * Get Media 
    */
    public function getMedia()
    {
        return $this->hasMany('App\Models\ProductMedia','product_id');
    }
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
    public function getUpdatedAtAttribute()
    {
        // dd(strtotime($this->attributes['created_at']));
        return strtotime($this->attributes['updated_at']);
    }
}
