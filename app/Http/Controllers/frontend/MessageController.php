<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Country;
use App\Models\BlockUser;
use App\Models\UserFollow;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Auth;
use DB;
use File;
use Carbon\Carbon;
use Validator;
use Storage; 
use Twilio\Rest\Client;
use App\Http\Requests\Frontend\UserFollowRequest;;

class MessageController extends Controller
{
    //

    public function __construct()
    {
    	$this->middleware('web');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userId= $user->id;
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
        $response['users']= User::where('id', '<>', $user->id)->get();
        $response['title']= 'Messages';

        return view('frontend.message.index',$response);

    } 

    public function clearReadCount(Request $request) {
        return true;
    }

   
    public function chat(Request $request, $ids)
    {
            $authUser = $request->user();
            $otherUser = User::find(explode('-', $ids)[1]);
            $users = User::where('id', '<>', $authUser->id)->get();
            $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_ACCOUNT_TOKEN'));
            // Fetch channel or create a new one if it doesn't exit    
            return view('frontend.message.index',compact('users', 'otherUser'));
    }

}
