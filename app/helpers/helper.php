<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
//use PDF;
// use GuzzleHttp\Client;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\Post;
use App\Models\BlockUser;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Event;
use App\Models\Category;
use App\Models\Country;
use App\Models\UserNotification;
use App\Models\Notification;
use App\Models\UsersTagged;
use App\Models\GlobalSetting;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\UserStory;
use App\Models\PostMedia;
use App\Models\UserHobby;
use App\Models\Hobby;

use App\Jobs\UserWelcomeEmailJob;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Jwt\Grants\VideoGrant;

use Twilio\Exceptions\TwilioException;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;

function get_user_by_token($token){
    $records =  DB::table('password_resets')->get();

    foreach ($records as $record) {

        if (Hash::check($token, $record->token) ) {
           return $record->email;
        }
    }
}


function p($arr,$exit = 1){
	if($exit == 1){
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		exit;
	}else{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}
}

function pr($data){
	echo "<pre>";
	print_R($data);
}

function prd($data){
	echo "<pre>";
	print_R($data);
	exit;
}

function getResponse($code,$httpCode,$data=[],$optionalData=[]){

	//$displayImagePath = config('constants.user_image.display_path');
	$msg = __('api-messages.'.$code);

	if(isset($optionalData['message']) || !empty($optionalData['message'])){
		$response['message'] = $optionalData['message'];
	}else{
		$response['message'] = $msg;
	}
	$response['code'] = $code;
	$response['data'] = $data;
	return Response::json($response, $httpCode);
}

function get_encrypted_value($key, $encrypt = false){
	$encrypted_key = null;
	if (!empty($key))
	{
		if ($encrypt == true)
		{
			$key = Crypt::encrypt($key);
		}
		$encrypted_key = $key;
	}
	return $encrypted_key;
}

function sendEmail($message,$templateData,$keywordArr,$receiverName=null,$receiverEmail=null){
	// $emailTemplateObj = EmailTemplate::where('key',$templateKey)->first();
	//remove table tag if exist
	$content = preg_replace('/\<[\/]?(table)([^\>]*)\>/i', '', $message);
	//$content = '';
	if(count($keywordArr)){
		$content = str_replace(array_keys($keywordArr),$keywordArr,$content);
	}

	if(env('IS_STAGE_SERVER')){
		$receiverEmail = env('STAGE_TEST_EMAIL');
	}

	$emailData = [];
	$emailData['title'] = $templateData['title'];
	$emailData['subject'] = $templateData['subject'];
	$emailData['sender'] = env('MAIL_FROM_ADDRESS');
	$emailData['receiver'] = $receiverEmail;
	$emailData['sender_name'] = config('app.name', 'ISIT');
	$emailData['receiver_name'] = $receiverName;
	$emailData['body'] = $content;
	$emailData['image_path'] = url('/');
	//$emailData['from_email'] = $emailTemplateObj->email_from;
	return $emailData;
}

function generate_string($strength = 16) {
	$input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

/*----SEND VERIFY CODE TO USER----*/
function emailVerifyMail($email, $otp){
	$code = '';
	$user = User::where('email', $email)->first();
	if(!empty($user)){
		// $user->is_verified = 0;
		$user->verifiy_token = $otp;
		$user->save();
		$EmailBodyArr = [
			'{name}' => $user->first_name.' '.$user->last_name,
			'{otp}' => $user->verifiy_token
		];
		$message = 'Hi {name} <br><br>Please verify your email address <br>Enter this Verification code to verify your email address <br><br><b>{otp}</b> <br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'ISIT User Verification','title'=>'ISIT User Verification'];
		$data = sendEmail($message,$templateData,$EmailBodyArr,$user->name,$user->email);
		UserWelcomeEmailJob::dispatch($data);
	}
	return $code;
}
/*----SEND VERIFY CODE TO USER----*/

/*----SEND CHANGE PASSWORD LINK TO USER----*/
function sendEmailVerificationCode($email){
	$code = '';
	$user = User::where('email', $email)->first();
	if(!empty($user)){
		$link = url('password/reset/'.generate_string(50).'?email='.$email);
		$EmailBodyArr = [
			'{name}' => $user->first_name.' '.$user->last_name,
			'{link}' => '<a style="pointer:cursor;" href="'.$link.'"><button type="submit" class="btn btn-primary butnSubmit">Reset Password</button></a>'
		];
		$message = 'Hello,  {name}<br>';
		$message ='<br>You are recieving this email because we recieved a password reset request for your account<br><br>';
		$message .='{link}';
		$message.= '<br><br>Thanks for joining us.<br>';
		$message.= '<br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'Reset Password Notification','title'=>'Reset Password Notification'];
		$data = sendEmail($message,$templateData,$EmailBodyArr,$user->name,$user->email);
		UserWelcomeEmailJob::dispatch($data);
	}
	return $code;
}
/*----SEND CHANGE PASSWORD LINK TO USER----*/

function sendEmailVerificationMail($email){
	$code = '';
	$user = User::select(['id','email','is_verified','first_name','last_name'])->whereRaw('email="'.$email.'"')->first();
	if(empty($user)){
		$code = 607;
	}else if($user->is_verified == 1){
		$code = 609;
	}else{
		$user->is_verified = 0;
		$user->verifiy_token = generate_string(10);
		$user->save();
		$link = url('verfiy-mail', $parameters = array($user->verifiy_token), $secure = null);
		$EmailBodyArr = [
			'{name}' => $user->first_name.' '.$user->last_name,
			'{link}' => '<a style="color:blue" href="'.$link.'">LINK</a>'
		];
		$message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br>Please click on this {link} to verify your email address.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'ISIT User Verification','title'=>'ISIT User Verification'];
		$data = sendEmail($message,$templateData,$EmailBodyArr,$user->name,$user->email);
	 
		//text message code start


		UserWelcomeEmailJob::dispatch($data);
	}
	return $code;
}



// function sendEmailVerificationMailFront($email){
// 	$code = '';
// 	$user = User::select(['id','email','is_verified','first_name','last_name'])->whereRaw('email="'.$email.'"')->first();
// 	if(empty($user)){
// 		$code = 607;
// 	}else if($user->is_verified == 1){
// 		$code = 609;
// 	}else{
// 		$user->is_verified = 0;
// 		$user->verifiy_token = generate_string(10);
// 		$user->save();
// 		$link = url('front-panel/verfiy-mail', $parameters = array($user->verifiy_token), $secure = null);
// 		$EmailBodyArr = [
// 			'{name}' => $user->first_name.' '.$user->last_name,
// 			'{link}' => '<a style="color:blue" href="'.$link.'">LINK</a>'
// 		];
// 		$message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br>Please click on this {link} to verify your email address.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
// 		$templateData = ['subject' => 'ISIT User Verification','title'=>'ISIT User Verification'];
// 		$data = sendEmail($message,$templateData,$EmailBodyArr,$user->name,$user->email);
// 		// prd($data);
// 		UserWelcomeEmailJob::dispatch($data);
// 	}
// 	return $code;
// }


function getProfileData($user_id,$isOtherUser = 0){
	// ->toArray()
	$user = User::select(['*'])->whereRaw('id='.$user_id)->first();
	$userArr = Array();
	$userArr['id'] = $user->id;
	$userArr['first_name'] = $user->first_name;
	$userArr['last_name'] = $user->last_name;
	$userArr['full_name'] = $userArr['first_name'].' '.$userArr['last_name'];
	$userArr['email'] = $user->email;
	$userArr['is_public_visibility'] = $user->is_public_visibility;
	$userArr['phone_number'] = ($user->phone_number == NULL ? '' : $user->phone_number);
	$userArr['twilio_s_id'] = ($user->twilio_s_id == NULL ? '' : $user->twilio_s_id);
	$userArr['device_id'] = ($user->device_id == NULL ? '' : $user->device_id);
	$userArr['voip_device_id'] = ($user->voip_device_id == NULL ? '' : $user->voip_device_id);
	$userArr['device_type'] = ($user->device_type == NULL ? 0 : (int)$user->device_type);
	$userArr['country_code'] = ($user->country_code == NULL ? '' : $user->country_code);
	$userArr['profile_picture'] = ($user->profile_picture == NULL ? '' : $user->profile_picture);
	$userArr['bio_text'] = ($user->bio_text == NULL ? '' : $user->bio_text);
	$userArr['follower_count'] = ($user->follower_count == NULL ? 0 : $user->follower_count);
	$userArr['following_count'] = ($user->following_count == NULL ? 0 : $user->following_count);
	$userArr['is_follow'] = ($user->is_follow == NULL ? 0 : $user->is_follow);
	$userArr['is_block'] = ($user->is_block == NULL ? 0 : $user->is_block);
	$userArr['is_block_by_you'] = ($user->is_block_by_you == NULL ? 0 : $user->is_block_by_you);
	$userArr['twillio_identity'] = ($user->twillio_identity == NULL ? 0 : $user->twillio_identity);
	$userArr['is_live'] = ($user->is_live == NULL ? 0 : $user->is_live);
	$userArr['username'] = ($user->username == NULL ? '' : $user->username);
	$userArr['is_star'] = ($user->is_star == NULL ? 0 : $user->is_star);
	$userArr['dob'] = ($user->dob == NULL ? '' : $user->dob);
	$userArr['gender'] = ($user->gender == NULL ? '' : $user->gender);
	$userArr['hobby'] = getHobbiesDetailUser($user_id);
	getHobbiesDetailUser($user_id);
	if($isOtherUser == 0){
		$userArr['last_login'] = $user->last_login;
		$userArr['status'] = $user->status;
		$userArr['device_type'] = $user->device_type;
		$userArr['device_id'] = $user->device_id;
		$userArr['is_public_visibility'] = $user->is_public_visibility;
	}
	return $userArr;
}
function sendEmailPasswordResetToken($email){
	$code = '';
	$user = User::select(['id','email','password_reset_token','first_name','last_name','status','deleted_at'])->whereRaw('users.email="'.$email.'" AND users.deleted_at IS NULL')->first();
	if(empty($user)){
		$code = 607;
	}else if($user->status == 0){
		$code = 603;
	}else{
		$passwordResetToken = substr(str_shuffle("0123456789"), 0, 4);
		$user->password_reset_token = $passwordResetToken;
		$user->save();
		$EmailBodyArr = [
			'{name}' => $user->first_name.' '.$user->last_name,
			'{code}' => $passwordResetToken
		];
		$message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br>Please check below code for Password Reset.<br><div style="margin-top:10px;"><span style="font-size:30px">{code}</span></div><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'ISIT Password Reset','title'=>'ISIT Password Reset'];
		$data = sendEmail($message,$templateData,$EmailBodyArr,$user->name,$user->email);
		// prd($data);
		UserWelcomeEmailJob::dispatch($data);
	}
	return $code;
}
function getS3Url($folderName){
	$S3URL = env('AWS_URL');
	$url = $S3URL.$folderName;
	return $url;
}
function get_decrypted_value($key, $decrypt = false)
{
	$decrypted_key = null;
	try{
		if (!empty($key))
		{
			if ($decrypt == true)
			{
				$key = Crypt::decrypt($key);
			}
			$decrypted_key = $key;
		}
	}
	catch(Exception $e){
		abort(404);
	}
	return $decrypted_key;
}
function AdminSendEmailPasswordResetToken($link,$firstName,$email){
	$code = '';
	$EmailBodyArr = [
		'{name}' => $firstName,
		'{link}' => $link
	];
	$message = 'Hi {name} <br><br>Please Click below button for Password Reset.<br><br><a href="{link}" style="-webkit-box-sizing: border-box ; -moz-box-sizing: border-box ; box-sizing: border-box ; color: #fbfbfc ; text-decoration: none ; font-family: &quot;montserrat&quot; , sans-serif ; font-size: 15px ; font-weight: 500 ; background: #E85922 ; border-radius: 5px ; padding: 15px 20px ; min-width: 218px ; display: inline-block ; letter-spacing: 1px" target="_other" rel="nofollow">Reset your password</a><br><br>Yours Truly<br>Team ISIT';
	$templateData = ['subject' => 'ISIT Admin Password Reset','title'=>'ISIT Admin Password Reset'];
	$data = sendEmail($message,$templateData,$EmailBodyArr,$firstName,$email);
	// prd($data);
	UserWelcomeEmailJob::dispatch($data);
	return $code;
}
function getUserPublicVisibilityStatus($user_id){
	$user = User::select(['is_public_visibility'])->whereRaw('id='.$user_id)->first();
	if(!empty($user)){
		return $user->is_public_visibility;
	}else{
		return '';
	}
}
function acceptAllFollowingRequest($user_id){
	$followRequest = UserFollow::where('follower_id', '=', $user_id)->whereRaw('status = 2')->get()->toArray();
	// prd($followRequest);
	foreach($followRequest as $key => $value){
		// $senderId,$reciverId,$notiType,$data
		$data = ['user_id'=>$value['user_id']];
		sendNotificationWithData($user_id,$value['user_id'],9,$data);
	}
	return UserFollow::where('follower_id', '=', $user_id)->update(['status' => 1]);
}
function isPostDeleted($postId){
	$post = Post::select(['deleted_at'])->whereRaw('id='.$postId)->first();
	if(empty($post))
		return 0;
	if($post->deleted_at != NULL){
		return 1; 
	}else{
		return 0;
	}
}
function getDailyDevotionalPost($numberOfPost=1){
	$newPost = Array();
	$posts = Post::with([
		'Country'=>function($query){
			$query->select(['countries.id','countries.name']);
		},
		'Category'=>function($query){
			$query->select(['categories.id','categories.name']);
		},
		'getMedia'=>function($query){
			$query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
		},
		'getUser'=>function($query){
			$query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
		}
	])
	->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at','posts.created_at as createdAt','posts.no_of_view')
	// ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(posts.created_at, "Y-m-d h:is")) as created_at_timestamp'))
	->where(function($query){
		$query->where('posts.post_type', 3);
		$query->where('posts.admin_status', 1);
		$query->whereRaw('posts.deleted_at IS NULL');
		// $query->whereRaw('DATE(posts.created_at) = " '.date('Y-m-d').'"');
	})
	->whereHas('getUser', function ($query){
		$query->whereRaw('users.deleted_at IS NULL');
	})
	->orderByRaw('posts.id desc')
	->offset(0)
	->limit($numberOfPost)
	->get();
	// ->toArray()
	// prd($posts->count());exit;
	if($posts->count()!=0){
		if($posts->count() == 1){
			return $posts->first();
		}else{
			return $posts;
		}
	}else{
		return $newPost;
	}
}
function sort_array_of_array(&$array, $subfield){
	$sortarray = Array();
	foreach ($array as $key => $row){
		$sortarray[$key] = $row[$subfield];
	}
	if($subfield=='created_at' || $subfield=='lastCreatedAt' || $subfield=='is_preview_media'){
		@array_multisort($sortarray, SORT_DESC, $array);
	}
}
function getBlockUserIds($userId){
	$blockUser = new BlockUser();
	$blockUserArr = $blockUser->select(DB::raw('group_concat(block_user_id) as ids'))
                               ->where('block_users.user_id', $userId)
                               ->groupBy('block_users.user_id')
                               ->get()
							   ->toArray();
	// prd($blockUserArr);
	if(!empty($blockUserArr) && isset($blockUserArr[0]['ids'])){
		return $blockUserArr[0]['ids'];
	}else{
		return '';
	}						
}
function getBlockByUser($loggedInUserId, $userId){
	$userDeleted = User::select(['*'])->whereRaw('id='.$userId.' AND deleted_at IS NOT NULL')->exists();
	if($userDeleted == true){
		return $userDeleted;
	}
	$blockUser = new BlockUser();
	$blockUserArr = $blockUser->select('id')
                               ->where('block_users.user_id', $loggedInUserId)
							   ->where('block_users.block_user_id', $userId)
							   ->exists();
	return $blockUserArr;
}
function createThumbImage(){
	
	prd($img);exit; 
}
function removeFollowUsers($userId, $blockUserId){
	$followUser = Array();
	$followUser = UserFollow::where(['user_id'=>$userId,'follower_id'=>$blockUserId])->first();
	if(!empty($followUser)){
		$followUser->delete();
	}
	$followUser = Array();
	$followUser = UserFollow::where(['user_id'=>$blockUserId,'follower_id'=>$userId])->first();
	if(!empty($followUser)){
		$followUser->delete();
	}
	return 1;
}
function getPostUserId($postId){
	$post = Post::select(['user_id'])->whereRaw('id='.$postId)->first();
	return $post->user_id;
}
function isProductDeleted($productId){
	$product = Product::select(['deleted_at'])->whereRaw('id='.$productId)->first();
	if(empty($product))
		return 0;
	if($product->deleted_at != NULL){
		return 1; 
	}else{
		return 0;
	}
}
function isEventDeleted($eventId){
	$event = Event::select(['deleted_at'])->whereRaw('id='.$eventId)->first();
	if(empty($event))
		return 0;
	if($event->deleted_at != NULL){
		return 1; 
	}else{
		return 0;
	}
}
function isPostBlockByAdmin($postId){
	$post = Post::select(['admin_status'])->whereRaw('id='.$postId)->first();
	if(empty($post))
		return 0;
	if($post->admin_status == 1){
		return 1; 
	}else{
		return 0;
	}
}
function deleteMediaProduct($ids){
	if(is_array($ids) && !empty($ids)){
		$destinationPath = config('constants.product_media.upload_path');
		foreach($ids as $key => $value){
			$productMedia = ProductMedia::findOrFail($value);
			if(!empty($productMedia)){
				if(isset($productMedia->file_name) && $productMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$productMedia->file_name)){
					Storage::disk('s3')->delete($destinationPath.'/'.$productMedia->file_name);
				}
				$productMedia->delete();
			}
		}	
	}
	return 1;
}
function createUserTwillio($userId,$fullName,$profileImageUrl){
		$sid = env("TWILIO_ACCOUNT_SID");
		$token = env("TWILIO_ACCOUNT_TOKEN");
		$twilio = new Client($sid, $token);
		$env = env("APP_ENV");
		$identity = $env.'_'.$userId;
		// echo $identity;exit;
		$json = json_encode(['USER_ID' => $userId,'FULL_NAME'=>$fullName,'USER_PROFILE_PIC'=>$profileImageUrl]);
		// prd($json);
		try {
				$user = $twilio->conversations->v1->users->create($identity);
		}
		catch (\Twilio\Exceptions\RestException $e) {
			dd($e->getMessage());
		}
		updateUserTwillio($user->sid,$userId,$fullName,$profileImageUrl);
		return $user->sid;
}

function getAccessToken($userId,$deviceType=1){
	$twilioAccountSid = env("TWILIO_ACCOUNT_SID");
	$twilioApiKey = env("TWILIO_API_KEY");
	$twilioApiSecret = env("TWILIO_API_SECRET");
	$serviceSid = env("TWILIO_SERVICE_ID");
	$env = env("APP_ENV");
	$identity = $env.'_'.$userId;
	// echo $identity;exit;
	$token = new AccessToken(
		$twilioAccountSid,
		$twilioApiKey,
		$twilioApiSecret,
		36000,
		$identity
	);
	
	// $user->twilio_s_id = updateUserTwillio($user->twilio_s_id,$user->id,$fullName,$profileImageUrl);
	// Create Chat grant
	$chatGrant = new ChatGrant();
	$chatGrant->setServiceSid($serviceSid);
	//IOS
	if($deviceType == 1){
		if($env == 'dev'){
			$chatGrant->setPushCredentialSid(env('TWILIO_IOS_PUSH_CREDENTIAL_DEV'));
		}else{
			$chatGrant->setPushCredentialSid(env('TWILIO_IOS_PUSH_CREDENTIAL_LIVE'));
		}
	}else{
	// Android
		$chatGrant->setPushCredentialSid(env('TWILIO_ANDROID_PUSH_CREDENTIAL_LIVE'));
	}
	// Add grant to token
	$token->addGrant($chatGrant);
	$videoGrant = new VideoGrant();
    $token->addGrant($videoGrant);

	// prd($token);exit;
	if(!empty($token))
		return $token->toJWT();
	else
		return '';
}



function updateUserTwillio($userSId,$userId,$fullName,$profileImageUrl){
	$sid = env("TWILIO_ACCOUNT_SID");
	$token = env("TWILIO_ACCOUNT_TOKEN");
	$twilio = new Client($sid, $token);
	$env = env("APP_ENV");
	$identity = $env.'_'.$userId;
	if(!empty($userSId)){
		$json = ['USER_ID' => $userId,'FULL_NAME'=>$fullName,'USER_PROFILE_PIC'=>$profileImageUrl];
		$user = $twilio->conversations->v1->users($userSId)
			->update([
						"friendlyName" => $fullName,
						"identity" => $identity,
						"attributes" => json_encode($json),
					]
			);
	}
	return $userSId;
}
//this function convert string to UTC time zone
function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s'){
        
    $new_str = new DateTime($str, new DateTimeZone(  $userTimezone  ) );
    $new_str->setTimeZone(new DateTimeZone('UTC'));
    return $new_str->format( $format);
}

//this function converts string from UTC time zone to current user timezone
function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:i:s'){
    if(empty($str)){
        return '';
    }
        
    $new_str = new DateTime($str, new DateTimeZone('UTC') );
    $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
    return $new_str->format( $format);
}



function combineArrayKey($array,$columnName,&$arr){
	$data = array_column($array, $columnName);
	$arr[$columnName]['ios'] = $data[0];
	$arr[$columnName]['android'] = $data[1];
	unset($data[0]);
	unset($data[1]);
	return $arr;
}

function postCategories(){
	// $categories = Category::where('status', 1)->get(); 
	$categories = Category::where(['status'=>1,'type'=>0])->get();
	// $categories = Category::select(['name','id'])->where(['status'=>1,'type'=>0])->orderBy('name','asc')->get();
           
	return $categories;
}

function postCountries(){
	$countries = Country::where('status', 1)->get();
	return $countries;
}


function postCategoriesAdd(){ 
	$categories = Category::where(['status'=>1,'type'=>3])->get();
	return $categories;
}

function postHelp(){
	$categories = Category::where(['status'=>1,'type'=>2])->get();
	return $categories;
}



function postCategoriesReport(){ 
	$categories = Category::where(['status'=>1,'type'=>1])->get();
	return $categories;
}


function convertIntTodate($int,$format = 'Y-m-d H:i:s')
{
	$date= date($format, $int);
	return $date;

}



function getCategoryNameById($id){
	$category = Category::find($id);
	$name ='';
	if(!empty($category))
		return $category->name;
	else
     	return '';
}


function getCountryNameById($id){
	$country = Country::where('id',$id)->where('status', 1)->first();
	$name ='';
	if(!empty($country))
		return $country->name;
	else
     	return '';
}
function sendFcmPushNotification($deviceIds,$message,$optionalData=[],$deviceType=1){
	// $deviceType=1 iOS
	// $deviceType=2 Android
	$url = env('FCM_URL');
	$api_key = env('FCM_SERVER_KEY');
	$title = env('APP_NAME');
	$notification = [];
	$data = [];
	if(!is_array($deviceIds)){ $deviceIds = array($deviceIds); }
	$data['payload'] = isset($optionalData['payload'])? $optionalData['payload']:[];
	$notification['title'] = $title;
	$data['title'] = $title;
	$notification['body'] = $message;
	$data['body'] = $message;
	$notification['sound'] = 'default';
	$notification['badge'] = 0;
	if($deviceType == 1){
		$fields = array (
			'registration_ids' => $deviceIds,
			'notification' => $notification,
			'priority'=>'high',
			'data' => $data,
			'mutable_content'=>true,
			'content_available'=>true
		);
	}else if($deviceType == 3){
		$fields = array (
			'registration_ids' => $deviceIds,
			'notification' => $notification,
			'priority'=>'high',
			'data' => $data,
			'mutable_content'=>true,
			'content_available'=>true
		);
	}
	else{
		$fields = array (
			'registration_ids' => $deviceIds,
			'priority'=>'high',
			'data' => $data,
			'mutable_content'=>true,
			'content_available'=>true
		);
	}

    //header includes Content type and api key
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );
    //prd($deviceIds);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    // echo $result;
    return $result;
}
function getUserIdFromTwillioIdentity($identity){
	$return = 0;
	if($identity!=''){
		$arr = explode('_',$identity);
		$return = $arr[1];
	}
	return $return;
}
function strReplaceDynamic(array $replace, $string) { 
	return str_replace(array_keys($replace), array_values($replace), $string);    
}
function getUserNotificationData($user_id){
	$user = User::select(['*'])->whereRaw('id='.$user_id)->first();
	$userArr = Array();
	$userArr['id'] = $user->id;
	$userArr['first_name'] = $user->first_name;
	$userArr['last_name'] = $user->last_name;
	$userArr['full_name'] = trim($userArr['first_name'].' '.$userArr['last_name']);
	$userArr['device_id'] = $user->device_id;
	$userArr['device_type'] = $user->device_type;
	return $userArr;
}
function sendNotificationWithData($senderId,$reciverId,$notiType,$data){
	$senderData = getUserNotificationData($senderId);
    $reciverData = getUserNotificationData($reciverId);
	$notificationMessage = getNotificationMessage($notiType);
	//prd($data);
	$replace = [
		"username:"=>$senderData['full_name'],
		"eventname:"=>(isset($data['eventname']) ? $data['eventname'] : ''),
		"name:"=>(isset($data['name']) ? $data['name'] : ''),
	];
	if($notiType != 21){
		$message = strReplaceDynamic($replace,$notificationMessage);
	}else{
		if(strlen($data['other_data'])>50)
            $message = 'Admin - '.substr($data['other_data'], 0,50).'...';
        else
			$message = 'Admin - '.$data['other_data'];	
	}
	// echo $message;exit;
	$optionalData = ['payload'=>$data];
	// prd($optionalData);
	insertUserNotification($senderId,$reciverId,$data,$notiType);
	if($reciverData['device_id'] != ''){
		return sendFcmPushNotification($reciverData['device_id'],$message,$optionalData,$reciverData['device_type']);
	}
}

function getEventUserId($eventId){
	$event = Event::select(['user_id'])->whereRaw('id='.$eventId)->first();
	return $event->user_id;
}
function insertUserNotification($sender_id,$receiver_id,$other_data,$notification_type){
	// prd($other_data);
	$other_id = 0;
	$otherData = '';
	$UserNotification = new UserNotification();
	$UserNotification['sender_id'] = $sender_id;
	$UserNotification['receiver_id'] = $receiver_id;
	if(isset($other_data['post_id']) && ($notification_type == 1 || $notification_type == 2 || $notification_type == 4 || $notification_type == 8 || $notification_type == 3 || $notification_type == 11)){
		$other_id = $other_data['post_id'];
	}else if(isset($other_data['post_id']) && $notification_type == 18 || $notification_type == 19){
		$other_id = $other_data['post_id'];
		$otherData = $other_data['comment_id'];
	}else if(isset($other_data['user_id']) && ($notification_type == 5 || $notification_type == 9)){
		$other_id = $other_data['user_id'];
	}else if(isset($other_data['event_id']) && ($notification_type == 6 || $notification_type == 7)){
		$other_id = $other_data['event_id'];
	}else if(isset($other_data['other_data']) && $notification_type == 17){
		$otherData = $other_data['other_data'];
	}else if(isset($other_data['other_data']) && $notification_type == 21){
		$otherData = $other_data['other_data'];
	}
	$UserNotification['other_id'] = $other_id;
	$UserNotification['other_data'] = $otherData;
	$UserNotification['notification_type'] = $notification_type;
	// prd($UserNotification);
	$UserNotification->save();
}
function getNotificationMessage($notiType){
	$message = '';
	$notification = Notification::select(['message'])->whereRaw('id='.$notiType)->first();
	if(!empty($notification)){
		$message = $notification->message;
	}
	return $message;
}
function getPostSingleMedia($post_id){
	$mediaData = [];
	$post = Post::findOrFail($post_id);
	// prd($post);
	if(!empty($post)){
		$getMedia = $post->getMedia()->get()->toArray();
		if(!empty($getMedia) && isset($getMedia[0]['file_name'])){
			if($getMedia[0]['media_type'] == 2){
				$mediaData['file_name'] = $getMedia[0]['thumb_image_name'];
			}else{
				$mediaData['file_name'] = $getMedia[0]['file_name'];
			}
			$mediaData['id'] = $post_id;
			$mediaData['media_type'] = $getMedia[0]['media_type'];
		}
	}
	return $mediaData;
}
function getUserNotificationUnreadCount($userId){
	$notificationCount = UserNotification::select(['message'])->whereRaw('users_notifications.receiver_id = '.$userId.' AND is_read = 0')->count();
	return $notificationCount;
}

function getFiletype($file)
{
		$mime = $file->getMimeType();
		if(strstr($mime, "video/")){
			return 2;
		}else if(strstr($mime, "image/")){
			return 1;
		}
		else{
			return 1;
		}
}


function getFileTypeUsingName($fileName)
{
	$info = pathinfo($fileName);
	$extension = $info['extension'];
	if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif') {
		return 1;
	}
	else if($extension == 'mp4' || $extension == 'ogg' || $extension == 'webm')
	{
		return 2;
	}
	else{
		return 1;
	}

}

function sendApnsPushNotification($deviceIds,$message,$optionalData=[]){
		
		$keyfile = app_path().'/AuthKey_SQR583S9R5.p8';  # <- Your AuthKey file
		$keyid = 'SQR583S9R5';                            # <- Your Key ID
		$teamid = 'J45J5KQJ59';                           # <- Your Team ID (see Developer Portal)
		
		/********** Live ************************/
		// Keep this api.push for Live and budble id -> com.huntleague
		$bundleid = 'com.isit.life.app.voip';
		$url = 'https://api.push.apple.com'; # <- production url, or use 
        /********** Live ************************/

        /********** Sand Box ************************/
        // Keep this sand box for QA and budble id -> com.huntleaguedemo
        // $url = 'https://api.sandbox.push.apple.com'; # <- development url, or use 
        // $bundleid = 'dev.isit.app.blitzinfosystem.voip';
        /********** Sand Box ************************/
 
        //print_r($optionalData) exit;
        $pload = isset($optionalData) ? $optionalData : [];
        $payload = array();
        $payload['aps'] = array('alert' => $message, 'sound' => 'default','data'=>$pload);
        $payload = json_encode($payload);
        // $payload['aps'] = array('noti_type' => $n_type,'alert' => $message, 'badge' => intval(1), 'sound' => 'default','pload'=>$pload, 'n_type' => $n_type  );
        // $payload = json_encode($payload);

        //print_r($payload); exit;

        $key = openssl_pkey_get_private('file://'.$keyfile);
        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];
        // $header_encoded = base64($header);
        // $claims_encoded = base64($claims);
        $header_encoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $claims_encoded = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');
        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);
        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }
        if(is_array($deviceIds)){
            foreach ($deviceIds as $k => $v) {
                $http2ch = curl_init();
                curl_setopt_array($http2ch, array(
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    CURLOPT_URL => "$url/3/device/$v",
                    CURLOPT_PORT => 443,
                    CURLOPT_HTTPHEADER => array(
                        "apns-topic: {$bundleid}",
                        "authorization: bearer $jwt"
                    ),
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HEADER => 1
                ));

                $result = curl_exec($http2ch);
                //print_r($deviceIds);
                if ($result === FALSE) {
                    echo "Error for given device : ".$v;
                    //$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                    //throw new Exception("Curl failed: ".curl_error($http2ch));
                }
            }
        }else{
            $http2ch = curl_init();
            curl_setopt_array($http2ch, array(
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_URL => "$url/3/device/$deviceIds",
                CURLOPT_PORT => 443,
                CURLOPT_HTTPHEADER => array(
                    "apns-topic: {$bundleid}",
                    "authorization: bearer $jwt"
                ),
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HEADER => 1
            ));
            $result = curl_exec($http2ch);
            // echo $result;exit;
            if ($result === FALSE) {
                echo "Error for one device : ".$deviceIds;
                //$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                //throw new Exception("Curl failed: ".curl_error($http2ch));
            }            
        }        
        return true;            
}

function time_elapsed_string($time_ago) {

    $time_ago =  strtotime($time_ago) ? strtotime($time_ago) : $time_ago;
    $time  = time() - $time_ago;
	$format='M d, Y';
		switch($time):
		// seconds
		case $time <= 60;
	    	return 'Just now';
		// minutes
		case $time == 60;
		   return 'Just now';
		case $time >= 60 && $time < 3600;
	    	return (round($time/60) == 1) ? '1 minute ago' : round($time/60).' minutes ago';
	 	// hours
		case $time >= 3600 && $time < 86399;
	    	return (round($time/3600) == 1) ? '1 hour ago' : round($time/3600).' hours ago';
		// days
		case $time >= 86399 && $time < 604800;
	    	return (round($time/86400) == 1) ? 'Yesterday' : convertIntTodate($time_ago,$format);
		//weeks
		case $time >= 604800 && $time < 2600640;
		return (round($time/604800) == 1) ? convertIntTodate($time_ago,$format) : convertIntTodate($time_ago,$format);
		// months
		case $time >= 2600640 && $time < 31207680;
        	return convertIntTodate($time_ago,$format);
		// weeks
		case $time >= 31207680;
		   return convertIntTodate($time_ago,$format) ;
		endswitch;
}




function getParticipantConversationList($conversationSid){
	$sid = env("TWILIO_ACCOUNT_SID");
	$token = env("TWILIO_ACCOUNT_TOKEN");
	$twilio = new Client($sid, $token);
	$env = env("APP_ENV");
	$uesrArr = Array();
	if(!empty($conversationSid)){
		$participantConversations = $twilio->conversations->v1->conversations($conversationSid)->participants->read(20);
		// prd($participantConversations);
		foreach ($participantConversations as $record) {
			// prd($record->toArray());
			$recordArr = $record->toArray();
			$uesrArr[] = $recordArr['identity'];
		}
	}
	return $uesrArr;
}

function getConversationDetailTwillio($conversationSid){
	$sid = env("TWILIO_ACCOUNT_SID");
	$token = env("TWILIO_ACCOUNT_TOKEN");
	$twilio = new Client($sid, $token);
	$env = env("APP_ENV");
	$groupDetail  = Array();
	$conversation = $twilio->conversations->v1->conversations($conversationSid)
                                          ->fetch();
	$groupDetail['name'] = $conversation->friendlyName;
	$attbutesArr = json_decode($conversation->attributes,true);
	// prd($attbutesArr);
	if(!empty($attbutesArr)){
		$groupDetail['profile_picture'] = $attbutesArr['GROUP_PROFILE_PIC'];
		$groupDetail['owner_id'] = $attbutesArr['GROUP_CREATOR_ID'];
	}
	return $groupDetail;
}
function getAvailableEvent($timeoffset,$user_id){
	$data = Array();
	$gmDate = gmdate('Y-m-d H:i:s');
	// echo $gmDate;exit;
	$event = Event::with([
		'getUser'=>function($query){
			$query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
		}
	])->select(['*'])
	->whereHas('getUser', function ($query){
		$query->whereRaw('users.deleted_at IS NULL');
	})
	->whereRaw('DATE(start_time) = "'.gmdate('Y-m-d').'" AND (UTC_TIMESTAMP() BETWEEN start_time AND end_time) AND admin_status = 1 AND status = 1 AND deleted_at IS NULL')->orderByRaw('start_time asc')->first();
	
	// (start_time >= "'.$gmDate.'" AND end_time <= "'.$gmDate.'")

	if($event == NULL){
		return $data;
	}
	$data = $event->toArray();
	// prd($data->getUser);
	if($data['user_id'] == $user_id){
		$data['isOwner'] = 1;
	}else{
		$data['isOwner'] = 0;
	}
	$user = User::select(['*'])->whereRaw('id='.$user_id)->first();
	if($user->isFollow == 1){
		$data['isFollow'] = 1;
	}else{
		$data['isFollow'] = 0;
	}

	//  $name = str_replace('"','', $data['name']);
      //	$channelName = str_replace(' ','_',$data['name']);
	$channelName = str_replace(' ','_',$data['name']); 
	$data['channel_name'] = $data['id'].'_'.$channelName;
	$data['post_id'] = (isset($data['post_id']) && $data['post_id'] != NULL) ? $data['post_id'] : 0;
	$data['live_user_count'] = $data['live_user_count'];
	// prd($data);
	return $data;
}
function getAgoraToken($eventName,$eventId,$userId,$isPublisher,$time){
	$appID = env('AGORA_APP_ID');
	$appCertificate = env('AGORA_APP_CERTIFICATE');
	$channelName = $eventId.'_'.$eventName;
	$user = $userId;
	if($isPublisher == 1){
		$role = RtcTokenBuilder::RolePublisher;
	}else{
		$role = RtcTokenBuilder::RoleAttendee;
	}
	$expireTimeInSeconds = $time;
	$currentTimestamp = now()->getTimestamp();
	$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
	$token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);
	return $token; 
}
function getGlobalSetting(){
	$returnData = Array();
	$globalSetting = new GlobalSetting();
	$globalSettingArr = $globalSetting->select('*')
                               ->get()
							   ->toArray();
	foreach ($globalSettingArr as $key => $value) {
		if($value['key'] == 'email') continue;
		$returnData[$value['key']] = (int)$value['value'];
	}
	return $returnData;
}
function sendLiveNotificationToFollowers($userName,$userId){
	$followerUsers = UserFollow::with([
		'getUser'=>function($query){
			$query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.device_id','users.device_type','users.deleted_at']);
			$query->whereRaw('users.deleted_at IS NULL');
		},
	])
	->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
	->where(function($query) use($userId){
		$query->where('users_follows.follower_id', $userId);
		$query->whereRaw('users_follows.status = 1');
	})
	->whereHas('getUser', function ($query){
		$query->whereRaw('users.deleted_at IS NULL');
	})
	->get()
	->toArray();
	// prd($followerUsers);
	if(!empty($followerUsers)){
		// prd($followerUsers);
		$message = $userName.' is live now.';
		$returnData['payload'] = ['notification_type'=>16,'user_id'=>$userId];
		foreach ($followerUsers as $key => $value) {
			$fcm_token = $value['get_user']['device_id'];
			$extra = sendFcmPushNotification($fcm_token, $message, $returnData,$value['get_user']['device_type']);
		}
	}
	return 1;
}
function deleteUserTwillio($userSId,$userId){
	$deleteUser = '';
	$sid = env("TWILIO_ACCOUNT_SID");
	$token = env("TWILIO_ACCOUNT_TOKEN");
	$twilio = new Client($sid, $token);
	$env = env("APP_ENV");
	$identity = $env.'_'.$userId;
	if(!empty($userSId)){
		$deleteUser = $twilio->conversations->v1->users($userSId)->delete();
	}
	return $deleteUser;
}
function createPost($userId,$heading,$descrption,$postType,$is_record=0){
	// $postType = 4 -> Event Post
	// $postType = 5 -> Event Go live User
	$post = new Post();
	$post->user_id = $userId;
	$post->heading = $heading;
	$post->description = $descrption;
	$post->post_type = $postType;
	$post->admin_status = 1;
	$post->is_recorded = $is_record;
	// $post->deleted_at = gmdate('Y-m-d H:i:s');
	$post->save();
	return $post->id;
}
function getPostType($postId){
	$post = Post::select(['post_type'])->whereRaw('id='.$postId)->first();
	return $post->post_type;
}
function getPostLiveUserCount($postId){
	$liveUserCount = 0;
	$postType = getPostType($postId);
	// Event live user count
	if($postType == 4 || $postType == 1){
		$event = Event::select(['live_user_count'])->whereRaw('post_id='.$postId)->first();
		if(!empty($event)){
			$liveUserCount = $event->live_user_count;
		}
	}else if($postType == 5){
		// User Live count
		$userId = getPostUserId($postId);
		$user = User::select(['live_user_count'])->whereRaw('id='.$userId)->first();
		if(!empty($user)){
			$liveUserCount = $user->live_user_count;
		}
	}
	return $liveUserCount;
}
function addUserTaggedData($typeId,$tag_json,$isType,$userId,$postId){
	$jsonArr = json_decode($tag_json,true);
	// prd($jsonArr);
	if(!empty($jsonArr) && $jsonArr!=''){
		foreach($jsonArr as $key => $value){
			$notidata = Array();
			$userTagged = new UsersTagged();
			$userTagged->user_id = $value['id'];
			$userTagged->tagged_in = $isType;
			$userTagged->tag_type_id = $typeId;
			$userTagged->save();
			
			// $senderId,$reciverId,$notiType,$data
			// For comment
			if($isType == 1){
				$notidata = ['comment_id'=>$typeId,'user_id'=>$userId,'post_id'=>$postId,'notification_type'=>18];
			}
			sendNotificationWithData($userId,$value['id'],18,$notidata);
		}
	}
	return 1;
}
function getUsername($userId){
	$user = User::select(['username'])->whereRaw('id='.$userId)->first();
	if(!empty($user))
		return $user->username;
	else
		return NULL;
}
function getCommentUserId($commentId){
	$comment = PostComment::select(['user_id'])->whereRaw('id='.$commentId)->first();
	return $comment->user_id;
}
function getPostIdComment($commentId){
	$comment = PostComment::select(['post_id'])->whereRaw('id='.$commentId)->first();
	return $comment->post_id;
}
function getUserStories($userId){
	$data = Array();
	$userStory = UserStory::with([
		'getUser'=>function($query){
			$query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.username','users.is_star']);
			$query->whereRaw('users.deleted_at IS NULL');
			}])
		->select(['users_stories.id','users_stories.user_id','users_stories.media_name','users_stories.media_type','users_stories.thumb_image_name','users_stories.width','users_stories.height','users_stories.created_at'])
        ->where(function($query) use($userId){
                $query->whereRaw('users_stories.deleted_at IS NULL AND users_stories.is_visible = 1 AND users_stories.user_id = '.$userId);
            })
		->get()
		->toArray();
	if(!empty($userStory)){
		foreach($userStory as $key => $value){
			if(!isset($data['User']) && empty($data['User'])){
				$data['User']['id'] =  $value['get_user']['id'];
				$data['User']['first_name'] =  $value['get_user']['first_name'];
				$data['User']['last_name'] =  $value['get_user']['last_name'];
				$data['User']['profile_picture'] =  $value['get_user']['profile_picture'];
				$data['User']['is_public_visibility'] =  $value['get_user']['is_public_visibility'];
				$data['User']['twilio_s_id'] =  $value['get_user']['twilio_s_id'];
				$data['User']['username'] =  $value['get_user']['username'];
				$data['lastCreatedAt'] = strtotime(gmdate('Y-m-d H:i:s'));
			}
			unset($value['get_user']);
			$data['Stories'][] = $value;
		}
	}
	// prd($data);
	return $data;	
}
function getPostName($postId){
	$post = Post::select(['heading'])->whereRaw('id='.$postId)->first();
	return $post->heading;
}
function getUserFollowerAndFollowingList($userId,$isLiveUsers){
	$isLiveUsersStr = '';
	$loggedInUserId= Auth::user()->id;
	if($isLiveUsers == 1)
		$isLiveUsersStr = ' AND users.is_live = 1';
	$followerList = DB::table('users_follows')
                ->join('users', function ($join) use($isLiveUsersStr){
                    $join->on('users_follows.follower_id', '=', 'users.id')->whereRaw('users.deleted_at IS NULL'.$isLiveUsersStr);
                })
                ->select('users.id as id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.username','users.is_live','users.live_post_id','users.is_star')
                ->where(function($query) use($userId){
                    $query->whereRaw("users_follows.user_id = ".$userId." AND users_follows.status = 1");                    
                });
	$allList = DB::table('users_follows')
	->join('users', function ($join) use($isLiveUsersStr){
		$join->on('users_follows.follower_id', '=', 'users.id')->whereRaw('users.deleted_at IS NULL'.$isLiveUsersStr);
	})
	->select('users.id as id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.username','users.is_live','users.live_post_id','users.is_star')
	->where(function($query) use($userId){
		$query->whereRaw("users_follows.follower_id = ".$userId." AND users_follows.status = 1");                    
	})
	->union($followerList)
    ->where('users.id', '<>', $loggedInUserId)
	->orderByRaw('first_name ASC')
	->get();
	// ->toArray();
	return $allList;
}
function agoraRecAcuireToken($accessChannelName,$token,$recordingUid){
	// echo $accessChannelName;exit;
	$response = Http::withHeaders([
		'Content-Type' => 'application/json',
	])->withBasicAuth(env('AGORA_CUSTOMER_ID'), env('AGORA_CUSTOMER_CERTIFICATE'))
	->withBody('{"cname": "'.$accessChannelName.'",
				"uid": "'.$recordingUid.'",
				"token":"'.$token.'","clientRequest":{}}'
				,'application/json')
	->post(env('AGORA_URL').'/'.env('AGORA_APP_ID').'/cloud_recording/acquire')->throw()->json();
	return $response['resourceId'];
}
function agoraRecStart($accessChannelName,$resourceId,$token,$recordingUid,$web=null){
	$width='360';
	$height='640';
	if($web==1){
		$width='768';
		$height='432';
	}
	
	$response = Http::withHeaders([
		'Content-Type' => 'application/json',
	])->withBasicAuth(env('AGORA_CUSTOMER_ID'), env('AGORA_CUSTOMER_CERTIFICATE'))
	->withBody('{
					"cname":"'.$accessChannelName.'",
					"uid":"'.$recordingUid.'",
					"clientRequest":{
						"recordingFileConfig":{
							"avFileType":["hls","mp4"]
						},
						"token":"'.$token.'",
							"recordingConfig":{
							"maxIdleTime":10,
							"streamTypes":2,
							"audioProfile":1,
							"channelType":1,
							"videoStreamType":0,
							"transcodingConfig":{
								"width":'.$width.',
								"height":'.$height.',
								"fps":14,
								"bitrate":720,
								"mixedVideoLayout":1,
								"maxResolutionUid":"1"
								}
							},
						"storageConfig":{
							"vendor":'.env('AGORA_S3_VENDOR').',
							"region":'.env('AGORA_S3_REGION').',
							"bucket":"'.env('AGORA_S3_BUCKET').'",
							"accessKey":"'.env('AGORA_S3_ACCESS_KEY').'",
							"secretKey":"'.env('AGORA_S3_SECRET_KEY').'",
							"fileNamePrefix": [
								"recording"
							]
						}	
					}
				}'
				,'application/json')
	->post(env('AGORA_URL').'/'.env('AGORA_APP_ID').'/cloud_recording/resourceid/'.$resourceId.'/mode/mix/start')->throw()->json();
	return $response['sid'];
}





function agoraRecQuery($sid,$resourceId){
	$response = Http::withHeaders([
		'Content-Type' => 'application/json',
	])->withBasicAuth(env('AGORA_CUSTOMER_ID'), env('AGORA_CUSTOMER_CERTIFICATE'))
	->get(env('AGORA_URL').'/'.env('AGORA_APP_ID').'/cloud_recording/resourceid/'.$resourceId.'/sid/'.$sid.'/mode/mix/query')->throw()->json();
	return $response['code'];
}
function agoraRecStop($accessChannelName,$recordingUid,$sid,$resourceId){
	$response = Http::withHeaders([
		'Content-Type' => 'application/json',
	])->withBasicAuth(env('AGORA_CUSTOMER_ID'), env('AGORA_CUSTOMER_CERTIFICATE'))
	->withBody('{"cname": "'.$accessChannelName.'",
				"uid": "'.$recordingUid.'",
				"clientRequest":{}}'
				,'application/json')
	->post(env('AGORA_URL').'/'.env('AGORA_APP_ID').'/cloud_recording/resourceid/'.$resourceId.'/sid/'.$sid.'/mode/mix/stop')->throw()->json();
	// prd($response);
	return $response['serverResponse']['fileList'][0]['fileName'];
	
}
function uploadMediaPostFromS3($postId,$filePath){
	$getFileUrl = env('AWS_URL').$filePath;
	if(Storage::disk('s3')->exists($filePath)){
		$width = 0;
		$height = 0;
		$isMediaAvail = PostMedia::select(['id','post_id'])->whereRaw('post_id = '.$postId.' AND deleted_at IS NULL')->get()->last();
		if($isMediaAvail == NULL && empty($isMediaAvail)){
			$postMedia = new PostMedia();
		}else{
			$newArr = $isMediaAvail->toArray();
			$postMedia = PostMedia::findOrFail($newArr['id']);
		}
		$destinationPath = config('constants.post_media.upload_path');
		$fileName = rand().time().'.mp4';
		// echo $fileName;exit;
		$pathToUpload = $destinationPath.'/'.$fileName;
		/****** Video copy   */
		$ffprobe = FFMpeg\FFProbe::create();
		$video = $ffprobe->streams($getFileUrl)->videos()->first();
		$width = $video->get('width');
		$height = $video->get('height');
		Storage::disk('s3')->copy($filePath, $pathToUpload);
		$postMedia->file_name = $fileName;
		/****** Video copy  */
		/****** Thumb image  */
		$ffmpeg = FFMpeg\FFMpeg::create();
		$video = $ffmpeg->open($getFileUrl);                    
		$path = public_path().'/'.config('constants.admin_image.upload_path').'frame.jpg';
		$img = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3))->save($path);
		$frameImagePath = url(config('constants.admin_image.display_path').'frame.jpg');
		// echo $frameImagePath;exit;
		$thumbFileName = 'thumb_'.rand().time().'.jpg';
		$pathToUpload = $destinationPath.'/'.$thumbFileName;
		Storage::disk('s3')->put($pathToUpload, file_get_contents($frameImagePath));		
		$postMedia->thumb_image_name = $thumbFileName;
		/****** Thumb image  */
		$postMedia->media_type = 2;
		$postMedia->post_id = $postId;
		$postMedia->media_order = 1;
		$postMedia->is_default = 1;
		$postMedia->width = $width;
		$postMedia->height = $height;
		$save = $postMedia->save();
		return Post::where('id', '=', $postId)->update(['post_type' => 1,'updated_at'=>gmdate('Y-m-d H:i:s')]);
	}	
	return 0;
}
function addViewCountPost($postId,$loggedInUserId){
	$post = Post::where('id', $postId)->first();
	if(!empty($post)){
		if($loggedInUserId != $post->user_id){
			$post->no_of_view = ($post->no_of_view + 1);
			$post->save();
		}		
		return $post->no_of_view;
	}else{
		return 0;
	}
}
function deleteMediaPost($ids){
	if(is_array($ids) && !empty($ids)){
		$destinationPath = config('constants.post_media.upload_path');
		foreach($ids as $key => $value){
			$postMedia = PostMedia::findOrFail($value);
			if($postMedia != NULL && !empty($postMedia)){
				if(isset($postMedia->file_name) && $postMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
					Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->file_name);
				}
				$postMedia->delete();
			}
		}	
	}
	return 1;
}

function  countViewMillionBillionLogic($number){
		$price = '14,500,000';
		echo $price = (int)str_replace(",","",$price);
		echo '<br>';

		$thousands = $price % 1000000;
		$millions = ($price - $thousands)/1000000;
		$thousands = $thousands / 1000;
		echo $thousands = $thousands . " Thousands";
		echo $millions = $millions . " Millions";
}
function getPostMediaCount($post_id){
	$mediaCount = PostMedia::select(['id'])->whereRaw('post_id='.$post_id)->count();
	if($mediaCount >= 5){
		return 0;
	}else{
		return 1;
	}
}
function isStoryDeleted($storyId){
	$story = UserStory::select(['deleted_at'])->whereRaw('id='.$storyId)->first();
	if(empty($story))
		return 0;
	if($story->deleted_at != NULL){
		return 1; 
	}else{
		return 0;
	}
}
function getStoryUserId($storyId){
	$story = UserStory::select(['user_id'])->whereRaw('id='.$storyId)->first();
	return $story->user_id;
}
function getAdsBlankObj($type){
	// $type = 0 -> Post Board Ads blank Object
	// $type = 1 -> Story Ads  blank Object
	if($type == 0){
		$arr = Array();
		$arr['id'] = "-1";
		$arr['user_id'] = "0";
		$arr['heading'] = "Ads";
		$arr['description'] = "Google Ads";
		$arr['post_type'] = 7;
		$arr['country_id'] = 0;
		$arr['category_id'] = 0;
		$arr['city'] = "";
		$arr['zip_code'] = "";
		$arr['is_saved'] = 0;
		$arr['is_liked'] = 0;
		$arr['total_likes'] = 0;                
		$arr['is_commented'] = 0;
		$arr['total_comments'] = 0;                
		$arr['is_reported'] = 0;
		$arr['created_at'] = time();
		$arr['updated_at'] = time();
		$arr['no_of_view'] = 0;
		$arr['get_user']['id'] = 0;
		$arr['get_user']['first_name'] = "Ads";
		$arr['get_user']['last_name'] = "Ads";
		$arr['get_user']['profile_picture'] = "";
		$arr['get_user']['twilio_s_id'] = "";
		$arr['get_user']['twillio_identity'] = "";
		$arr['get_user']['is_star'] = 0;
		$arr['is_viewed'] = 1;
		$arr['total_views'] = 0;
		$arr['get_media'] = [];
		return $arr;
	}else if($type == 1){
		$arr = Array();
		$arr['id'] = -1;
		$arr['user_id'] = 0;
		$arr['media_name'] = "Ads";
		$arr['media_type'] = 7;
		$arr['thumb_image_name'] = "";
		$arr['width'] = 0;
		$arr['height'] = 0;
		$arr['is_liked'] = 0;
		$arr['total_likes'] = 0;
		$arr['created_at'] = time();
		$arr['is_viewed'] = 1;
		$arr['total_views'] = 0;
		return $arr;
	}
}
function addUserHobbies($hobbiesIds,$newTexts,$userId){
	UserHobby::where(['user_id'=>$userId])->delete();
	if($hobbiesIds !=''){
		$hobbiesIdArr = explode(',',$hobbiesIds);
		if(!empty($hobbiesIdArr)){
			foreach ($hobbiesIdArr as $key => $value) {
				$hobbyExists = UserHobby::whereRaw('user_id='.$userId.' AND hobby_id = "'.$value.'"')->exists();
				// var_dump($hobbyExists);exit;
				if(!$hobbyExists){
					$userHobby = new UserHobby();	
					$userHobby['user_id'] = $userId;
					$userHobby['hobby_id'] = $value;
					$userHobby['created_at'] = time();
					$userHobby['updated_at'] = time();
					// prd($userHobby);
					$userHobby->save();
				}
			}
		}
	}
	if($newTexts !=''){
		$hobbiesStrArr = explode(',',$newTexts);
		if(!empty($hobbiesStrArr)){
			foreach ($hobbiesStrArr as $key => $value) {
					$hobbyId = addHobbies($value);
					$userHobby = new UserHobby();	
					$userHobby['user_id'] = $userId;
					$userHobby['hobby_id'] = $hobbyId;
					$userHobby['created_at'] = time();
					$userHobby['updated_at'] = time();
					$userHobby->save();
				}
		}
	}
}
function addHobbies($name){
	$hobbyExists = Hobby::select(['id'])->whereRaw('LOWER(name) = "'.$name.'"')->first();
	// prd($hobbyExists);
	if(!empty($hobbyExists)){
		return $hobbyExists->id;
	}else {
		$Hobby = new Hobby();	
		$Hobby['name'] = $name;
		$Hobby['status'] = 1;
		$Hobby['created_at'] = date('Y-m-d H:i:s',time());
		$Hobby['updated_at'] = date('Y-m-d H:i:s',time());
		$Hobby->save();
		return $Hobby->id;
	}
}
function getHobbiesDetailUser($userId){
	$returnArr = Array();
	$hobbies = UserHobby::with(['getHobby'=>function($query){
			$query->select(['hobbies.id','hobbies.name']);
			// $query->whereRaw('posts.user_id != '.$user->id);
		}
	])->where(['user_id'=>$userId])->get()->toArray();
	if(!empty($hobbies)){
		foreach ($hobbies as $key => $value) {
			$returnArr[] = ['id'=>$value['get_hobby']['id'],'name' => $value['get_hobby']['name']];
		}
	}
	return $returnArr;
}


function sendOtptophone($data){
        	$phone_number=$data['phone_number'];
			if($data['type']==1){
				$custum_message=$data['message'];
			}else{
				$custum_message="Do not share your ISIT App otp is ".$data['otp'];
				
			}

			$sid = env('TWILIO_ACCOUNT_SID');
			$token = env('TWILIO_ACCOUNT_TOKEN');
			$sms_form = env("TWILIO_SMS_FORM");
			$twilio = new Client($sid,$token);
			
			$message = $twilio->messages
			->create('+916350227598', // to
				array(
					"from" => $sms_form,
					"body" => $custum_message
				)
			);
		return true;

}

