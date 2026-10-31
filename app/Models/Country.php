<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;


    public static function getCountryCodeList(){
    	return self::select(\DB::raw('id,CONCAT(name,"(+",code,")") as name'))
    		->get()->pluck('name','id')->toArray();

    }
}
