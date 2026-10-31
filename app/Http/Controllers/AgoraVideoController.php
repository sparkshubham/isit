<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Events\MakeAgoraCall;

class AgoraVideoController extends Controller
{
    public function index(Request $request)
    {
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', Auth::id())->get();
        return view('frontend.common.agora-test', ['users' => $users]);
    }

    public function token(Request $request)
    {
        echo "sdfsdfsf 123";exit;
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channelName;
        $user = Auth::user()->name;
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);
        return getResponse(100,200,$token);
        // return $token;
    }

    public function callUser(Request $request)
    {

        $data['userToCall'] = $request->user_to_call;
        $data['channelName'] = $request->channel_name;
        $data['from'] = Auth::id();

        broadcast(new MakeAgoraCall($data))->toOthers();
    }




    public function getAvailableEvents(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            $data = Array();
            $timeoffset = 19800;
            $data = getAvailableEvent($timeoffset,$user->id);
        
            if(empty($data)){
                return $response;
            }
            $data['user_id'] = $user->id;
            if(!empty($data)){
                // $name = str_replace('"','', );
                $name = str_replace(' ','_',$data['name']);
                $data['token'] = getAgoraToken($name,$data['id'],$data['user_id'],$data['isOwner'],3600);
                $data['recToken'] = getAgoraToken($name,$data['id'],1,0,3600);
            }
            // prd($data);
            // dd($data);
            $userFolderName = config('constants.user_image.upload_path');
            $data['path']['user_media'] = getS3Url($userFolderName);
            $response = $data;
            // dd($data);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return $response;
            // return view('frontend.agora.agora-index',$response);
        }
        return $response;
        // return view('frontend.agora.agora-index',$response);
    }


    



}