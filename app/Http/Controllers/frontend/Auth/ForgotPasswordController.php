<?php
namespace App\Http\Controllers\frontend\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Auth;
use Cookie;
use App\Models\AdminUser;
use App\Models\User;

class ForgotPasswordController extends Controller{
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

    public function showLinkRequestForm(){
        return view('frontend.auth.passwords.email');
        // return view('frontend.auth.passwords.email',[
        //     'title' => 'Admin Password Reset',
        //     'passwordEmailRoute' => 'admin.password.email',
        //     'loginRoute' => 'admin.login'
        // ]);
    }
    
    public function __construct(){
        $this->middleware('guest:web');
    }

    public function sendResetLinkEmail(Request $request){
        // $this->validate($request, [
        //     'email' => 'required_without:phone_number|email:rfc,dns',
        //     'phone_number' => 'required_without:email|numeric',
        // ], [
        //     'email.required_without' => 'Phone number is required when email is not provided.',
        //     'email.email' => 'Invalid email format.',
        //     'phone_number.required_without' => 'Email is required when phone number is not provided.',
        //     'phone_number.numeric' => 'Invalid phone number format.',
        // ]);
        
        if (!empty($request->email)) {
            // Email verification logic
            $otp = rand(1000, 9999);
            $mailData['otp'] = $otp;
            $user_check = User::where('email', $request->email)->first();
            if (!is_null($user_check)) {
                if ($user_check->status == 1) {
                    if ($request->mail_send == 0) {
                        emailVerifyMail($request->email, $otp);
                        return redirect('verify/email/'.$request->email)->with('success', 'Verification code sent successfully');
                    } else {
                        return redirect('verify/email/'.$request->email);
                    }
                } else {
                    return back()->withErrors(['deactivated' => 'Your account has been deactivated']);
                }
            } else {
                return back()->withErrors(['email' => 'Email id does not match with our records']);
            }
        } 
        elseif (!empty('phone_number')) {
            // Phone number verification logic
            $otp = rand(1000, 9999);
            $user_check = User::where('phone_number', $request->phone_number)->first();
            if (!is_null($user_check)) {
                if ($user_check->status == 1) {
                    $data_message=['message'=>'','type'=>'2','phone_number'=>$request->phone_number,'otp'=>$otp];
                    sendOtptophone($data_message);
                    $user_check->phone_verify_token = $otp;
                    $user_check->save();
                    return redirect('verify/phone/'.$request->phone_number)->with('success', 'OTP sent to your phone number');
                } else {
                    return back()->withErrors(['deactivated' => 'Your account has been deactivated']);
                }
            } else {
                return back()->withErrors(['phone_number' => 'Phone number does not match with our records']);
            }
        }

    }
    public function showSetForm(Request $request){
        $email = $request->email;
        
        $title='Reset password';
        return view('frontend.auth.passwords.set-password', compact('email','title'));
	}
    public function broker(){
        return Password::broker('users');
    }
    public function guard(){
        return Auth::guard('web');
    }
}