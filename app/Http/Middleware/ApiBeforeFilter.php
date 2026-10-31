<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiBeforeFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $api_key = '39bdd6fd-8498-55c7-bcfc-8e227d324429';
        $publicApis = ['api.get-twilio-callback','api.send-notification-test','api.send-busy-notification','api.send-notification-event','api.misscall-notification'];
        $currentUrlName = Route::currentRouteName();
        // echo $currentUrlName;exit;
        if(!in_array($currentUrlName, $publicApis) && (empty($request->header('api-key')) || $request->header('api-key') != $api_key)){
            return getResponse(6000,401);
        }else{
            if(Auth::check()){
                $api_user = Auth::guard('api')->user();
                if(empty($api_user) || $api_user['status'] != 1){
                    if($api_user['status']==2){
                        return getResponse(612,401);
                    }else{
                        return $next($request);
                        return getResponse(603,401);
                    }
                }
                return $next($request);
            }else{
                return $next($request);
            }
        }
    }
}
