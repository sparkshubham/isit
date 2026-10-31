<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminNotification;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminNotificationAdd;
use App\Jobs\SendPushNotificationJob;

use DB;
use File;
use Auth;

class AdminNotificationController extends Controller
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
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function index()
    {
        $data = array(
            'title' => __('language.admin_notification_title'),
            'changeStatusURL' => url('admin-panel/admin-notification/change-status')
        );
        return view ('backend.admin-notification.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){

        $admin_notification = AdminNotification::where(function($query) use ($request){})->orderBy('updated_at', 'ASC')->get();
        // prd($cms);
        return DataTables::of($admin_notification)
        /*->addColumn('name', function ($cms)
        {
            return ucwords($cms->name);
        })*/
        ->addColumn('message', function ($admin_notification)
        {
            if(strlen($admin_notification->message)>70)
                $message = substr($admin_notification->message, 0,70).'...';
            else
                $message = $admin_notification->message;
            return $message;
        })
        ->addColumn('created_at', function ($admin_notification)
        {
            return Carbon::parse($admin_notification->created_at)->format('d/m/Y');
        })
        ->addColumn('action', function ($admin_notification)
        {
            return '<a href="'.url('admin-panel/admin-notification/view/'.$admin_notification->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a>';
        })
        ->rawColumns(['action' => 'action'])->addIndexColumn()->make(true);
    }


    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function create(Request $request, $id=null)
    {
        $backButton = url('admin-panel/admin-notification');
        $saveButton = url('admin-panel/admin-notification/save');

        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.admin_notifcation_add'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Send Notification',
                            'save' => url('admin-panel/admin-notification/save'),
                        );
        } else {
            $pageData = CmsPages::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.cms_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.admin-notification.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function store(AdminNotificationAdd $request){
        DB::beginTransaction();
        try{
            // sendNotificationWithData(1,2,21,['other_data'=>"This is new update"]);
           //  var_dump($request->filled('id'));exit;
            if($request->filled('id')){
                $admin_notification = AdminNotification::findOrFail($request['id']);
            }else{
                $admin_notification = new AdminNotification();
            }            
            // echo $request['message'];exit;
            $admin_notification->message = $request['message'];
            $admin_notification->save();
            SendPushNotificationJob::dispatch($request['message']);
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            prd($e);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'Admin Notifications','attribute2'=>'updated']);
        return redirect('admin-panel/admin-notification')->with("message",$suceessMsg);
    }    

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function show($id)
    {
        $pageData = AdminNotification::where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $data = array(
            'title' => __('language.admin_notification_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/admin-notification'),
        );
        return view ('backend.admin-notification.detail')->with($data);
    }
}
