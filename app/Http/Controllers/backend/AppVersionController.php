<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AppVersion;
use App\Models\GlobalSetting;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminAppVersion;
use Illuminate\Support\Facades\Session;

use DB;
use File;
use Auth;

class AppVersionController extends Controller
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
            'title' => __('language.app_version_title'),
            'changeStatusURL' => url('admin-panel/app-version/change-status')
        );
        return view ('backend.app-version.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){

        $app_version = AppVersion::where(function($query) use ($request){})->orderBy('updated_at', 'ASC')->get();
        // prd($app_version);
        return DataTables::of($app_version)
        /*->addColumn('name', function ($app_version)
        {
            return ucwords($app_version->name);
        })*/
        ->addColumn('title', function ($app_version)
        {
            if(strlen($app_version->title)>70)
                $title = substr($app_version->title, 0,70).'...';
            else
                $title = $app_version->title;
            return $title;
        })
        ->addColumn('created_at', function ($app_version)
        {
            return Carbon::parse($app_version->created_at)->format('d/m/Y');
        })
        ->addColumn('updated_at', function ($app_version)
        {
            return Carbon::parse($app_version->updated_at)->format('d/m/Y');
        })
        ->addColumn('action', function ($app_version)
        {
            return '<a href="'.url('admin-panel/app-version/view/'.$app_version->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a><a href="'.url('admin-panel/app-version/edit/'.$app_version->id).'" class="action-control" title="Edit"><i class="fa fa-edit"></i></a>';
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
        $backButton = url('admin-panel/app-version/add-version');
        $saveButton = url('admin-panel/app-version/save');

        if( $id == null ){
            $newArray = Array();
            // Return With Data Array
            $pageData = AppVersion::orderBy('id', 'ASC')->get()->toArray();
            combineArrayKey($pageData, 'current_version', $newArray);
            combineArrayKey($pageData, 'min_version', $newArray);
            combineArrayKey($pageData, 'is_force_update', $newArray);
            // combineArrayKey($pageData, 'msg_force_update', $newArray);
            combineArrayKey($pageData, 'msg_normal_update', $newArray);
            combineArrayKey($pageData, 'is_prompt_update', $newArray);
            // prd($newArray);
            $data = array(
                            'title' => __('language.app_version_title'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Update',
                            'save' => url('admin-panel/app-version/save'),
                            'pageData' => $newArray
                        );
        } else {
            $pageData = AppVersion::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.app_version_title'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.app-version.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function store(AdminAppVersion $request){
        DB::beginTransaction();
        // prd($request['is_force_update']);
        try{            
            // ios save Data
            $iosVer = AppVersion::findOrFail(1);
            $iosVer->current_version = $request['current_version']['ios'];
            $iosVer->min_version = $request['min_version']['ios'];
            $iosVer->is_force_update = (isset($request['is_force_update']['ios']) ? 1 : 0);
            // $iosVer->msg_force_update = $request['msg_force_update']['ios'];
            $iosVer->msg_normal_update = $request['msg_normal_update']['ios'];
            $iosVer->is_prompt_update = (isset($request['is_prompt_update']['ios']) ? 1 : 0);
            $iosVer->save();
            // Android save Data
            $androidVer = AppVersion::findOrFail(2);
            $androidVer->current_version = $request['current_version']['android'];
            $androidVer->min_version = $request['min_version']['android'];
            $androidVer->is_force_update = (isset($request['is_force_update']['android']) ? 1 : 0);
            // $androidVer->msg_force_update = $request['msg_force_update']['android'];
            $androidVer->msg_normal_update = $request['msg_normal_update']['android'];
            $androidVer->is_prompt_update = (isset($request['is_prompt_update']['android']) ? 1 : 0);
            $androidVer->save();
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'App Version','attribute2'=>'updated']);
        return redirect('admin-panel/app-version/add-version')->with("message",$suceessMsg);
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
        $pageData = AppVersion::where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $data = array(
            'title' => __('language.app_version_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/app-version'),
        );
        return view ('backend.app-version.detail')->with($data);
    }
    /**
     * @summary
     * @param
     * @return global-setting
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function globalSetting(Request $request)
    {
        $pageData = GlobalSetting::all();
        $globalData = array();
        $globalDataIds = array();
        foreach($pageData as $key=>$data){
            $globalData[$data['key']] = $data['value'];
            $globalDataIds[$data['key'].'_ID'] = $data['id'];
        }
        // p($globalData);
        $data = array(
                        'title' => __('language.app_version_global_setting_title'),
                        'pageData' => $globalData,
                        'globalDataIds' => $globalDataIds,
                        'saveBtn' => 'Update',
                        'backBtnLink' => url('admin-panel/app-version/global-setting'),
                        'save' => url('admin-panel/app-version/global-setting-save'),                        
                    );



        return view('backend.app-version.global-setting')->with($data);
    }
    /**
     * @summary
     * @param
     * @return global-setting
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function globalSettingSave(Request $request)
    {
        DB::beginTransaction();
        try{
            $pageData = GlobalSetting::all();
            if($pageData){
                foreach($pageData as $key=>$data){
                    if($data['key']=='email'){
                        GlobalSetting::where('id',$data['id'])->update(['value'=>$request['email']]);
                    }
                }
            }
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        
        $suceessMsg = __('messages.success',['attribute1'=>'Global Settings','attribute2'=>'updated']);
        return redirect('admin-panel/app-version/global-setting')->with("message",$suceessMsg);
    }
     /**
     * @summary
     * @param
     * @return send-notfication
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function sendNotification(Request $request)
    {
        $pageData = GlobalSetting::all();
        $globalData = array();
        $globalDataIds = array();
        foreach($pageData as $key=>$data){
            $globalData[$data['key']] = $data['value'];
            $globalDataIds[$data['key'].'_ID'] = $data['id'];
        }
        // p($globalData);
        $data = array(
                        'title' => __('language.app_version_send_notification_title'),
                        'pageData' => $globalData,
                        'globalDataIds' => $globalDataIds,
                        'saveBtn' => 'Update',
                        'backBtnLink' => url('admin-panel/app-version/global-setting'),
                        'save' => url('admin-panel/app-version/global-setting-save'),                        
                    );



        return view('backend.app-version.global-setting')->with($data);
    }

}
