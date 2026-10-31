<?php

namespace App\Http\Controllers\backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use Auth;
use Cookie;

use App\Models\AdminUser;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function guard()
    {
        return auth()->guard('admin_user');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('backend.auth.passwords.email');
    }

    //defining which password broker to use, in our case its the admins
    public function broker() {
        return Password::broker('admin_user');
    }

    public function sendResetLinkEmail(Request $request)
    {

        $this->validate($request,
            [
            'email' => 'required|email:rfc,dns',
            ],
            [
                'email.required' => 'Email is required',
            ]
        );
        $user_check = AdminUser::where([ ['email', $request->email]])->first();

        if(!is_null($user_check)){
            if($user_check->status ==1 ){
                $response = $this->broker()->sendResetLink(
                    $request->only('email')
                );

                if ($response === Password::RESET_LINK_SENT) {
                    return redirect()->back()->with('status', trans($response));
                }

                return back()->withErrors(
                    ['email' => trans($response)]
                );
            }else{
                return back()->withErrors(['deactivated' => 'Your account has been deactivated']);
            }
        } else {
            return back()->withErrors(['email' => 'Email id does not match with our record']);
        }
    }
}
