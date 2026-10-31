<?php

namespace App\Http\Controllers\backend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use Auth;
use Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\AdminUser;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin-panel';

    protected function guard()
    {
        return auth()->guard('admin_user');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }


    /**
     * @summary Overriding default validation to add captcha validation
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:30',
            'password_confirmation' => 'required',
        ];
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $hash_token = Hash::make($token);
        $email = get_decrypted_value($request->get('secure'),true);

        $pass_data = DB::select('select * from password_resets where email = :email', ['email' => $email]);
        $current_date = Carbon::now();

        if( !empty($pass_data) ){
            foreach ( $pass_data as $res ){
                if($res->created_at >= $current_date AND  $current_date <= date("Y-m-d H:i:s", strtotime("+24 hours",strtotime($res->created_at))))
                {
                    return redirect('/reset-password-link-expired');

                } else {
                    if(Hash::check($token, $res->token)){
                        return view('backend.auth.passwords.reset')->with(
                            ['token' => $token, 'email' => $email]
                        );
                    } else {
                        return redirect('admin-panel/link-expired');
                    }
                }

            }
        } else {
            return redirect('admin-panel/link-expired');
        }

    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\Response
     */
    protected function sendResetResponse($response)
    {
        // return response()->json(['success' => trans($response)]);
        Auth::guard('admin_user')->logout();
        return redirect('admin-panel')->with('status', 'Your password has been reset');
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\Response
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return back()->withErrors($response);
    }

    //defining our password broker function
    protected function broker() {
        return Password::broker('admin_user');
    }

    // Custom messages
    protected function validationErrorMessages()
    {
    return [
        'password.required'=> 'The password is required.',
        'password_confirmation.required'=> 'The confirm password is required.',
        ];
    }
}
