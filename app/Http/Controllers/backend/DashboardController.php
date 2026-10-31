<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\Models\User;
use App\Models\Event;
use App\Models\Post;
use Auth;
use DB;
use Response;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function __construct(Route $route){
        $this->middleware(function ($request, $next) use ($route){
            // Getting controller function
            $routeNameStr = $route->getActionName();
            $routeName = explode('@',$routeNameStr);

            $moduleName = request()->segment(2);
            return $next($request);
        });
    }


    /**
     * @summary for dashboard page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 10 Oct 2019
     */
    public function index()
    {
        $page_title     =  'Dashboard';
        // Return With Data Array
        $getUser = User::count();
        $getEvent = Event::where(function($query){
                $query->whereRaw('events.deleted_at IS NULL');
            })->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })->count();
        $getAnnoPost = Post::where(function($query){
            $query->where(['post_type'=>2]);
            $query->whereRaw('deleted_at IS NULL');
        })->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->count();
        // prd($getTotalQRCode);exit;
        $totalOrder = 0;
        $data = array(
            'title'   => $page_title,
            'getUser' => $getUser,
            'getEvent' => $getEvent,
            'getAnnoPost' => $getAnnoPost,
            'changeURL' => url('admin-panel/dashboard/change-date')
        );

        return view('backend.dashboard.index')->with($data);
    }
    /**
     * @summary to change date
     * @param
     * @return jsonResponse
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function changeDate(Request $request)
    {
        $response = [];
        if($request->filled('dateValue')){
            try{
                $dateVal = $request->dateValue;
                $type = $request->type;
                if($type == 'order'){
                    $condition = 'MONTH(created_at) = '.date('m');
                    if($dateVal == '1'){
                        $condition = 'DATE(created_at) = "'.date('Y-m-d').'"';
                    }else if($dateVal == '2'){
                        $condition = 'YEARWEEK(created_at)=YEARWEEK(NOW())';
                    }else if($dateVal == '3'){
                        $condition = 'created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()';
                    }else if($dateVal == '4'){
                        $condition = 'created_at BETWEEN NOW() - INTERVAL 90 DAY AND NOW()';
                    }else if($dateVal == '5'){
                        $condition = 'YEAR(created_at) = '.date('Y');
                    }
                    $getOrder = Order::where(function($query) use($condition){
                        $query->where('is_payment_success',1);
                        $query->whereRaw($condition);
                    })->count();
                    $getTotalQRCode = QrCodeBatch::select([DB::raw("SUM(no_of_order) as total_order")])
                    ->where(function($query) use($condition){
                        $query->whereRaw($condition);
                    })->get()->toArray();
                    $response = ['getOrder'=>$getOrder,'getTotalQRCode'=>(isset($getTotalQRCode[0]['total_order']) ? $getTotalQRCode[0]['total_order'] : 0)];
                }else if($type == 'shipping'){
                    $condition = 'shipping_status = '.$dateVal;
                    $getOrder = Order::where(function($query) use($condition){
                        $query->where('is_payment_success',1);
                        $query->whereRaw($condition);
                    })->count();
                    $response = ['getOrder'=>$getOrder];
                }else if($type == 'category'){
                    $getOrder = getAvaiableforOnlyCategory((int)$dateVal);
                    $response = ['getOrder'=>$getOrder];
                }else if($type == 'category-release'){
                    $getOrder = getReleaseforOnlyCategory((int)$dateVal);
                    $response = ['getOrder'=>$getOrder];
                }else if($type == 'template'){
                    $getOrder = getAvaiableforOnlyTemplate((int)$dateVal);
                    $response = ['getOrder'=>$getOrder];
                }else if($type == 'template-release'){
                    $getOrder = getReleaseforOnlyTemplate((int)$dateVal);
                    $response = ['getOrder'=>$getOrder];
                }
            } catch (\Exception $e){
                prd($e->message);
                $response = ['getOrder'=>0,'getTotalQRCode'=>0];
            }
        }else{
            $response = ['getOrder'=>0,'getTotalQRCode'=>0];
        }

        return Response::json($response, 200);
    }
}
