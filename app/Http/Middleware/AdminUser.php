<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminUser{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard='admin_user'){
        $admin_user = Auth::guard($guard)->user();
        // $admin_user = ['admin_id'=>1];
        if( !empty($admin_user['admin_id']) ){
            return $next($request);
        } else {
            // return redirect()->guest('admin-panel/login');
            return redirect('/admin-panel');
        }
    }
}
