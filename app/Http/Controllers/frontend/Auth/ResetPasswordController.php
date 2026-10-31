<?php
namespace App\Http\Controllers\frontend\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use Auth;
use Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AdminUser;
use App\Models\User;

class ResetPasswordController extends Controller{
	use ResetsPasswords;
    public function __construct(){
		$this->middleware('guest:web');
	}
    public function showResetForm(Request $request, $token = null){
        $email = $request->email;
        $title='Reset password';
        return view('frontend.auth.passwords.set-password', compact('email','title'));
		// return view('frontend.auth.passwords.reset',[
		// 	'title' => 'Reset Admin Password',
		// 	'passwordUpdateRoute' => 'admin.password.update',
		// 	'token' => $token,
		// ]);
	}

    public function reset(Request $request){
        $this->validate($request,[
            'email' => 'required|email:rfc,dns',
            'password' => 'min:8|max:30',
            'password_confirmation' => 'min:8|max:30'
            ],
            [
                'email.required' => 'Email is required',
                'password.required' => 'Password is required',
                'password_confirmation.required' => 'Confirm Password is required'
            ]
        );
        try{
            $data = $request->all();
            $user = User::where('email', $request->email)->first();
            if(!is_null($user)){
                if($user->status ==1 ){
                    $user->password = Hash::make($request->password);
                    $user->save();
                    return redirect()->route('front-login')->with('success', 'Password has been changed successfully');  
                    // return redirect()->back()->with('success', 'Password has been changed successfully');                    
                }else{
                    return back()->withErrors(['deactivated' => 'Your account has been deactivated']);
                }
            }else{
                return back()->withErrors(['email' => 'Email id does not match with our record']);
            }
        } catch (\Throwable $th) {
            return back()->withErrors(['email' => 'Email id does not match with our record']);
        }
        $request->validate($this->rules(), $this->validationErrorMessages());
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
        ? $this->sendResetResponse($request, $response)
        : $this->sendResetFailedResponse($request, $response);
    }

	protected function broker(){
		return Password::broker('users');
	}

	/**
	 * Get the guard to be used during password reset.
	 *
	 * @return \Illuminate\Contracts\Auth\StatefulGuard
	 */
	protected function guard(){
		return Auth::guard('web');
	}
	protected function rules(){
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:30',
            'captcha'  => 'required|captcha',
        ];
    }
}
