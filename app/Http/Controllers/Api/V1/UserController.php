<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Response;
use Hash;
use DB;
use URL;
use Auth;
use Mail;
use Storage;
use Carbon\Carbon;
use Twilio\Jwt\AccessToken;

use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\PasswordResetRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\ValidateResetRequest;
use App\Http\Requests\Api\UserFollowRequest;
use App\Http\Requests\Api\UserBlockRequest;

use App\Models\User;
use App\Models\BlockUser;
use App\Models\Country;
use App\Models\Category;
use App\Models\UserFollow;
use App\Models\OauthAccessTokens;
use App\Models\UserHelp;
use App\Models\AppVersion;
use App\Models\UserNotification;
use App\Models\Event;
use App\Models\Hobby;


use App\Jobs\UserWelcomeEmailJob;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;

class UserController extends Controller
{
     /**
     * @summary : Api for register user
     * @param : first_name, last_name, email, password, device_type
     * @return : json
     * @author Ashok Dadhich
     * @created_at 07 Dec 2021
     */
    public function register(RegisterRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            $user = new User;
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->phone_number = $request['phone_number'];
            $user->device_type = $request['device_type'];
            $user->last_login = date('Y-m-d H:i:s');
            $user->dob = $request['dob'];
            $user->gender = (isset($request['gender']) ? $request['gender'] : '');
            $user->is_verified = 1;
            if($request['password']) 
                $user->password = Hash::make($request['password']);
            $user->save();
            /******** Create twillio id after register Verefication have been removed */
            $userId = $user->id;
            $user = User::select(['id','verifiy_token','first_name','last_name'])->whereRaw('id='.$userId)->first();
            // prd($user);
            $user->is_verified = 1;
            $user->verifiy_token = '';
            $fullName = $user->first_name.' '.$user->last_name;
            $profileImageUrl = '';
            // echo $fullName;exit;
            $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
            $user->save();
            /******** Create twillio id after register Verefication have been removed */
            // $code = sendEmailVerificationMail($request['email']);
            /* if($code !='')
                return getResponse($code,404); */
        } catch(\Exception $e){
            echo $e;
            exit;
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse(100,200,$response);
    }
    /**
     * @summary : Api for login user
     * @param : email, password, device_type?(1:ios,2:android), device_token?
     * @return : json
     * @author Ashok Dadhich
     * @created_at 07 Dec 2021
     */
    public function login(LoginRequest $request){
        $response = [];
        $isFirstLogin = 0;
        $folderName = config('constants.user_image.upload_path');
        $response['path']['user'] = getS3Url($folderName);
        if(Auth::attempt(['email' => $request->email,'password'=>$request->password])){
            $user = Auth::user();
            if($user->is_verified != 1){
                return getResponse(604,404);
            }else if($user->status != 1){
                return getResponse(603,404);
            }else if (!Hash::check($request->password, $user['password'])) {
                return getResponse(602,403);
            }else{
                OauthAccessTokens::where(['user_id'=>$user->id])->delete();
                $tokenObj = $user->createToken('isit');
                if($user->last_login == NULL){
                    $isFirstLogin = 1;
                }
                $user->last_login  = gmdate('Y-m-d H:i:s');
                // $user->device_type  = $request->device_type;
                // $user->device_id  = $request->device_id;
                $user->save();
                $user->encrypted_id = get_encrypted_value($user->id,true);
                $response['user'] = getProfileData($user->id);
                $response['token'] = $tokenObj->accessToken;
                $response['isFirstLogin'] = $isFirstLogin;
                // $tokenObj->token->device_type = $request->device_type;
                // $tokenObj->token->device_token = $request->device_token;
                // $tokenObj->token->save();
                return getResponse(101,200,$response);
            }
        }
        else{
            return getResponse(602,403);
        }
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : get
     * @api : profile/{userId}
     * @summary : Get User Detail of userId
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 08 Dec 2021
     */
    public function Profile(Request $request){
        $user = Auth::user();
        if(empty($request->route('userId'))){ return getResponse(6000,401); }
        // $userId = $request->userId;
        if(!empty($request->route('userId')))
            $userId = $request->route('userId');    
        else
            $userId = $user->id;
        $response = [];
        $todayDate = Carbon::today()->toDateString();
        $folderName = config('constants.user_image.upload_path');
        $url = getS3Url($folderName);
        $response['path']['user'] = $url;
        $profile = User::where([ ['id', $userId]])
            ->select(['id','name','email','status'])
            ->first();
        // var_dump($profile);
        if(!empty($profile)){
            if($profile->status != 1){
                return getResponse(603,404);
            }else if($profile->is_block == 1){
                if($profile->is_block_by_you == 1){
                    return getResponse(636,404);
                }else{
                    return getResponse(640,404);
                }
            }else{
                $profile->encrypted_id = get_encrypted_value($userId,true);
                if($user->id == $userId){
                    // If same as logged in user than send all detail of User
                    $response['user'] = getProfileData($userId,0);
                }else{
                    // If not than send selected detail of user
                    $response['user'] = getProfileData($userId,1);
                }
                return getResponse(103,200,$response);
            }
        }else{
            return getResponse(640,404,$response);
        }
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : profile
     * @summary : Get User Detail
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 08 Dec 2021
     */
    public function resendVerificationMail(PasswordResetRequest $request){
        $response = [];
        $code = sendEmailVerificationMail($request->email);
        if($code != ''){
            return getResponse($code,404);
        }else{
            return getResponse(608,200,$response);
        }
    }
    /**
     * @summary : Api for send forgot password email
     * @url : password-reset
     * @type : post
     * @param : email
     * @return : json
     * @author Ashok Dadhich
     * @created_at 09 Dec 2021
     */
    public function sendForgotPasswordLink(PasswordResetRequest $request){
        $code = sendEmailPasswordResetToken($request->email);
        if($code !=''){
            return getResponse($code,403);
        }else{
            return getResponse(107,200);
        }
    }

    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : change-password
     * @summary : Api for change password using old password
     * @param : old_password, new_password, confirm_password
     * @return : json
     * @author Ashok Dadhich
     * @created_at 09 Dec 2020
     */
    public function changePassword(ChangePasswordRequest $request){
    	//prd($request->all());
    	$currentPassword = Auth::user()->password;
    	if (!(Hash::check($request->get('old_password'), $currentPassword))) {
            return getResponse(611,400);
        }
        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return getResponse(612,400);
        }
        $user = Auth::user();
        $user->password = Hash::make($request['new_password']);
        $user->save();
        return getResponse(613,200);
    }

    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : update-password
     * @summary : Api for change password using old password
     * @param : password_reset_token, new_password, confirm_password
     * @return : json
     * @author Ashok Dadhich
     * @created_at 09 Dec 2021
     */
    public function updatePassword(UpdatePasswordRequest $request){
    	//prd($request->all());
        $user = User::where([ ['password_reset_token', $request->password_reset_token]])->first();
        if(empty($user)){
            return getResponse(109,400);
        }
        $user->password = Hash::make($request->new_password);
        $user->password_reset_token = '';
        $user->save();
        return getResponse(610,200);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : profile
     * @summary : Api for update user profile
     * @param : first_name?, last_name?, email?, phone_number?, image?, country_code?
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 Dec 2021
     */
    public function setProfile(UpdateProfileRequest $request){
        $response = [];
        $folderName = config('constants.user_image.upload_path');
        $response['path']['user'] = getS3Url($folderName);
        if($request->all()){
            DB::beginTransaction();
            try{
                $emailChange = false;
                $user = Auth::user();
                $currentEmail = $user->email;
                $user = User::select(['*'])->whereRaw('id='.$user->id)->first();
                // prd($user);
                $user->first_name = $request['first_name'];
                $user->last_name = $request['last_name'];
                $user->country_code = $request['country_code'];
                $user->phone_number = $request['phone_number'];
                $user->bio_text = $request['bio_text'];
                $user->username = $request['username'];
                $user->dob = $request['dob'];
                $user->gender = (isset($request['gender']) ? $request['gender'] : '');
                if($request->hasFile('image')){
                    $file = $request->file('image');
                    $destinationPath = config('constants.user_image.upload_path');
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    if(isset($user->profile_picture) && $user->profile_picture !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$user->profile_picture)){
                        Storage::disk('s3')->delete($destinationPath.'/'.$user->profile_picture);
                        // unlink($destinationPath.$user->profile_picture);
                    }
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    // $file->move($destinationPath,$fileName);
                    $user->profile_picture = $fileName;
                }
                if($user->twilio_s_id == '' || $user->twilio_s_id == NULL){
                    $fullName = $request['first_name'].' '.$request['last_name'];
                    $profileImageUrl = '';
                    if($user->profile_picture!='')
                        $profileImageUrl = $response['path']['user'].$user->profile_picture;
                    $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
                }
                $hobbiesIds = (isset($request['hobbies_ids']) ? $request['hobbies_ids'] : '');
                $NewHobbiesText = (isset($request['new_hobbies_text']) ? $request['new_hobbies_text'] : '');
                addUserHobbies($hobbiesIds,$NewHobbiesText,$user->id);
                $save = $user->save();
                if($user->twilio_s_id != ''){
                    $fullName = $request['first_name'].' '.$request['last_name'];
                    $profileImageUrl = '';
                    if($user->profile_picture!='')
                        $profileImageUrl = $response['path']['user'].$user->profile_picture;
                    $user->twilio_s_id = updateUserTwillio($user->twilio_s_id,$user->id,$fullName,$profileImageUrl);
                }
            } catch (\Exception $e){
                DB::rollback();
                // echo $e->getMessage();exit;
                $this->optionalData['message'] = $e->getMessage();
                return getResponse(6000,400,$response);
            }
            DB::commit();
            $response['user'] = getProfileData($user->id);
            return getResponse(108,200,$response);
        }else{
            return getResponse(6000,400,$response);
        }
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : post
     * @api : validate-reset-token
     * @summary : Api for change password using old password
     * @param : password_reset_token
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 Dec 2020
     */
    public function validateResetCode(ValidateResetRequest $request){
        //prd($request->all());
        $user = User::where([['password_reset_token', $request->password_reset_token],['email',$request->email]])->first();
        if(empty($user)){
            return getResponse(109,400);
        }
        return getResponse(614,200);
    }

    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : countries
     * @summary : Api for get countries list
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 16 Dec 2020
     */
    public function getCountries(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $countries = Country::select(['name','id'])->where(['status'=>1])->orderBy('name','asc')->get();
            if(empty($countries)){ throw new \Exception(__('api-messages.615')); }
            $response = $countries;
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : categories
     * @summary : Api for get countries list
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 16 Dec 2020
     */
    public function getCategories(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $categories = Category::select(['name','id'])->where(['status'=>1,'type'=>0])->orderBy('name','asc')->get();
            if(empty($categories)){ throw new \Exception(__('api-messages.615')); }
            $response = $categories;
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Accept: application/json
     * @type : get
     * @api : logout
     * @summary : Common to logout currunt session
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 16 Dec 2021
     */
    public function logout(Request $request){
    	$user = Auth::guard('api')->user();
        $user = User::select(['id','device_type','device_id'])->whereRaw('id='.$user->id)->first();
        $user->device_id  = '';
        $user->voip_device_id  = '';
        $user->save();
        Auth::user()->token()->delete();
    	return getResponse(110,200);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : public-visibility
     * @summary : Api for change public visibiliy og user
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 27 Dec 2021
     */
    public function publicVisibilityUpdate(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        // echo $user->id;exit;
        $userData = User::where([['id', $user->id]])->first();
        $userData->is_public_visibility = ($userData->is_public_visibility == 1 ? 0 : 1);
        $userData->save();
        if($userData->is_public_visibility == 1){
            acceptAllFollowingRequest($user->id);
        }
        $response = ['is_public_visibility'=>$userData->is_public_visibility];
        return getResponse(111,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : follow_user
     * @summary : Api for follow user
     * @param : follower_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function followUser(UserFollowRequest $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        $request['user_id'] = $user->id;
        $response = [];
        if(empty($request->follower_id)){ throw new \Exception(__('api-messages.6000')); }
        $isBlocked = getBlockByUser($user->id,$request->follower_id);
        if($isBlocked){
            return getResponse(640,400);
        }
        try{
            $userFollow = new UserFollow();
            $userFollow['follower_id'] = $request->follower_id;
            $userFollow['user_id'] = $user->id;
            $userFollow['action_user_id'] = $userFollow['follower_id'];
            $isPublic = getUserPublicVisibilityStatus($userFollow['follower_id']);
            if($isPublic == 0){
                $userFollow['status'] = 2;
                sendNotificationWithData($user->id,$request->follower_id,10,['user_id'=>$user->id,'notification_type'=>10]);
            }else{
                $userFollow['status'] = 1;
                // Notification Send ($senderId,$reciverId,$notificationMsgKey,$data)
                sendNotificationWithData($user->id,$request->follower_id,5,['user_id'=>$user->id,'notification_type'=>5]);
            }
            $userFollow->save();
            $response['follower_id'] = $userFollow->id;
            $response['status'] = $userFollow['status'];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(623,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unfollow_user
     * @summary : Api for unfollow user
     * @param : follower_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function unfollowUser(Request $request){
    	//prd($request->all());
        $user = Auth::user();
        $response = [];
        try{
            if(empty($request->follower_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isBlocked = getBlockByUser($user->id,$request->follower_id);
            if($isBlocked){
                return getResponse(640,400);
            }
            $followUser = UserFollow::where(['user_id'=>$user->id,'follower_id'=>$request->follower_id])->first();
            if(empty($followUser)){
                return getResponse(112,400);
            }
            $followUser->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(624,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : follower-list
     * @summary : Api for follower list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function followerList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.username','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getUser', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->where(function($query) use($userId){
                $query->where('users_follows.follower_id', $userId);
                $query->whereRaw('users_follows.status = 1');
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr =  $value['getUser'];
                    $arr['follower_count'] = $value['follower_count'];
                    $arr['post_count'] = $value['post_count'];
                }
                return $arr;
                // return $value['post_count'];
            });
            // prd($NewUsers->toArray());
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['users'] = $NewUsers->toArray();
            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : following-list
     * @summary : Api for following list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function followingList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getFollower'=>function($query) use($user){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.username','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getFollower', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('CONCAT(users.first_name, " ", users.last_name) LIKE "%'.$search_keyword.'%" OR users.last_name LIKE "%'.$search_keyword.'%"');
                        });
                }
            })
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
                $query->where('users_follows.status', 1);
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getFollower'])){
                    $arr =  $value['getFollower'];
                }
                $arr['follower_count'] = $value['following_count'];
                $arr['post_count'] = $value['post_follower_count'];
                return $arr;
                // return $value['post_count'];
            });
            // prd($NewUsers->toArray());
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['users'] = $NewUsers->toArray();
            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : following-request-list
     * @summary : Api for following request list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 Feb 2022
     */
    public function followingRequestList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.is_star']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getUser', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->where(function($query) use($userId){
                $query->where('users_follows.follower_id', $userId);
                $query->whereRaw('users_follows.status = 2');
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr =  $value['getUser'];
                }
                return $arr;
                // return $value['post_count'];
            });
            // prd($NewUsers->toArray());
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['users'] = $NewUsers->toArray();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : accept-following-request
     * @summary : Api for accept following request
     * @param : user_id, follower_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 Feb 2022
     */
    public function acceptFollowRequest(Request $request){
    	$response = [];
        $user = Auth::user();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            $userId = $request->user_id;
            $follwerId = $user->id;
            $followUser = UserFollow::where(['user_id'=>$userId,'follower_id'=>$follwerId,'status'=>2])->first();
            if(empty($followUser)){
                return getResponse(112,400);
            }
            $followUser->status = 1;
            $followUser->save();
            $data = ['user_id'=>$follwerId,'notification_type'=>9];
            sendNotificationWithData($user->id,$userId,9,$data);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(628,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : decline-following-request
     * @summary : Api for decline following request
     * @param : user_id, follower_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 Feb 2022
     */
    public function declineFollowRequest(Request $request){
    	$response = [];
        $user = Auth::user();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            $userId = $request->user_id;
            $follwerId = $user->id;
            $followUser = UserFollow::where(['user_id'=>$userId,'follower_id'=>$follwerId,'status'=>2])->first();
            if(empty($followUser)){
                return getResponse(112,400);
            }
            $followUser->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(627,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : following-request-list
     * @summary : Api for following request list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 10 Feb 2022
     */
    public function searchUserList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        // echo $blockUserIds;exit;
        try{
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $user->id;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $users = User::select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.is_star'])
            // ->whereNotIn('users.id', [$blockUserIds])
            ->where(function($query) use($userId,$search_keyword,$blockUserIds){
                $query->where('users.status', 1);
                // $query->where('users.is_public_visibility', 1);
                $query->whereRaw('users.id != '.$userId);
                $query->whereRaw('users.deleted_at IS NULL AND users.id NOT IN ('.$blockUserIds.')');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(CONCAT(users.first_name, " ", users.last_name)) like "%'.strtolower($search_keyword).'%"');
                            // $query->orWhereRaw('LOWER(users.email) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            $response['users'] = $users->items();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : block_user
     * @summary : Api for block any user
     * @param : block_user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Feb 2022
     */
    public function blockUser(UserBlockRequest $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        $request['user_id'] = $user->id;
        $response = [];
        if(empty($request->block_user_id)){ throw new \Exception(__('api-messages.6000')); }
        try{
            $userBlock = new BlockUser();
            $userBlock['block_user_id'] = $request->block_user_id;
            $userBlock['user_id'] = $user->id;
            $userBlock['block_by'] = $user->id;
            $userBlock->save();
            /**** Enter vice versa entry */
            $userBlock = new BlockUser();
            $userBlock['block_user_id'] = $user->id;
            $userBlock['user_id'] = $request->block_user_id;
            $userBlock['block_by'] = $user->id;
            $userBlock->save();
            /**** Enter vice versa entry */
            $response['user_block_id'] = $userBlock->id;
            /**** Remove follower after block user.. User have to resend follwing request after block */
            removeFollowUsers($user->id,$request->block_user_id);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(634,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unblock_user
     * @summary : Api for unblock any user
     * @param : unblock_user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Feb 2022
     */
    public function UnblockUser(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        $request['user_id'] = $user->id;
        $response = [];
        if(empty($request->unblock_user_id)){ throw new \Exception(__('api-messages.6000')); }
        try{
            $blockUser = BlockUser::where(['user_id'=>$user->id,'block_user_id'=>$request->unblock_user_id,'block_by'=>$user->id])->first();
            if(empty($blockUser)){
                return getResponse(112,400);
            }
            $blockUser->delete();
            /**** Remove vice versa entry */
            $blockUser = BlockUser::where(['user_id'=>$request->unblock_user_id,'block_user_id'=>$user->id])->first();
            $blockUser->delete();
            /**** Remove vice versa entry */
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(635,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : block-user-list
     * @summary : Api for blocked user list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function blockUserList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = BlockUser::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.is_star']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
            ])
            ->select('block_users.id','block_users.user_id','block_users.block_user_id','block_users.block_by')
            ->whereHas('getUser', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->where(function($query) use($userId){
                $query->where('block_users.user_id', $userId);
                $query->where('block_users.block_by', $userId);
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr =  $value['getUser'];
                }
                return $arr;
                // return $value['post_count'];
            });
            // prd($NewUsers->toArray());
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['users'] = $NewUsers->toArray();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : global-settings
     * @summary : Api for all global settings
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 1 March 2022
     */
    public function globalSettings(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $reportsCat = Category::select(['name','id'])->where(['status'=>1,'type'=>1])->orderBy('name','asc')->get();
            $response['report_categories'] = $reportsCat;
            $helpsCat = Category::select(['name','id'])->where(['status'=>1,'type'=>2])->orderBy('name','asc')->get();
            $response['help_categories'] = $helpsCat;
            $ProductCat = Category::select(['name','id'])->where(['status'=>1,'type'=>3])->orderBy('name','asc')->get();
            $response['product_categories'] = $ProductCat;
            $userReportCat = Category::select(['name','id'])->where(['status'=>1,'type'=>4])->orderBy('name','asc')->get();
            $response['report_user_categories'] = $userReportCat;
            $response['admin_setting'] = getGlobalSetting();
            $response['is_username_set'] = (($user!= NULL && getUsername($user->id) != NULL) ? 1 : 0);
            
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @summary : Api for send help request to admin
     * @type : post
     * @param : user_id, category, description
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Feb 2022
     */
    public function helpUser(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->category)){ throw new \Exception(__('api-messages.6000')); }
            $user = Auth::user();
            $UserHelp = new UserHelp();
            $UserHelp->user_id = $user->id;
            $UserHelp->description = $request['description'];
            $UserHelp->status = 0;
            $UserHelp->category = (empty($request->category) ? '' : $request->category);
            $UserHelp->save();
            $response['user_help_id'] = $UserHelp->id;
            $code = 639;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : getTwillioAccessToken
     * @summary : Api for get twillioaccessToken
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 31 March 2022
     */
    public function getTwillioAccessToken(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $deviceType = $request->header('device-type');
            $token = getAccessToken($user->id,$deviceType);
            $response['accessToken'] = $token;
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : getAppVersion
     * @summary : Api for get versions detail
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 12 April 2022
     */
    public function getAppVersion(Request $request){
        $response = [];
        if(empty($request->header('device-type'))){ throw new \Exception(__('api-messages.6000')); }
        try{
            $deviceType = $request->header('device-type');
            $appVersion  = AppVersion::select('device_type','current_version','min_version','is_force_update','msg_normal_update','is_prompt_update')
            ->where(function($query) use($deviceType){
                $query->where('device_type', $deviceType);
            })
            ->first();
            $response['data'] = $appVersion->toArray();
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
     /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : uploadFileS3
     * @summary : Api to upload file to s3
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 April 2022
     */
    public function uploadFileS3(Request $request){
        $response = [];
        if(empty($request->image) || empty($request->folder)){ throw new \Exception(__('api-messages.6000')); }
        try{
            if($request->hasFile('image')){
                $file = $request->file('image');
                $destinationPath = config('constants.group_media.upload_path');
                $extension = $file->extension();
                $fileName = rand().time().'.'.$extension;
                $pathToUpload = $destinationPath.'/'.$fileName;
                Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                $folderName = config('constants.group_media.upload_path');
                $response['mediaUrl'] = getS3Url($folderName).$fileName;
            }
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
     /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : post
     * @api : get-twilio-callback
     * @summary : Api to call back from twillio
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 25 April 2022
     */
    public function getTwilioCallBack(Request $request){
        $setting = getGlobalSetting();
        if($setting['is_1to1_twillio_audio'] == 0 && ($setting['is_1to1_twillio_video'] == 0 && $setting['is_group_twillio_video'] == 0)){
            return 1;
        }
        $returnData = Array();
        $data = $request->all();
        // $sampleStr='{
        //     "RoomStatus": "in-progress",
        //     "RoomType": "group",
        //     "RoomSid": "RMd2ddd49784318313344ae0def7d025bc",
        //     "RoomName": "G~V~CH62d4c25927724bc6943caff259567deb",
        //     "ParticipantStatus": "connected",
        //     "ParticipantIdentity": "qa_108",
        //     "SequenceNumber": "1",
        //     "StatusCallbackEvent": "participant-connected",
        //     "Timestamp": "2022-09-13T06:56:29.800Z",
        //     "ParticipantSid": "PA458df96f399e603e069dd4b526e2b112",
        //     "AccountSid": "AC_REDACTED"
        //   }';
         // $data = json_decode($sampleStr,true);
        // prd($data);
      
        // $data['RoomName'] = '';
        $participantId = explode('~', $data['RoomName']);
    //    prd($participantId);
        // For indivisual Call
        if($participantId['0'] == 'I'){
            // Callee Detail 
            // prd($participantId['0']);
            $calleeUserId = getUserIdFromTwillioIdentity($participantId['3']);
            $calleeUserData = getProfileData($calleeUserId);
            // Caller  Detail 
            $callerUserId = getUserIdFromTwillioIdentity($participantId['2']);
            $callerUserData = getProfileData($callerUserId);
            $returnData['payload']['calleeName'] = $calleeUserData['full_name'];
            $returnData['payload']['callerName'] = $callerUserData['full_name'];
            // && $data['SequenceNumber'] == 1
            if ($data['StatusCallbackEvent'] == "participant-connected" && $data['SequenceNumber'] == 1){
                // $returnData['payload']['type'] = $data['call'];
                $returnData['payload']['type'] = 1;
                $returnData['payload']['callerUserId'] = $participantId['2'];
                $returnData['payload']['calleeUserId'] = $participantId['3'];
                $returnData['payload']['callBackEvent'] = $data['ParticipantStatus'];
                $returnData['payload']['callBackEventStatus'] = $data['StatusCallbackEvent'];
                $returnData['payload']['callId'] = $data['RoomName'];
                $returnData['payload']['callType'] = $participantId['1'];
                if($callerUserData['profile_picture'] != ''){
                    $folderName = config('constants.user_image.upload_path');
                    $imgPath = getS3Url($folderName);
                    $returnData['payload']['callerPic'] = $imgPath.$callerUserData['profile_picture'];
                }else{
                    $returnData['payload']['callerPic'] = '';
                }
                if($calleeUserData['device_type'] == 1){
                    $fcm_token = $calleeUserData['voip_device_id'];
                    $extra = sendApnsPushNotification($fcm_token, $callerUserData['full_name'].' called you.' , $returnData,$calleeUserData['device_type']);
                }else{
                    $fcm_token = $calleeUserData['device_id'];
                    $extra = sendFcmPushNotification($fcm_token, $callerUserData['full_name'].' called you.' , $returnData,$calleeUserData['device_type']);
                }
            } elseif ($data['StatusCallbackEvent'] === "participant-disconnected") {
                $returnData['payload']['type'] = 2;
                $returnData['payload']['callerUserId'] = $participantId['2'];
                $returnData['payload']['calleeUserId'] = $participantId['3'];
                $returnData['payload']['callBackEvent'] = $data['ParticipantStatus'];
                $returnData['payload']['callBackEventStatus'] = $data['StatusCallbackEvent'];
                $returnData['payload']['callId'] = $data['RoomName'];
                $returnData['payload']['callType'] = $participantId['1'];
                if($callerUserData['profile_picture'] != ''){
                    $folderName = config('constants.user_image.upload_path');
                    $imgPath = getS3Url($folderName);
                    $returnData['payload']['calleePic'] = $imgPath.$callerUserData['profile_picture'];
                }else{
                    $returnData['payload']['calleePic'] = '';
                }
                if($callerUserData['device_type'] == 1){
                    $fcm_token = $callerUserData['voip_device_id'];
                    $extra = sendApnsPushNotification($fcm_token, $calleeUserData['full_name'].' has left.' , $returnData,$callerUserData['device_type']);
                }else{
                    $fcm_token = $callerUserData['device_id'];
                    $extra = sendFcmPushNotification($fcm_token, $calleeUserData['full_name'].' has left.' , $returnData,$callerUserData['device_type']);
                }
            }
        }else if($participantId['0'] == 'G'){
            // Caller  Detail
            $participantIds = getParticipantConversationList($participantId['2']);
            // prd($participantIds);
            $groupDetail = getConversationDetailTwillio($participantId['2']);
        
            foreach($participantIds as $key => $value){
                // if($key != 'I' && $key != 'G')
                {
                    // Callee Detail 
                    $calleeUserId = getUserIdFromTwillioIdentity($value);
                    // echo $calleeUserId;
                    // exit;
                    $calleeUserData = getProfileData($calleeUserId);
                    // prd($calleeUserData);
                    $returnData['payload']['calleeName'] = $calleeUserData['full_name'];
                    if ($data['StatusCallbackEvent'] == "participant-connected" && (int)$data['SequenceNumber'] == 1) {
                        // echo $data['StatusCallbackEvent'];exit;
                        $returnData['payload']['type'] = 1;
                        $returnData['payload']['caller_userId'] = $participantId['1'];
                        $returnData['payload']['callee_userId'] = $value;
                        $returnData['payload']['callBackEvent'] = $data['ParticipantStatus'];
                        $returnData['payload']['callBackEventStatus'] = $data['StatusCallbackEvent'];
                        $returnData['payload']['callId'] = $data['RoomName'];
                        $returnData['payload']['callType'] = $participantId['1'];
                        $returnData['payload']['callerPic'] = (isset($groupDetail['profile_picture']) ? $groupDetail['profile_picture'] : '');
                        $groupName = (isset($groupDetail['name']) ? $groupDetail['name'] : 'Group');
                        $returnData['payload']['callerName'] = $groupName;
                        // echo $calleeUserData['device_type']; exit;
                        if($calleeUserData['device_type'] == 1){
                            $fcm_token = $calleeUserData['voip_device_id'];
                            //echo $fcm_token;exit;
                            $extra = sendApnsPushNotification($fcm_token, $groupName.' called you.' , $returnData,$calleeUserData['device_type']);
                        }else{
                            $fcm_token = $calleeUserData['device_id'];
                            $extra = sendFcmPushNotification($fcm_token, $groupName.' called you.' , $returnData,$calleeUserData['device_type']);
                        }

                    } elseif ($data['StatusCallbackEvent'] == "participant-disconnected") {
                        $returnData['payload']['type'] = 2;
                        $returnData['payload']['caller_userId'] = $participantId['1'];
                        $returnData['payload']['callee_userId'] = $value;
                        $returnData['payload']['callBackEvent'] = $data['ParticipantStatus'];
                        $returnData['payload']['callBackEventStatus'] = $data['StatusCallbackEvent'];
                        $returnData['payload']['callId'] = $data['RoomName'];
                        $returnData['payload']['callType'] = $participantId['1'];
                        $returnData['payload']['callee_name'] = $calleeUserData['full_name'];
                        /*****Get Owner Detail  */
                        $callerUserId = getUserIdFromTwillioIdentity($groupDetail['owner_id']);
                        $callerUserData = getProfileData($calleeUserId);
                        /*****Get Owner Detail  */
                        if($callerUserData['device_type'] == 1)
                        {
                            $fcm_token = $callerUserData['voip_device_id'];
                            $extra = sendApnsPushNotification($fcm_token, $calleeUserData['full_name'].' has left.' , $returnData,$callerUserData['device_type']);
                        }else
                        { 
                            $fcm_token = $callerUserData['device_id'];
                            $extra = sendFcmPushNotification($fcm_token, $calleeUserData['full_name'].' has left.' , $returnData,$callerUserData['device_type']);
                        }
                    }
                }
            }
        }
        //  $EmailBodyArr = [
		// 	'{name}' => 'Ashok',
		// 	'{data}' => json_encode($data)
		// ];
		//  $message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br><br>{data}.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		// $templateData = ['subject' => 'ISIT Twillio Callback','title'=>'ISIT Twillio Callback'];
		// $data = sendEmail($message,$templateData,$EmailBodyArr,'Ashok','twillio@yopmail.com');
        // UserWelcomeEmailJob::dispatch($data); 
        $response = [];
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : post
     * @api : send-notification-test
     * @summary : Api to call back from twillio
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 25 April 2022
     */
    public function sendNotificationTest(Request $request){
        getAvailableEvent('19800',3);
        echo $token;exit;
        // echo $notificationMessage;exit;
        // getConversationDetailTwillio('CHa15fbe9b2b754b59b93bee2770ca40d0');
        // $arr = getParticipantConversationList('CHa15fbe9b2b754b59b93bee2770ca40d0');
        // prd($arr);
        // $returnData = Array();
        $returnData = Array();
        
        return sendApnsPushNotification($data['device_id'],'Test Notification',['payload'=>['type'=>1]]);
        // return sendFcmPushNotification($data['device_id'],'Test Notification',['payload'=>['type'=>1]]);

    }
    /**
     * @header : Accept: application/json
     * @type : get
     * @api : checkAuthentication
     * @summary : Check authentication of user
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 29 April 2022
     */
    public function checkAuthentication(Request $request){
    	return getResponse(657,200);
    }
    /**
     * @header : Accept: application/json
     * @type : post
     * @api : deviceTokenUpdate
     * @summary : To update device id and type
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 29 April 2022
     */
    public function deviceTokenUpdate(Request $request){
        $response = [];
        // echo $request->device_token;exit;
        if(empty($request->device_type)){ throw new \Exception(__('api-messages.6000')); }
        try{ 
            User::where('device_id', '=', $request->device_token)->update(['device_id' => '']);
            User::where('voip_device_id', '=', $request->device_token_voip)->update(['voip_device_id' => '']);
            $user = Auth::guard('api')->user();
            $user = User::select(['id','device_type','device_id'])->whereRaw('id='.$user->id)->first();
            // echo $user->id;exit;
            if(!empty($request->device_token)){
                $user->device_id  = $request->device_token;
            }else if(!empty($request->device_token_voip)){
                $user->voip_device_id  = $request->device_token_voip;
            }
            $user->device_type  = $request->device_type;
            $user->save();
            return getResponse(105,200,$response);
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : notification-list
     * @summary : Api for follower list
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Jan 2022
     */
    public function getUserNotificationList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserNotification::with([
                'getSender'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
                'getNotification'=>function($query) use($user,$search_keyword){
                    $query->select(['notifications.id','notifications.message']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
            ])
            ->select('users_notifications.id','users_notifications.is_read','users_notifications.sender_id','users_notifications.receiver_id','users_notifications.receiver_id','users_notifications.other_id','users_notifications.notification_type','users_notifications.created_at','users_notifications.other_data')
            ->where(function($query) use($userId){
                $query->where('users_notifications.receiver_id', $userId);
                // $query->whereRaw('users_notifications.deleted_at IS NULL');
            })
            ->whereHas('getSender', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($userSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                // prd($value->getSender->first_name);
                if($value->notification_type == 17){
                    if(strlen($value->other_data) != 0){
                        $otherArr = json_decode($value->other_data,true);
                        if(!empty($otherArr)){
                            $data['name'] = $otherArr['name'];
                        }
                    }
                }
                $replace = [
                    "username:"=>(isset($value->getSender->first_name)) ? trim($value->getSender->first_name.' '.$value->getSender->last_name) : '',
                    "eventname:"=>(isset($data['eventname']) ? $data['eventname'] : ''),
                    "name:"=>(isset($data['name']) ? $data['name'] : ''),
                ];
                $notificationMessage = $value->getNotification->message;
                $message = strReplaceDynamic($replace,$notificationMessage);
                $arr['id'] = $value->id;
                $arr['is_read'] = $value->is_read;
                $arr['message'] = $message;
                $arr['notification_type'] = $value->notification_type;
                $arr['created_at'] = $value->created_at;
                $arr['other_id'] = $value->other_id;
                $arr['userData'] = $value->getSender;
                $notification_type = $value->notification_type;
                if($value->other_id != 0 && ($notification_type == 1 || $notification_type == 2 || $notification_type == 4 || $notification_type == 8 || $notification_type == 3 || $notification_type == 11 || $notification_type == 18 || $notification_type == 19)){
                    // $arr['postData'] = [];
                    $arr['postData'] = getPostSingleMedia($value->other_id);
                    if(empty($arr['postData'])){
                        unset($arr['postData']);
                    }
                    $arr['post_id'] = $arr['other_id'];
                    unset($arr['other_id']);
                }else if($notification_type == 17 || $notification_type == 21){
                    if($notification_type == 21){
                        $arr['message'] = $value->other_data; 
                    }
                    $arr['other_data'] = $value->other_data;
                }
                return $arr;
                // return $value['post_count'];
            });
            // prd($NewUsers->toArray());
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            UserNotification::where('users_notifications.receiver_id', '=', $userId)->update(['is_read' => 1]);
            $response['notifications'] = $NewUsers->toArray();
            $response['paging'] = [
                'total_item'=>$users->total(),
                'item_per_page'=>$users->perPage(),
                'current_page'=>$users->currentPage(),
                'last_page'=>$users->lastPage(),
                'prev'=>$users->previousPageUrl(),
                'next'=>$users->nextPageUrl(),
                'first'=>$users->url(1),
                'last'=>$users->url($users->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : getNotificationCount
     * @summary : Api for get notification unread count of user
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 31 March 2022
     */
    public function getNotificationCount(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $response['unreadNotificationCount'] = getUserNotificationUnreadCount($user->id);
            $response['is_username_set'] = (($user!= NULL && getUsername($user->id) != NULL) ? 1 : 0);
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Accept: application/json
     * @type : post
     * @api : send-busy-notification
     * @summary : Send busy notification to declined user
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 30 May 2022
     */
    public function sendBusyNotification(Request $request){
        $response = ['status'=>1];
        // echo $request->device_token;exit;
        if(empty($request->callId) && empty($request->userId)){ throw new \Exception(__('api-messages.6000')); }
        try{ 
            $participantId = explode('~', $request->callId);
            $returnData = Array();
            // For indivisual Call
            if($participantId['0'] == 'I'){
                // Callee Detail 
                $calleeUserId = getUserIdFromTwillioIdentity($participantId['3']);
                $callerUserId = getUserIdFromTwillioIdentity($participantId['2']);
                if($calleeUserId == $request->userId){
                    $userData = getProfileData($callerUserId);
                    $declinedUserData = getProfileData($calleeUserId);
                }else if($callerUserId == $request->userId){
                    $userData = getProfileData($calleeUserId);
                    $declinedUserData = getProfileData($callerUserId);
                }
                // prd($userData);
                $returnData['payload']['callId'] = $request->callId;
                $returnData['payload']['callBackEvent'] = 'call_declined';
                $returnData['payload']['user_id'] = $userData['id'];
                $returnData['payload']['reason'] = (!empty($request->reason) ? $request->reason : '');
                if($userData['device_type'] == 1){
                    $fcm_token = $userData['voip_device_id'];
                    $extra = sendApnsPushNotification($fcm_token, $declinedUserData['full_name'].' is busy.' , $returnData,$userData['device_type']);
                }else
                {
                    $fcm_token = $userData['device_id'];
                    $extra = sendFcmPushNotification($fcm_token, $declinedUserData['full_name'].' is busy.' , $returnData,$userData['device_type']);
                }
            }
            return getResponse(103,200,$response);
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : public-visibility
     * @summary : Api for change public visibiliy og user
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 27 Dec 2021
     */
    public function deleteUser(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        // echo $user->id;exit;
        $userData = User::where([['id', $user->id]])->first();
        $twilio_s_id = $userData->twilio_s_id;
        $userData->status = 0;
        $userData->device_id  = '';
        $userData->voip_device_id  = '';
        $userData->twilio_s_id = '';
        $userData->deleted_at  = date('Y-m-d H:i:s');
        deleteUserTwillio($twilio_s_id,$userData->id);
        Auth::user()->token()->delete();
        $userData->save();
        $response = [];
        return getResponse(658,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : user-go-live
     * @summary : Api for user go live and join user who is live
     * @param : is_publisher
     * @return : json
     * @author Ashok Dadhich    
     * @created_at 27 Dec 2021
     */
    public function userGoLive(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        $postId = 0;
        $title = '';
        $RecToken = '';
        // echo $user->id;exit;
        if(empty($request->is_publisher) && empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
        // echo $user->id;exit;
        $isPublisher = $request->is_publisher;
        $userData = User::where([['id', $request->user_id]])->first();
        $eventName = $userData->first_name;
        if($isPublisher != 1 && $userData->live_post_id == NULL && $userData->is_live == 0){
            return getResponse(669,400);
        }
        $eventName = str_replace(' ','_',$userData->first_name);
        $fullName = trim($userData->first_name).' '.($userData->last_name);
        $eventId = $request->user_id;
        $userId = $request->user_id;
        $loggedUserId = $user->id;
        $channelName = $eventId.'_'.$eventName;
        // echo $channelName;exit;
        $token = getAgoraToken($eventName,$eventId,$loggedUserId,$isPublisher,3600);
        if($request->is_record == 1){
            $RecToken = getAgoraToken($eventName,$eventId,1,0,3600);
        }
        $userData->is_live = 1;
        if($isPublisher == 1){
            $postName = $title = $request->title; 
            $postId = createPost($eventId,$postName,'',5);
            $userData->live_post_id = $postId;
        }else{
            $postId = $userData->live_post_id;
            $title = getPostName($userData->live_post_id);
        }
        $userData->save();
        // echo $channelName;exit;
        $response = ['token'=>$token,'recToken'=>$RecToken,'channelName'=>$channelName,'postId'=>$postId,'title'=>($title == NULL ? "" : $title),'live_user_count'=>$userData->live_user_count];
        if($request->is_publisher == 1){
            // sendLiveNotificationToFollowers($fullName,$loggedUserId);
        }
        return getResponse(103,200,$response);
        
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : get
     * @api : user-stop-live
     * @summary : Api for user go live and join user who is live
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 27 Dec 2021
     */
    public function userStopLive(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        // echo $user->id;exit;
        $userData = User::where([['id', $user->id]])->first();
        $userData->is_live = 0;
        $userData->live_post_id = NULL;
        $userData->live_user_count = 0;
        $userData->save();
        $response = ['is_live'=>$userData->is_live];
        return getResponse(111,200,$response);
    }
    /**
     * @header : Accept: application/json
     * @type : post
     * @api : misscall-notification
     * @summary : Send misscall notification users
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 07 July 2022
     */
    public function misscallNotification(Request $request){
        $response = ['status'=>1];
        // echo $request->device_token;exit;
        if(empty($request->user_ids) && empty($request->name) && empty($request->other_data)){ throw new \Exception(__('api-messages.6000')); }
        try{
            $userArr = explode(',', $request->user_ids);
            foreach ($userArr as $key => $value) {
                $senderId = 1;
                $reciverId = getUserIdFromTwillioIdentity($value);
                $data = ['other_data'=>$request->other_data,'name'=>$request->name,'notification_type'=>17];
                sendNotificationWithData($senderId,$reciverId,17,$data);
            }
            return getResponse(103,200,$response);
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : post
     * @api : isUserLive
     * @summary : Api for get user is live or not
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 July 2022
     */
    public function isUserLive(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            $userData = User::where([['id', $request->user_id]])->first();
            $response = ['is_live'=>$userData->is_live];
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : update-user-live-count
     * @summary : Api for user go live and join user who is live
     * @param : type, is_add, id
     * @return : json
     * @author Ashok Dadhich    
     * @created_at 27 Dec 2021
     */
    public function updateUserLiveCount(Request $request){
        //prd($request->all());
        $user = Auth::guard('api')->user();
        $postId = 0;
        // echo $user->id;exit;
        if(empty($request->type) && empty($request->id)){ throw new \Exception(__('api-messages.6000')); }
        // echo $user->id;exit;
        if($request->type == 2){
            $userData = User::where([['id', $request->id]])->first();
            $newCount = 0;
            if($request->is_add == 1){
                $newCount = $userData->live_user_count+1;
            }else if($request->is_add == 2){
                if($userData->live_user_count != 0){
                    $newCount = $userData->live_user_count-1;
                }
            }else if($request->is_add == 3){
                $newCount = 0;
            }
            $userData->live_user_count = $newCount;
            $userData->save();   
        } else{
            $eventData = Event::where([['id', $request->id]])->first();
            $newCount = 0;
            if($request->is_add == 1){
                $newCount = $eventData->live_user_count+1;
            }else if($request->is_add == 2){
                if($eventData->live_user_count != 0){
                    $newCount = $eventData->live_user_count-1;
                }
            }else if($request->is_add == 3){
                $newCount = 0;
            }
            $eventData->live_user_count = $newCount;
            $eventData->save();
        }
        $response = ['newCount'=>$newCount];
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : start-agora-recodring
     * @summary : Api for start recording on Agora
     * @param : channelName, agoraToken, recordingUid
     * @return : json
     * @author Ashok Dadhich
     * @created_at 29 September 2022
     */
    public function startAgoraRecording(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            if(empty($request->channelName) && empty($request->agoraToken)){ throw new \Exception(__('api-messages.6000')); }
            $recordingUid = 1;
            $resourceId = agoraRecAcuireToken($request->channelName,$request->agoraToken,$recordingUid);
            $sid = agoraRecStart($request->channelName,$resourceId,$request->agoraToken,$recordingUid);
            $response = ['resourceId'=>$resourceId,'sid'=>$sid];
        }catch(\Exception $e){
            $this->optionalData['message'] = 'Error while recording. Please try again.';
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : start-agora-recodring
     * @summary : Api for start recording on Agora
     * @param : channelName, agoraToken, recordingUid
     * @return : json
     * @author Ashok Dadhich
     * @created_at 29 September 2022
     */
    public function stopAgoraRecording(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            if(empty($request->channelName) && empty($request->agoraToken) && empty($request->sid) && empty($request->resourceId) && empty($request->postId)){
                throw new \Exception(__('api-messages.6000')); 
            }
            $recordingUid = 1;
            /* $retCode = agoraRecQuery($request->sid,$request->resourceId);
            if($retCode == 200)
            { */
                $response = agoraRecStop($request->channelName,$recordingUid,$request->sid,$request->resourceId);
                
            /* }else{
                return getResponse(667,400);
            } */
        }catch(\Exception $e){
            $this->optionalData['message'] = 'Error while recording. Please try again.';
            // $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        uploadMediaPostFromS3($request->postId,$response);
        return getResponse(668,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : users-live-list
     * @summary : Api for get user live listing
     * @param : user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 27 September 2022
     */
    public function getUserLiveList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');        
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $page = $request->page;
        $paginate = 10;
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $liverUserListing = getUserFollowerAndFollowingList($request->user_id,1);
            $slice = array_slice($liverUserListing->toArray(), $paginate * ($page - 1), $paginate);
            $result = json_decode(json_encode($slice), true);
            // prd($liverUserListing);
            $response['users'] = $result;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : send-go-live-notifications
     * @summary : Send live notification to all user 
     * @param : is_publisher
     * @return : json
     * @author Ashok Dadhich    
     * @created_at 27 Oct 2022
     */
    public function sendGoLiveNotifications(Request $request){
    	//prd($request->all());
        $user = Auth::guard('api')->user();
        $postId = 0;
        $title = '';
        // echo $user->id;exit;
        if(empty($request->is_publisher) && empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
        // echo $user->id;exit;
        $isPublisher = $request->is_publisher;
        $userData = User::where([['id', $request->user_id]])->first();
        $eventName = $userData->first_name;
        $fullName = trim($userData->first_name).' '.($userData->last_name);
        $loggedUserId = $user->id;
        if($request->is_publisher == 1){
            sendLiveNotificationToFollowers($fullName,$loggedUserId);
        }
        $response = [];
        return getResponse(103,200,$response);
    }
    /**
     * @summary : Api for report and user to admin
     * @type : post
     * @param : user_id, description
     * @return : json
     * @author Ashok Dadhich
     * @created_at 17 Mar 2023
     */
    public function reportUser(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            $response['report_user_id'] = 1;
            $code = 676;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @summary : Api for get user contact detail 
     * @type : post
     * @param : user_id, contact_str
     * @return : json
     * @author Ashok Dadhich
     * @created_at 29 May 2023
     */
    public function getContactDetailUser(Request $request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->user_id) || empty($request->contact_str)){ throw new \Exception(__('api-messages.6000')); }
            $contactArr = json_decode($request->contact_str,true);
            $userId = $request->user_id;
            $blockUserIds = "''";
            $returnArr = Array();
            // prd($contactArr);exit;
            if(!empty($contactArr['contacts'])){
                foreach($contactArr['contacts'] as $key => $value){
                    // prd($value);
                    //Phone
                    $PhoneStr = (isset($value['phone_no']) ? $value['phone_no'] : '');
                    $phone = preg_replace("/[^\w ]+/", "", $PhoneStr);
                    $phone = preg_replace("/\s+/", "",trim($phone));
                    // echo "<br>".var_dump($phone);
                    //Mobile
                    $MobStr = (isset($value['mobile']) ? $value['mobile'] : '');
                    $mobile = preg_replace("/[^\w ]+/", "", $MobStr);
                    $mobile = preg_replace("/\s+/", "", trim($mobile));
                    $email = trim($value['email']);
                    if($email =='' && $phone == '' && $mobile == ''){
                        $returnArr['contacts'][] = ['oriObj'=>$value,'userDetails' => null];
                        continue;
                    }
                    // echo "Email -> ".$email.' -> '.$mobile.' => '.$phone; 
                    $users = User::select(['users.id','users.email','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.is_star','users.phone_number','users.country_code'])
                            // ->whereNotIn('users.id', [$blockUserIds])
                            ->where(function($query) use($userId,$blockUserIds,$phone,$email,$mobile){
                                $query->where('users.status', 1);
                                // $query->where('users.is_public_visibility', 1);
                                $query->whereRaw('users.id != '.$userId.' AND users.id!=1');
                                $query->whereRaw('users.deleted_at IS NULL AND users.id NOT IN ('.$blockUserIds.')');
                                // if($email!='' || $phone !='' || $mobile!='')
                                {
                                    $query->where(function($query) use($email,$phone,$mobile){
                                            if($email!=''){
                                                $query->orWhereRaw('LOWER(users.email) like "%'.strtolower($email).'%" AND LOWER(users.email) !=""');
                                            }
                                            if($phone!=''){
                                                $query->orWhereRaw('CONCAT(users.country_code, "",users.phone_number) = "'.($phone).'" AND CONCAT(users.country_code, "",users.phone_number)!=""');
                                                $query->orWhereRaw('users.phone_number = "'.($phone).'" AND users.phone_number!=""');
                                            }
                                            if($mobile!=''){
                                                $query->orWhereRaw('CONCAT(users.country_code, "",users.phone_number) = "'.($mobile).'" AND CONCAT(users.country_code, "",users.phone_number)!=""');
                                                $query->orWhereRaw('users.phone_number = "'.($mobile).'" AND users.phone_number!=""');
                                            }
                                            
                                            // $query->orWhereRaw('LOWER(users.email) like "%'.strtolower($search_keyword).'%"');
                                        });
                                }
                            })
                            ->get()
                            ->toArray();
                        if(!empty($users)){
                            $returnArr['contacts'][] = ['oriObj'=>$value,'userDetails' => $users[0]];
                        }else{
                            $returnArr['contacts'][] = ['oriObj'=>$value,'userDetails' => null];
                        }
                }
            }
            $response = $returnArr;
            $userFolderName = config('constants.user_image.upload_path');
            $response['path']['user_media'] = getS3Url($userFolderName);
            $code = 103;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Accept: application/json, Api-Key:
     * @type : get
     * @api : getHobbies
     * @summary : Api for get hobbies
     * @param : 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 22 Aug 2023
     */
    public function getHobbies(Request $request){
        $response = [];
        $user = Auth::guard('api')->user();
        try{
            $search_keyword = '';
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $hobbies = Hobby::select(['name','id'])->whereRaw('status = 1 AND LOWER(name) like "%'.strtolower($search_keyword).'%"')->orderBy('name','asc')->get();
            if(empty($hobbies)){ throw new \Exception(__('api-messages.615')); }
            $response = $hobbies;
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
}
