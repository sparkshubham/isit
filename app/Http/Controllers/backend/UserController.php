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

class UserController extends Controller
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
     * @created_at 30 Oct 2019
     */
    public function index()
    {
        $data = array(
            'title' => __('language.user_title'),
            'changeStatusURL' => url('admin-panel/users/change-status'),
            'changeStatusStarURL' => url('admin-panel/users/change-star-status'),
            'changeVerifyURL' => url('admin-panel/users/change-verify-status'),
            'getAutologinUrl' => url('admin-panel/users/autologin-url')
        );
        return view ('backend.user.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return datatable
     * @author blitzinfosystem
     * @created_at 30 Oct 2019
     */
    public function data(Request $request){
        $StarStatus = [1=>'Yes',0=>'No'];
        $verifiedStatus = [1=>'Yes',0=>'No'];
        $users = User::select('users.id','users.first_name','users.last_name','users.email','users.status','users.is_verified','users.created_at','users.last_login','users.country_code','users.phone_number','users.username','users.is_star')
        ->where(function($query) use ($request){            
            $query->whereRaw('users.deleted_at IS NULL');
        });
        // ->orderBy('id', 'DESC');
        // echo $order;exit;
        // prd($users);
        // 
        return DataTables::of($users)
        ->addColumn('status', function ($users)
        {
            $disable = '';
            $state = $users->status==1?'checked':'';
            return '<div class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$users->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$users->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$users->id.'" class="label-success status-lable"></label></div>';
        })
        ->addColumn('is_star', function ($users) use($StarStatus)
        {
            $html = '<select class="star-check" data-id="'.$users->id.'" id="'.$users->id.'">';
            $selected = '';
            foreach($StarStatus as $key => $value){
                $selected = '';
                if($key == $users->is_star){
                    $selected = 'selected = selected';
                }
                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
            $html .= '</select>';
            return $html;
            // $disable = '';
            // $state = $users->is_start==1?'checked':'';
            // return '<div id="star" class="material-switch pull-center"><input class="star-check" id="starSwitchOptionSuccess_'.$users->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$users->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$users->id.'" class="label-success is_start-lable"></label></div>';
        })
        ->addColumn('is_verified', function ($users) use($verifiedStatus)
        {
            $disable = '';
            if($users->is_verified == 1)
                $disable = 'disabled';
            $html = '<select class="verify-check" '.$disable.' data-id="'.$users->id.'" id="'.$users->id.'">';
            $selected = '';
            foreach($verifiedStatus as $key => $value){
                $selected = '';
                if($key == $users->is_verified){
                    $selected = 'selected = selected '.$disable;
                }
                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
            $html .= '</select>';
            return $html;
            // $disable = '';
            // $state = $users->is_start==1?'checked':'';
            // return '<div id="star" class="material-switch pull-center"><input class="star-check" id="starSwitchOptionSuccess_'.$users->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$users->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$users->id.'" class="label-success is_start-lable"></label></div>';
        })
        ->addColumn('name', function ($users)
        {
            $name = str_replace('“', '', $users->first_name.$users->last_name);
            $name = str_replace('”', '', $name);
            $name = addslashes(substr($name,0,30));
            return strlen($users->first_name.$users->last_name) > 30 ? $name."..." : $users->first_name.' '.$users->last_name;
        })
        ->addColumn('username', function ($users)
        {
            if($users->username == NULL){
                return '-';
            }else{
                $name = str_replace('“', '', $users->username);
                $name = str_replace('”', '', $name);
                $name = addslashes(substr($name,0,30));
                return strlen($users->username) > 30 ? $name."..." : $users->username;
            }
        })
        ->addColumn('action', function ($users)
        {
            return '<a href="'.url('admin-panel/users/view/'.$users->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a>';

        })
        ->addColumn('last_login', function ($users)
        {
            if($users->last_login != NULL){
                $time = strtotime($users->last_login);
                return Carbon::parse($time)->format('d/m/Y');
            }else{
                return '-';
            }
            
        })
        ->addColumn('phone_number', function ($users)
        {
            if($users->country_code !=''){
                return $users->country_code.'-'.$users->phone_number;
            }else{
                return $users->phone_number;
            }
            
        })
        ->filterColumn('name', function($query, $keyword) {
            $sql = "users.first_name like ?";
            $keyword = str_replace("'",'',$keyword);
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('email', function ($query, $keyword) {
            $keyword = str_replace("'",'',$keyword);
            return $query->where("users.email", "LIKE", "%{$keyword}%");
        })
        ->filterColumn('username', function ($query, $keyword) {
            $keyword = str_replace("'",'',$keyword);
            return $query->where("users.username", "LIKE", "%{$keyword}%");
        })
        ->filterColumn('phone_number', function ($query, $keyword) {
            $keyword = str_replace("'",'',$keyword);
            return $query->whereRaw("users.phone_number LIKE '%".$keyword."%' OR users.country_code LIKE '%".$keyword."%' OR users.last_name LIKE '%".$keyword."%'");
        })
        ->orderColumn('name', function($query, $order){
            return $query->orderBy('users.first_name', $order);
        })
        ->orderColumn('username', function($query, $order){
            return $query->orderBy('users.username', $order);
        })
        ->orderColumn('email', function($query, $order){
			return $query->orderBy('users.email', $order);
        })
        ->orderColumn('status', function($query, $order){
			return $query->orderBy('status', $order);
        })
        ->orderColumn('is_star', function($query, $order){
			return $query->orderBy('is_star', $order);
        })
        ->orderColumn('is_verified', function($query, $order){
			return $query->orderBy('is_verified', $order);
        })
        ->orderColumn('last_login', function($query, $order){
            // echo $order;exit;
            return $query->orderByRaw('DATE(last_login)'.$order);
        })
        // ->orderColumn('subdcription_plan', function($query, $order){
		// 	$query->whereHas('getPlan.getPlan', function ($query) use($order) {
        //         $ids = [3,4,'',2,1];
        //         return $query->orderByRaw('FIELD (id, ' . implode(', ', $ids) . ') '.$order);
        //     });
        // })
        ->rawColumns(['status' => 'status', 'action' => 'action','is_star'=>'is_star','is_verified'=>'is_verified'])->addIndexColumn()->make(true);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 30 Oct 2019
     */
    public function show($id)
    {
        $pageData = User::find($id);
        $imagePath = '';
        $userFolderName = config('constants.user_image.upload_path');
        $pageData['image_path'] = getS3Url($userFolderName).$pageData['profile_picture'];
        $data = array(
            'title' => __('language.user_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/users'),
        );

        return view ('backend.user.detail')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 30 Oct 2019
     */
    public function create(Request $request, $id=null)
    {
        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.user_add'),
                            'backBtnLink' => url('admin-panel/users'),
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/users/save'),
                        );
        } else {

            $pageData = User::findOrFail($id);
            $imagePath = '';
            $displayImagePath = config('constants.user_image.display_path');
            $pageData['image_path'] = url($displayImagePath.$pageData['profile_picture']);

            // Return With Data Array
            $data = array(
                            'title' => __('language.user_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => url('admin-panel/users'),
                            'save' => url('admin-panel/users/save'),
                        );

        }

        return view('backend.user.add')->with($data);
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
                User::where('id', '=', $request['id'])->update(['status' => $request['status']]);
                OauthAccessTokens::where(['user_id'=>$request['id']])->delete();
                $response = ['status'=>true];
            } catch (\Exception $e){
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }

    /**
     * @summary delete record from database
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function destroy($id = null)
    {
        $user = User::findOrFail($id);
        if ($user){
            DB::beginTransaction();
            try
            {
                $user->delete();
                $success_msg = __('messages.delete_success',['attribute1'=>'Record']);
            }
            catch (\Exception $e)
            {
                DB::rollback();
                $error_message = __('messages.delete_failure');
                echo $e->getMessage();
                exit;
                return redirect()->back()->withErrors($error_message);
            }
            DB::commit();
            return redirect('/admin-panel/users')->with('message', $success_msg);

        } else {
            $error_message = __('messages.not_found',['attribute1'=>'Record']);
            return redirect()->back()->withErrors($error_message);
        }

    }
    /**
     * @summary to change star mark
     * @param
     * @return jsonResponse
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function changeStarStatus(Request $request)
    {
        $response = [];
        if($request->filled('id') || $request->filled('status')){
            try{
                User::where('id', '=', $request['id'])->update(['is_star' =>$request['status']]);                
                $response = ['status'=>true];
            } catch (\Exception $e){
                // echo $e->getMessage();exit;
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }
    /**
     * @summary to verify the user which is not verified
     * @param
     * @return jsonResponse
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function changeVerifyStatus(Request $request)
    {
        $response = [];
        if($request->filled('id') || $request->filled('status')){
            try{
                $user = User::select(['id','verifiy_token','first_name','last_name'])->whereRaw('id='.$request['id'])->first();
                // prd($user);
                $user->is_verified = 1;
                $user->verifiy_token = '';
                $fullName = $user->first_name.' '.$user->last_name;
                $profileImageUrl = '';
                // echo $fullName;exit;
                $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
                $user->save();
                // User::where('id', '=', $request['id'])->update(['is_verified' =>$request['status']]);                
                $response = ['status'=>true];
            } catch (\Exception $e){
                echo $e->getMessage();exit;
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }
}
