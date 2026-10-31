<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CmsPages;
use Auth;
use DB;


use App\Models\UserHelp;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth', ['except' => ['verfiyPhone','verfiyMail','termsOfUse','privacyPolicy','aboutUs','support','supportUser']]);
    }
    /**
     * @summary to get message email verification
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2019
     */
    public function verfiyMail($verifyKey)
    {
        $user = User::select(['id','verifiy_token','first_name','last_name'])->whereRaw('verifiy_token="'.$verifyKey.'"')->first();
        if(empty($user)){
            $code = 605;
        }else{
            $code = 606;      
            $user->is_verified = 1;
            $user->verifiy_token = '';
            $fullName = $user->first_name.' '.$user->last_name;
            $profileImageUrl = '';
            // echo $fullName;exit;
            $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
            $user->save();
        }
        $message = __('api-messages.'.$code);
        return view('home.verify-mail',[
            'message' => $message
        ]);
    }
    public function verfiyPhone($verifyKey)
    {
       
        $user = User::select(['id','phone_verify_token','first_name','last_name','is_phone_verified'])->whereRaw('phone_verify_token="'.$verifyKey.'"')->first();
        if(empty($user)){
            $message = 'Verification Link Expired.';
        }else{
            $message ='Phone verified Successfully!';     
            $user->is_phone_verified = 1;
            $user->phone_verify_token = '';
            $fullName = $user->first_name.' '.$user->last_name;
            $profileImageUrl = '';
            // echo $fullName;exit;
            $user->save();
        }
        
        return view('home.verify-mail',[
            'message' => $message
        ]);
    }
    /**
     * @summary to get terms of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function termsOfUse(){
        $content = "Terms and Condition Page Comming Soon!";
        $cms = CmsPages::select(['id','description'])->whereRaw('title="TERMS AND CONDITIONS"')->first();
        if(!empty($cms)){
            $content = $cms->description;
        }
        return view('frontend.layouts.terms_of_uses',[
            'content' => $content,
            'title' => __('language.terms_of_uses'),
        ]);
    }
    /**
     * @summary to get privacy of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function privacyPolicy(){
        $content = "Privacy Policy Page Comming Soon!";
        $cms = CmsPages::select(['id','description'])->whereRaw('title="PRIVACY POLICY"')->first();
        if(!empty($cms)){
            $content = $cms->description;
        }
        return view('frontend.layouts.privacy_policy',[
            'content' => $content,
            'title' => __('language.privacy_policy'),
        ]);
    }
    /**
     * @summary to get about us of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function aboutUs(){
        // Auth::logout();
        $content = "About Us Page Comming Soon!";
        $cms = CmsPages::select(['id','description'])->whereRaw('title="ABOUT US"')->first();
        if(!empty($cms)){
            $content = $cms->description;
        }
        return view('frontend.layouts.about_us',[
            'content' => $content,
            'title' => __('language.about_us'),
        ]);
    }


    
    
    public function support(Request $request){
        $response['title'] = 'Support';
        return view('frontend.user.support',$response);
    }


    public function supportUser(Request $request){
        $response = [];
        // dd($request->all());
        DB::beginTransaction();
        try{
            if(empty($request->category)){
                
                return redirect()->back()->with("error","Category id not present");
            }
            // $user = Auth::user();
            $UserHelp = new UserHelp();
            $UserHelp->user_id =1;
            $UserHelp->description = $request['description'];
            $UserHelp->admin_comment ="SUPPORT";
            $UserHelp->status = 0;
            $UserHelp->category = (empty($request->category) ? '' : $request->category);
            $UserHelp->save();

            if($UserHelp){
                $response_type='success';
                $response_message='Successfully submit your request';
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }
            
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            $response_type='error';
            return redirect()->route('support')->with($response_type,$this->optionalData['message']);
        }
        DB::commit();
        return redirect()->route('support')->with($response_type,$response_message);
    }



}
