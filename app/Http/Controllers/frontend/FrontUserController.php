<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Illuminate\Routing\Route;

use App\Models\OauthAccessTokens;
use App\Models\User;
use Carbon\Carbon;
use Response;
use DataTables;
use DB;
use Auth;

class FrontUserController extends Controller{
    public function __construct(Route $route){
        // $this->middleware(function ($request, $next) use ($route){
        //     // Getting controller function
        //     $routeNameStr = $route->getActionName();
        //     $routeName = explode('@',$routeNameStr);

        //     $moduleName = request()->segment(2);
        //     return $next($request);
        // });
    }


    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 30 Oct 2019
     */
    public function index()
    {
       
    }

}
