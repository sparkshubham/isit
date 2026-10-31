<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Response;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {  
        // var_dump(collect($request->route()->middleware())->contains('api'));exit;
        if (! ($request->expectsJson() || collect($request->route()->middleware())->contains('api'))) {
            $url = request()->url();
            $pos = strpos($url, '/admin-panel'); 
            if($pos !== false){
                return route('admin-login');
            }else{
                return route('login');
            }
        }else{
            abort(response()->json(['message'=>'Unauthorized',"code"=>"401"], 401));
            // return redirect('cyberSecuritySummit')->with('message', json_encode(['success'=>'Payment sucessfull!']));
            // echo Response::json(['dfsdfsf'], 400);
        }
    }
}
