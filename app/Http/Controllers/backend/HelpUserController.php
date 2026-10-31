<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\UserHelp;
use App\Models\Post;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;

use DB;
use File;
use Auth;

class HelpUserController extends Controller
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
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function index()
    {
        $data = array(
            'title' => __('language.help_user_title'),
            'changeStatusURL' => url('admin-panel/help-user/change-status')
        );
        return view ('backend.help-user.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){
        $helpStatus = config('constants.helpStatus');
        $help_user = UserHelp::with([
                        'getUser'=>function($query){
                            $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                        }])->whereHas('getUser', function ($query){
                            $query->whereRaw('users.deleted_at IS NULL');
                        })->where(function($query) use ($request){})->orderBy('id', 'DESC')->get();
        // prd($help_user->toArray());
        return DataTables::of($help_user)
        /*->addColumn('name', function ($help_user)
        {
            return ucwords($help_user->name);
        })*/
        ->addColumn('category', function ($help_user)
        {
            if(strlen($help_user->category)>70)
                $category = substr($help_user->title, 0,70).'...';
            else
                $category = $help_user->category;
            return $category;
        })
        ->addColumn('description', function ($help_user)
        {
            if(strlen($help_user->description)>70)
                $description = substr($help_user->description, 0,70).'...';
            else
                $description = $help_user->description;
            return $description;
        })
        ->addColumn('username', function ($help_user)
        {   
            if(!isset($help_user->getUser->first_name)){
                return '';
            }
            $name = str_replace('“', '', $help_user->getUser->first_name.$help_user->getUser->last_name);
            $name = str_replace('”', '', $name);
            $name = addslashes(substr($name,0,30));
            return strlen($help_user->getUser->first_name.$help_user->getUser->last_name) > 30 ? $name."..." : $help_user->getUser->first_name.' '.$help_user->getUser->last_name;
        })
        ->addColumn('help_status', function ($help_user) use($helpStatus)
        {
            $html = '<select class="testimonial-check" data-id="'.$help_user->status.'" id="'.$help_user->id.'">';
            $selected = '';
            foreach($helpStatus as $key => $value){
                $selected = '';
                if($key == $help_user->status){
                    $selected = 'selected = selected';
                }
                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
            $html .= '</select>';
            return $html;
            // return $ShippingStatus[$order->shipping_status];
        })      
        ->addColumn('status', function ($help_user)
        {
            $disable = '';
            $state = $help_user->status==1?'checked':'';
            return '<div status = '.$help_user->status.' class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$help_user->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$help_user->id.'" post-id="'.$help_user->post_id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$help_user->id.'" class="label-success status-lable"></label></div>';
        })
        ->addColumn('created_at', function ($help_user)
        {
            return Carbon::parse($help_user->created_at)->format('d/m/Y');
        })
        /* ->addColumn('actions', function ($help_user)
        {
            $disable = '';
            $state = $help_user->status==1?'checked':'';
            return '<div class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$help_user->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$help_user->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$help_user->id.'" class="label-success status-lable"></label></div>';
        }) */
        ->rawColumns(['status' => 'status','help_status'=>'help_status'])->addIndexColumn()->make(true);
    }


    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function create(Request $request, $id=null)
    {
        $backButton = url('admin-panel/help-user');
        $saveButton = url('admin-panel/help-user/save');

        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.help_user_add'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/help-user/save'),
                        );
        } else {
            $pageData = UserHelp::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.help_user_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.help-user.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function store(AdminCmsUpdate $request){
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $help_user = UserHelp::findOrFail($request['id']);
            }else{
                $help_user = new UserHelp;
            }            
            $help_user->description = $request['description'];
            $help_user->save();
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'CMS Pages','attribute2'=>'updated']);
        return redirect('admin-panel/help-user')->with("message",$suceessMsg);
    }    

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function show($id)
    {
        $pageData = UserHelp::where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $data = array(
            'title' => __('language.help_user_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/help-user'),
        );
        return view ('backend.help-user.detail')->with($data);
    }
    /**
     * @summary to change status
     * @param
     * @return jsonResponse
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function changeStatus(Request $request)
    {
        $response = [];
        if($request->filled('id') && $request->filled('status')){
            try{
                UserHelp::where('id', '=', $request['id'])->update(['status' => $request['status']]);
                $response = ['status'=>true];
            } catch (\Exception $e){
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }
}
