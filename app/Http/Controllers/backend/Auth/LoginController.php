<?php

namespace App\Http\Controllers\backend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Cookie;

class LoginController extends Controller
{
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin-panel/dashboard';

    protected function guard()
    {
        
        return auth()->guard('admin_user');
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
        $data = [
                'email' => $email,
                'password' => $password,
                'is_checked' => $is_checked
            ];

        return view('backend.auth.login')->with($data);
    }
    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|string|email:rfc,dns',
                'password' => 'required|string|min:8|max:30',
            ],
            [
                'email.required' => 'Email is required',
                'password.required' => 'Password is required',
            ]
        );
    }
     /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function attemptLogin(Request $request)
    {
        $response = $this->guard('admin_user')->attempt(
            $this->credentials($request), $request->filled('remember')
        );
        // echo "<pre>";var_dump($response);exit;
        if($response){
            if($request->filled('remember')){
                Cookie::queue('username',$request['email'], strtotime("+1 year"));
                Cookie::queue('password',$request['password'], strtotime("+1 year"));
            }else{
                Cookie::queue('username','', -5);
                Cookie::queue('password','', -5);
            }
        }
        // prd($response);
        return $response;
    }
    /**
     * @summary overriding default logout function
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 10 Oct 2019
     */
    public function logout(Request $request){
        Auth::guard('admin_user')->logout();
        return redirect('/admin-panel');
    }
    /**
     * Validate the user login.
     * @param Request $request
     */
    public function passwordLinkExpired(){
        return view('backend.auth.link-expired');
    }
}
