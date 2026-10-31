<?php
namespace App\Http\Controllers\frontend\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests\FrontRegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\FrontVerifyRequest;
use App\Http\Requests\FrontLoginRequest;
use Auth;
use Hash;
use Mail;
use Cookie;
use ValidationException;
use App\Models\User;
use Redirect;
use Session;
use Toastr;


use Carbon\Carbon;

class LoginController extends Controller{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;
    use AuthenticatesUsers{
        logout as performLogout;
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/post';
    protected function guard(){        
        return auth()->guard('web');
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){        
        $this->middleware('guest')->except('logout');
    }

    /**
     * @summary to show login page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 10 Oct 2019
     */
    public function showLoginForm(){   
        $email = Cookie::get('username');
        $password = Cookie::get('password');        
        if(!is_null($email) && !is_null($password)){
            $is_checked = 'checked';
        }else{
            $is_checked = '';
        }
        $country_arr =  config('country.country_arr');
        foreach ($country_arr as $key => $value){
            $value=$key;
            $country_arr[$value]='+'.$key;
        }

        $data = [
                'email' => $email,
                'password' => $password,
                'is_checked' => $is_checked,
                'title' => 'Login',
                'country_arr'=> $country_arr

            ];
        return view('frontend.auth.login')->with($data);
    }
    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function validateLogin(Request $request){
        $this->validate(
            $request,
            [
                // 'email' => 'required|string|email:rfc,dns',
                'password' => 'required|string|min:8|max:30',
            ],
            [
                // 'email.required' => 'Email is required',
                // 'password.required' => 'Password is required',
                'password.min' => 'These credentials do not match our records.',
                'password.max' => 'These credentials do not match our records.',
            ]
        );
    }
     /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */


    public function attemptLogin(Request $request){
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        
        if($user->is_verified == 1){
            if(!empty($user)) {
                if($user->status){
                    if (Auth::attempt($credentials)){
                        return redirect()->route('post-board');
                    }else {
                        return redirect()->route('front-login')->with(['error_msg' => 'Login credentials are invalid']);
                    }
                }else {
                    return redirect()->route('front-login')->with(['error_msg' => 'You are not active']);
                }
            }else{
                return redirect()->route('front-login')
                ->with(['error_msg' => 'You are not logged in because do not have authorised permission']);
            }
        }else{
            $user->save();
            return redirect('verify/email/'.$request->email);            
        }
    }
    /**
     * @summary overriding default logout function
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 10 Oct 2019
     */
    public function frontRegister(){
        try{
            $country_arr =  config('country.country_arr');
            foreach ($country_arr as $key => $value){
                $value=$key;
                $country_arr[$value]='+'.$key;
            }

            $data = [
                'title' => 'Sign Up',
                'country_arr'=>$country_arr

            ];
            return view('frontend.auth.register', $data);
        }catch (Exeption $th) {
            return redirect()->back()->with('error', 'something wrong');
        }
    }

    public function frontPostRegister(FrontRegisterRequest $request){
        try{
            $user = User::where('email', $request->email)->first();
            $otp = rand(1000, 9999);
            if(isset($user)){
                if($user->is_verified == 0){
                    $user->save();
                    return redirect()->back()->with('success', 'Verification sent successfully. Please be verfied your email first.');
                }else if($user->is_phone_verified==0){
                    $user->save();
                    return redirect()->back()->with('success', 'Verification sent successfully to phone. Please be verfied your phone first.');
               
                }else{
                    return redirect()->route('front-register')->with(['error_msg' => 'This Email address is already exist!']);
                }
            }else{
                $user = new User();                
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->name = $request->first_name.' '.$request->last_name;
                $user->phone_number = $request->phone_number;
                $user->country_code = $request->country_code;
                $user->verifiy_token = $otp;
                $user->phone_verify_token = $otp;

                
                $phone_number='+'.$request->country_code.$request->phone_number;
                $mailData['email'] = $request->email;
                $mailData['name'] = $request->first_name.' '.$request->last_name;
                $mailData['otp'] = $otp;
                $to_email = $request->email;
                try{
                    if($user->save()){

                        sendEmailVerificationMail($request->email, $otp);
                        $user->phone_verify_token = generate_string(10);
                        $user->save();
                        $link = url('verfiy-phone', $parameters = array($user->phone_verify_token), $secure = null);

                        $message_phone = 'Hi '.$user->first_name.' '.$user->last_name.' Welcome to InSpiritInTruth!  Please click on this '.$link.' to verify your email address.Thanks for joining us. Yours Truly Team ISIT';	
                        $phone_number='+'.$user->country_code.$user->phone_number;
                        $data_message=['message'=>$message_phone,'type'=>'1','phone_number'=>$phone_number];
                        sendOtptophone($data_message);
                        Toastr::success('You have successfully signup please check your email/phone and verifed your account.', 'Success');
                        return redirect()->route('front-login');
                    }else {
                        return redirect()->route('front-register')->with('error', 'Registration Failed.');
                    }
                } catch (Exception $ex) {
                    return redirect()->route('front-register')->with('error','Registration Failed.');
                }
            }
        }catch (Exception $e){
            return redirect()->back()->with('error', 'something wrong');
        }
    }

    public function verification($email){
        try{
            $user = User::where('email', $email)
                        ->first();
            if(isset($user)){
                $data = [
                    'title' => 'Email Verification',
                    'user'=>$user
                ];
                return view('frontend.auth.verify', $data);
            }else{
                return redirect()->route('front-register')->with(['error_msg' => 'This Email address is already exist!']);
            }
        }catch(Exeption $th){
            return redirect()->back()->with('error', 'something wrong');
        }
    }
    public function verificationPhone($identifier){
        try{
            $user = User::where('phone_number', $identifier)
                        ->first();
            if(isset($user)){
                $data = [
                    'title' => 'Phone Verification',
                    'user'=>$user
                ];
                return view('frontend.auth.verify-phone', $data);
            }else{
                return redirect()->route('front-register')->with(['error_msg' => 'This Email address is already exist!']);
            }
        }catch(Exeption $th){
            return redirect()->back()->with('error', 'something wrong');
        }
    }


    public function verfiyMail($verifyKey)
    {
        $user = User::select(['id','verifiy_token','first_name','last_name'])->whereRaw('verifiy_token="'.$verifyKey.'"')->first();
        if(empty($user)){
            return redirect()->route('front-register')->with('error', 'This Email address is already exist!');
        }else{
            $code = 606;      
            $user->is_verified = 1;
            $user->verifiy_token = '';
            $fullName = $user->first_name.' '.$user->last_name;
            $profileImageUrl = '';
            // echo $fullName;exit;
            $user->save();
        }
        return redirect('login')->with('success', 'Verification successfully');
    }
    
    public function resendVerification($email){
        try{
            $user = User::where('email', $email)
            ->first();
            $otp = rand(1000, 9999);
            if(isset($user)){
                emailVerifyMail($email, $otp);
               
                return redirect()->back()->with('success', 'Verification sent successfully');
            }else{
                return redirect()->route('front-register')->with(['error_msg' => 'This Email address is already exist!']);
            }
        }catch(Exeption $th){
            return redirect()->back()->with('error', 'something wrong');
        }
    }
    public function resendVerificationPhone($phone){
        try{
            $user = User::where('phone_number', $phone)
            ->first();
            $otp = rand(1000, 9999);
            if(isset($user)){
                $data_message=['message'=>'','type'=>'2','phone_number'=>$user->phone_number,'otp'=>$otp];
                sendOtptophone($data_message);
                $user->phone_verify_token = $otp;
                $user->save();
                return redirect()->back()->with('success', 'Verification sent successfully');
            }else{
                return redirect()->route('front-register')->with(['error_msg' => 'This Email address is already exist!']);
            }
        }catch(Exeption $th){
            return redirect()->back()->with('error', 'something wrong');
        }
    }
    public function postVerify(Request $request){
        try{
            $user = User::where('email', $request->email)->first();
            $otp = $request->otp_1.$request->otp_2.$request->otp_3.$request->otp_4;
            if(isset($user)){
                if($user->verifiy_token == trim($otp)){
                    $credentials = $request->only('email', 'password');
                    $checkActiveRecord = User::where('email', $request->email)->first();
                    if (!empty($checkActiveRecord)){
                        if($checkActiveRecord->status){ 
                            $user->is_verified = 1;
                            $user->save();
                            return redirect('set-password/'.$request->email)->with('success', 'Your verification is completed, Please set new password.');   
                           }else{
                            return redirect()->route('front-login')->with('error','You are not active');
                        }
                    } else {
                        return redirect()->route('front-login')
                        ->with(['error' => 'You are not logged in because do not have authorised permission']);
                    }
                }else{              
                    return redirect('verify/email/'.$request->email)->with('error', 'Verification code is not matched');                      
                }
            }else{
                return redirect()->route('front-register')->with('error', 'Your are not registred yet!');
            }
        }catch (Exception $e){
            return redirect()->back()->with('error', 'something wrong');
        }
    }
    public function postVerifyphone(Request $request){
        try{
            
            $user = User::where('phone_number', $request->phone_number)->first();
            $otp = $request->otp_1.$request->otp_2.$request->otp_3.$request->otp_4;
            if(isset($user)){
                if($user->verifiy_token == trim($otp)){
                    $credentials = $request->only('email', 'password');
                    $checkActiveRecord = User::where('phone_number', $request->phone_number)->first();
                    if (!empty($checkActiveRecord)){
                        if($checkActiveRecord->status){ 
                            $user->is_phone_verified = 1;
                            $user->phone_verify_token='';
                            $user->save();
                            return redirect('set-password/'.$user->email)->with('success', 'Your verification is completed, Please set new password.');   
                           }else{
                            return redirect()->route('front-login')->with('error','You are not active');
                        }
                    } else {
                        return redirect()->route('front-login')
                        ->with(['error' => 'You are not logged in because do not have authorised permission']);
                    }
                }else{              
                    return redirect('verify/phone/'.$request->phone_number)->with('error', 'Verification code is not matched');                      
                }
            }else{
                return redirect()->route('front-register')->with('error', 'Your are not registred yet!');
            }
        }catch (Exception $e){
            return redirect()->back()->with('error', 'something wrong');
        }
    }

    public function passwordForgot(){
        try{
            $country_arr =  config('country.country_arr');
            foreach ($country_arr as $key => $value){
                $value=$key;
                $country_arr[$value]='+'.$key;
            }
            $data = [
                'title' => 'Forget Password',
                'country_arr'=>$country_arr
            ];
        return view('frontend.auth.passwords.email')->with($data);
            // return view('frontend.auth.passwords.email');            
        }catch(Exeption $th){
            return redirect()->back()->with('error', 'something wrong');
        }
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();
        Session::forget('twillio');
        return redirect('/');
        // ->with('success', 'You are successfully logout.');
    }
    
    public function passwordLinkExpired(){
        return view('frontend.auth.link-expired');
    }
   
    protected function login(Request $request){
        // $user = User::where('email', $request->email)->first();
        // $loginField = $request->email; // This is the field where the user can enter either email or phone number
        $password = $request->password;
        $loginType = $request->input('login_type'); // Assuming you have a hidden input field named 'login_type' in your form
        if ($loginType === 'email') {
            $this->validate($request, [
                'email' => 'required|email',
            ]);
            $user = User::where('email', $request->email)->first();
            if(isset($request->email) && $request->email !='' && $user->is_verified != 1){
                return redirect()->back()->with('error', 'Email is not verified yet. Please resend code again.');
            }

           } elseif ($loginType === 'phone') {
            $this->validate($request, [
                'country_code' => 'required',
                'phone_number' => 'required|numeric',
            ]);
            $country_code=$request->country_code;
            $phone_number=$request->phone_number;
            $user = User::where('country_code', $country_code)
                      ->where('phone_number', $phone_number)
                      ->first();
                    if(isset($request->phone_number) && isset($request->country_code) && $request->country_code!='' && $request->phone_number!='' && $user->is_phone_verified != 1){
                        return redirect()->back()->with('error', 'Phone Number is not verified yet. Please resend code again.');
                    }
        }
        
        $otp = rand(1000, 9999);
        if(isset($user)){

            $credentials = ['email' => $user->email, 'password' => $password];
            if($user->status != 1)
            {
                return redirect()->back()->with('error', 'User deactived by admin. please contact to admin.');
            }
            if($user->is_verified == 1){
                $this->validateLogin($request);    
                // If the class is using the ThrottlesLogins trait, we can automatically throttle
                // the login attempts for this application. We'll key this by the username and
                // the IP address of the client making these requests into this application.
                if(method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)){
                    $this->fireLockoutEvent($request);    
                    return $this->sendLockoutResponse($request);
                }
                if(Auth::guard('web')->attempt($this->credentials($request), $request->filled('remember'))){
                    //Authenticated, redirect to the intended route
                    //if available else admin dashboard.
                    // return redirect()
                    // ->intended(route('post-index'))
                    // ->with('status','You are Logged in!');
                    $tokenValue=$this->getTwillioAccessTokenweb();
                    $user->last_login = Carbon::now();
                    $user->save();
                    Session::put('twillio', $tokenValue);
                    return redirect()->route('post-board');
                    // ->with('success', 'You are logged In successfully.');                    
                }
                // Customization: Validate if client status is active (1)
                $email = $request->get($this->username());
                // Customization: It's assumed that email field should be an unique field 
                $client = User::where($this->username(), $email)->first();
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);
                // Customization: If client status is inactive (0) return failed_status error.
                // if (!empty($client) && $client->status != 1) {
                //     return $this->sendFailedLoginResponse($request, 'Your account has been disabled, please contact admin.');
                // }
                return $this->sendFailedLoginResponse($request);
            }else{
                // emailVerifyMail($request->email, $otp);

                                
                if(Hash::check($request->password, $user->password)) {
                    sendEmailVerificationMail($request->email, $otp);
                    return redirect()->back()->with('success', 'Email is not verified yet. Please check your mailbox for verification link.');
                } else {
                    return redirect()->back()->with('error', 'These credentials do not match our records.');
                }
                    
             
                    
                // return redirect('front-panel/verify/'.$request->email)->with('success', 'Verification sent successfully');
            }
        }else{
            return redirect()->back()->with('error', 'Login credentials are invalid');
        }
    }
    
    protected function credentials(Request $request){
        $credentials = $request->only($this->username(), 'password');
        return $credentials;
    }

    public  function getTwillioAccessTokenweb(){
        $response = [];
        try{
                $user = Auth::user();
                $userFolderName = config('constants.user_image.upload_path');
                $path= getS3Url($userFolderName);
                $deviceType = 3;
                $userId= $user->id;
                $env = env("APP_ENV");
                $identity = $env.'_'.$userId;
                $response['identity'] = $identity;
                $response['env'] = $env;
                $users = User::select(['*'])->whereRaw('id='.$user->id)->first();
                
                if($users->twilio_s_id == '' || $users->twilio_s_id == NULL){
                    $userId= $users->id;
                    $fullName=$users->first_name." ".$users->last_name;
                    $profileImageUrl=$path.$users->profile_picture;
                    $users->twilio_s_id = createUserTwillio($users->id,$fullName,$profileImageUrl);
                    $save = $users->save();

                    if($users->twilio_s_id != ''){
                        $users->twilio_s_id = updateUserTwillio($users->twilio_s_id,$users->id,$fullName,$profileImageUrl);
                    }
                }

                $token = getAccessToken($users->id,$deviceType);
                $response['accessToken'] = $token;
                $users->save();
                
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
          return $response;
        }
        return $response;
    }




    public function showSetForm(Request $request){
        $email = $request->email;
        
        $title='Reset password';
        return view('frontend.auth.passwords.set-password', compact('email','title'));
	}
}
