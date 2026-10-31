<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\SubscriptionPlan;
use App\Model\SubscriptionFeature;
use Illuminate\Routing\Route;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminChangeprofile;
use App\Http\Requests\backend\AdminChangepassword;
use Auth;
use DB;
use Hash;
use File;


class HomeController extends Controller
{
    public function __construct(Route $route)
    {
        $this->middleware(function ($request, $next) use ($route){
            // Getting controller function
            $routeNameStr = $route->getActionName();
            $routeName = explode('@',$routeNameStr);

            $moduleName = request()->segment(2);
            return $next($request);
        });
    }

    public function showChangePasswordForm(){
        $data = array(
            'title' => __('language.change_password_title'),
            'backBtnLink' => url('admin-panel/change-profile'),
            'saveBtn' => 'Save',
            'save' => url('admin-panel/changePassword'),
        );
        return view('backend.auth.changepassword')->with($data);
    }

    public function changePassword(AdminChangepassword $request){

        $currentPassword = Auth::guard('admin_user')->user()->password;
        if (!(Hash::check($request->get('current-password'), $currentPassword))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your old password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|min:8|max:32',
            'new-password-confirm' => 'required|string|required_with:new-password|same:new-password',
        ]);

        //Change Password
        $user = Auth::guard('admin_user')->user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();

        return redirect('admin-panel/dashboard')->with("success","Password changed successfully !");
        //return redirect()->back()->with("success","Password changed successfully !");

    }

    public function showChangeProfileForm(Request $request, $id=null){

        $pageData = Auth::guard('admin_user')->user();
        $imagePath = '';
        $displayImagePath = config('constants.admin_image.display_path');
        $pageData['image_path'] = url($displayImagePath.$pageData['profile_picture']);
        $pageData['image_path_exist']='';
        if($pageData['profile_picture'] != NULL && $pageData['profile_picture'] != '')
            $pageData['image_path_exist'] = $displayImagePath.$pageData['profile_picture'];

        $data = array(
            'title' => __('language.change_profile_title'),
            'backBtnLink' => url('admin-panel/dashboard'),
            'saveBtn' => 'Update',
            'save' => url('admin-panel/changeProfile'),
            'pageData' => $pageData,
        );

        return view('backend.auth.changeprofile')->with($data);
    }
    public function changeProfile(AdminChangeprofile $request){

        DB::beginTransaction();
        try{
            $userId = Auth::guard('admin_user')->user()->admin_id;
            if(isset($userId)){
                $user = Auth::guard('admin_user')->user();
                $user->first_name = $request['first_name'];
                // $user->last_name = $request['last_name'];
                $user->phone_number = $request['phone_number'];
                if(isset($request['email'])){
                    $user->email = $request['email'];
                }
                if($request->hasFile('profile_picture')){
                    /*crete admin image folder if not exists*/
                    $path = public_path().'/'.config('constants.admin_image.upload_path');
                    if(!file_exists($path)){
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }
                    $file = $request->file('profile_picture');
                    //p($path.$user->profile_picture);
                    if(isset($user->profile_picture) && $user->profile_picture !=NULL && file_exists($path.$user->profile_picture)){
                        unlink($path.$user->profile_picture);
                    }
                    $destinationPath = config('constants.admin_image.upload_path');
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    //Move Uploaded File
                    $file->move($destinationPath,$fileName);
                    $user->profile_picture = $fileName;
                }else if($request['remove_img']){
                    $user->profile_picture = null;
                }
                $user->save();
            }
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        return redirect('admin-panel/dashboard')->with("success","Profile changed successfully !");
        //return redirect()->back()->with("success","Profile changed successfully !");
    }
}
