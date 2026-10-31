<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Frontend\FrontChangeprofile;
use App\Http\Requests\Frontend\FrontChangePasswordRequest;
use App\Models\User;
use App\Models\Country;
use App\Models\BlockUser;
use App\Http\Requests\Frontend\UserBlockRequest;
use Auth;
use DB;
use File;
use Carbon\Carbon;
use App\Models\UserFollow;
use Validator;
use Storage; 
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\Event;
use App\Http\Requests\Frontend\UserFollowRequest;

class ProfileController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->middleware('web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {     
    	$country_arr =  config('country.country_arr');
        
        foreach ($country_arr as $key => $value)
        {
            $value=$key;
            $country_arr[$value]='+'.$key;
        }

        $user = Auth::guard('web')->user();
        $imagePath = '';
        $folderName = config('constants.user_image.upload_path');
        $url = getS3Url($folderName);
        $displayImagePath = config('constants.front_image.display_path');
        $user['image_path'] = url($url.$user['profile_picture']);
        $user['image_path_exist']='';
        if($user['profile_picture'] != NULL && $user['profile_picture'] != '')
            $user['image_path_exist'] = $url.$user['profile_picture'];
            // $user['name']=$user['first_name'].' '.$user['last_name'];
        $data = array(
            'title' => __('language.change_profile_title'),
            'backBtnLink' => url('post-board'),
            'changePasswordBtnLink' => url('change-password'),
            'profileUpdateBtnLink' => route('frontend-profile-update'),
            'user' => $user,
            'country_arr' => $country_arr,
        );
        return view('frontend.user.profile',$data);
    }

    public function changePassword(FrontChangePasswordRequest $request){

        $currentPassword = Auth::guard('web')->user()->password;
        if (!(Hash::check($request->get('old_password'), $currentPassword))) {
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|max:32',
            'confirm_password' => 'required|string|required_with:new_password|same:new_password',
        ]);

        //Change Password
        $user = Auth::guard('web')->user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return redirect('explore')->with("success","Password changed successfully !");
       
     
    }

    public function profileUpdate(Request $request){
        
       
        DB::beginTransaction();
        try{
          
            if($request->value_pic!=1)
            {
                $rules = [
                    'profile_picture' => 'required|image|mimes:jpeg,png,jpg',
                ];
                $message = [
                    'profile_picture.required' => trans('The profile picture field is required.'),

                ];
                $validator = Validator::make($request->all(), $rules, $message);
                if($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            $userId = Auth::guard('web')->user()->id;
            if(isset($userId)){
        
                $userAuth = Auth::guard('web')->user();
                $user = User::select(['*'])->whereRaw('id='.$userId)->first();
                $user->first_name = $request['first_name'];
                $user->last_name = $request['last_name'];

                // $user->last_name = $request['last_name'];
                $user->phone_number = $request['phone_number'];
                if(isset($request['email'])){
                    $user->email = $request['email'];
                }
                
                if(isset($request['bio_text'])){
                    $user->bio_text = $request['bio_text'];
                }
                if(isset($request['country_code'])){
                    $user->country_code = $request['country_code'];
                }

              

                if($request->hasFile('profile_picture')){
                    $file = $request->file('profile_picture');
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

                $userFolderName = config('constants.user_image.upload_path');
                $path= getS3Url($userFolderName);
                $fullName=$user->first_name." ".$user->last_name;
                $profileImageUrl=$path.$user->profile_picture;
                if($user->twilio_s_id == '' || $user->twilio_s_id == NULL){
                    $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
                }
                $save = $user->save();
                if($user->twilio_s_id != ''){
                    $user->twilio_s_id = updateUserTwillio($user->twilio_s_id,$user->id,$fullName,$profileImageUrl);
                }

                $user->save();
            }
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        return redirect('my-profile')->with("success","Profile updated successfully !");
    }


    public function showChangePasswordForm(){
  
        return response()->view('frontend.user.change-password', ['name' => 'rocky']);
        // return $data;
    }

    public function getBlockUser(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            $request->user_id = $user->id;
            if(empty($request->user_id)){  return redirect()->back()->with('error','Something wrong.'); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = BlockUser::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
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
            ->orderByRaw($userSortBy)
            ->paginate(10);

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
            // $response['paging'] = [
            //     'total_item'=>$users->total(),
            //     'item_per_page'=>$users->perPage(),
            //     'current_page'=>$users->currentPage(),
            //     'last_page'=>$users->lastPage(),
            //     'prev'=>$users->previousPageUrl(),
            //     'next'=>$users->nextPageUrl(),
            //     'first'=>$users->url(1),
            //     'last'=>$users->url($users->lastPage()),
            // ];
            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            // return redirect()->back()->with('error', $this->optionalData['message']);
        }
        $response['title']="Blocked Users";
        return view('frontend.user-profile.blocked-users',$response);
    }

    
    public function blockUser(UserBlockRequest $request){
    	// prd($request->all());
        $user = Auth::guard('web')->user();
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


    public function UnblockUser(Request $request){
    	// prd($request->all());
        $user = Auth::guard('web')->user();
        $request['user_id'] = $user->id;
        $response = [];
        if(empty($request->unblock_user_id)){ throw new \Exception(__('api-messages.6000')); }
        try{
            $blockUser = BlockUser::where(['user_id'=>$user->id,'block_user_id'=>$request->unblock_user_id,'block_by'=>$user->id])->first();
            if(empty($blockUser)){
                return getResponse(112,400);
            }
            if(!empty($blockUser))
            $blockUser->delete();
            /**** Remove vice versa entry */
            $blockUser = BlockUser::where(['user_id'=>$request->unblock_user_id,'block_user_id'=>$user->id])->first();
            if(!empty($blockUser))
            $blockUser->delete();
            /**** Remove vice versa entry */
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(635,200,$response);
    }


     //for other my profile this method will work
    public function myProfile(Request $request)
    {     
        $user = Auth::guard('web')->user();
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        if(!empty($request->user_id))
            $userId = $request->user_id;    
        else
        $userId = $user->id;
        $response = [];
        $todayDate = Carbon::today()->toDateString();
        $folderName = config('constants.user_image.upload_path');
        $url = getS3Url($folderName);
        $response['path'] = $url;
        $profile = User::where([['id', $userId]])
            ->select(['id','name','email','status'])
            ->first();

        if(!empty($profile)){
            if($profile->status != 1){
                return redirect()->back()->with('error', 'Something wrong.');
            }else if($profile->is_block == 1){
                if($profile->is_block_by_you == 1){
                    return redirect()->back()->with('error', 'Something wrong.');
                }else{
                    return redirect()->back()->with('error', 'Something wrong.');
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
            }
        }
        $request['user_id']=$userId;
        $response['my_post']=$this->getMyPosts($request);
        $response['saved_post']=$this->getSavedPosts($request);
        if($request->ajax()){
            
            if($request->post_type==1)
            {
                $response['my_post']= $response['saved_post'];
                $save_html = view('frontend.user-profile.my-post',$response)->render();
                return response()->json(['save_html'=>$save_html]);
            }else{
                $my_post_html = view('frontend.user-profile.my-post',$response)->render();
                return response()->json(['my_post_html'=>$my_post_html]);
            }
        }
        $response['title'] ='User Profile';
    
        return view('frontend.user-profile.my-profile',$response);
    }


    //for other user profile this method will work
    public function userProfile(Request $request,$user_id)
    {     
        $user = Auth::guard('web')->user();
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $request->user_id=$user_id;
        if(!empty($request->user_id))
            $userId = $request->user_id;    
        else
        $userId = $user->id;
        $response = [];
        $todayDate = Carbon::today()->toDateString();
        $folderName = config('constants.user_image.upload_path');
        $url = getS3Url($folderName);
        $response['path'] = $url;
        $profile = User::where([['id', $userId]])
            ->select(['id','name','email','status'])
            ->first();
            if(empty($profile))
            {
                $data_arr=['error_name'=>'User not found'];
                return view('frontend.common.no-post',$data_arr);
            }

        if(!empty($profile)){
            if($profile->status != 1){
                $data_arr=['error_name'=>'User not found'];
                return view('frontend.common.no-post',$data_arr);
                // return redirect()->back()->with('error', 'Something wrong.');
            }else if($profile->is_block == 1){
                if($profile->is_block_by_you == 1){
                    $data_arr=['error_name'=>'User not found'];
                    return view('frontend.common.no-post',$data_arr);
                    // return redirect()->back()->with('error', 'Something wrong.');
                }else{
                    $data_arr=['error_name'=>'User not found'];
                    return view('frontend.common.no-post',$data_arr);
                    // return redirect()->back()->with('error', 'Something wrong.');
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
            }
        }
        $request['user_id']=$userId;
        $response['my_post']=$this->getMyPosts($request);
        $response['saved_post']=$this->getSavedPosts($request);
        // dd($response['saved_post']);
        $response['title'] ='User Profile';
        if($request->ajax()){
            
            if($request->post_type==1)
            {
                $response['my_post']= $response['saved_post'];
                $save_html = view('frontend.user-profile.my-post',$response)->render();
                return response()->json(['save_html'=>$save_html]);
            }else{
                $my_post_html = view('frontend.user-profile.my-post',$response)->render();
                return response()->json(['my_post_html'=>$my_post_html]);
            }
        }
    
        return view('frontend.user-profile.my-profile',$response);
    }

    // public function getMyPosts(Request $request){
    //     $response = [];
    //     $userFolderName = config('constants.user_image.upload_path');
    //     $postFolderName = config('constants.post_media.upload_path');
    //     $response['path']['post_media'] = getS3Url($postFolderName);
    //     $response['path']['user_media'] = getS3Url($userFolderName);
    //     $user = Auth::user();
    //     try{
    //         if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }

    //         if(getBlockByUser($user->id,$request->user_id)){
    //             return getResponse(638,404);
    //         }
    //         $postSortBy = 'id desc';
    //         $userId = $request->user_id;
    //         $posts = Post::with([
    //             'Country'=>function($query){
    //                 $query->select(['countries.id','countries.name']);
    //             },
    //             'Category'=>function($query){
    //                 $query->select(['categories.id','categories.name']);
    //             },
    //             'getMedia'=>function($query){
    //                 $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
    //             },
    //             'getUser'=>function($query){
    //                 $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
    //             }
    //         ])
    //         ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at')
    //         ->where(function($query) use($userId){
    //             $query->where('posts.user_id', $userId);
    //             $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1');
    //         })
    //         ->orderByRaw($postSortBy)
    //         ->paginate(10);
    //         $response['posts'] = $posts;
        
    //     } catch (\Exception $e){
    //         $this->optionalData['message'] = $e->getMessage();
    //         return getResponse(6000,403,$response,$this->optionalData);
    //     }
    //     return $response;
    // }


    public function getMyPosts(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }

            if(getBlockByUser($user->id,$request->user_id)){
                return getResponse(638,404);
            }
            $postSortBy = 'id desc';
            $userId = $request->user_id;
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at','posts.no_of_view')
            ->where(function($query) use($userId){
                $query->where('posts.user_id', $userId);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.post_type NOT IN (4,5) AND posts.admin_status=1');
            })
            ->orderByRaw($postSortBy)
            ->paginate(100);
            $response['posts'] = $posts;
            
            // $response['paging'] = [
            //     'total_item'=>$posts->total(),
            //     'item_per_page'=>$posts->perPage(),
            //     'current_page'=>$posts->currentPage(),
            //     'last_page'=>$posts->lastPage(),
            //     'prev'=>$posts->previousPageUrl(),
            //     'next'=>$posts->nextPageUrl(),
            //     'first'=>$posts->url(1),
            //     'last'=>$posts->url($posts->lastPage()),
            // ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return $response;
    }

    public function getSavedPosts(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            $postSortBy = 'id desc';
            $userId = $request->user_id;
            // echo $userId;exit;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $posts = SavedPost::with([
                'getPost'=>function($query) use($user,$blockUserIds){
                    $query->select(['posts.id','posts.user_id','posts.heading','posts.description','post_type','country_id','category_id','city','zip_code','created_at','no_of_view']);
                    $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type != 3 AND posts.user_id NOT IN ('.$blockUserIds.')');
                    // $query->whereNotIn('posts.user_id', [$blockUserIds]);
                },
                'getPost.getMedia'=>function($query){
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
                    $query->where('posts_media.is_default',1);
                },
                'getPost.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('saved_posts.id','saved_posts.user_id','saved_posts.post_id','saved_posts.created_at')
            ->where(function($query) use($userId){
                $query->where('saved_posts.user_id', $userId);
            })
            ->orderByRaw($postSortBy)
            ->paginate(10);
            $NewPosts = $posts->getCollection()->transform(function ($value) {
                // Your code here
                if(isset($value['getPost']))
                    return $value['getPost'];
            });
            $NewPosts = $NewPosts->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['posts'] = $NewPosts;
            // $response['paging'] = [
            //     'total_item'=>$posts->total(),
            //     'item_per_page'=>$posts->perPage(),
            //     'current_page'=>$posts->currentPage(),
            //     'last_page'=>$posts->lastPage(),
            //     'prev'=>$posts->previousPageUrl(),
            //     'next'=>$posts->nextPageUrl(),
            //     'first'=>$posts->url(1),
            //     'last'=>$posts->url($posts->lastPage()),
            // ];
    
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return $response;
    }

    public function followUnfollowList(Request $request,$user_id,$type)
    {    
        $id = Auth::user()->id;
        if(!empty($request->user_id))
        {

        }
        else{
            $request->merge([
                'user_id' => $id
            ]);
        }
        $data = [];
        $userFolderName = config('constants.user_image.upload_path');
        $data['path'] = getS3Url($userFolderName);
        $data['followers']=$this->followerList($request);
        $data['following']=$this->followingList($request);
        $data['title'] = 'Followers & Following';
        return view('frontend.user-profile.followers-unfollowers',$data);
    }

    
    public function followerList(Request $request){
        $user = Auth::user();
        $search_keyword = '';
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getUser', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('concat(users.first_name, " ", users.last_name) like "%'.strtolower($search_keyword).'%"');
                    
                        });
                }
            })
            ->where(function($query) use($userId){
                $query->where('users_follows.follower_id', $userId);
                $query->whereRaw('users_follows.status = 1');
            })
            ->orderByRaw($userSortBy)
            ->paginate(200);
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
            $response['followersusers'] = $NewUsers->toArray();
            $html='';
            $userFolderName = config('constants.user_image.upload_path');
            $path = getS3Url($userFolderName);
            if(count($response['followersusers']) > 0)
            {
                foreach($response['followersusers'] as $followersValue)
                {
                    $url=route('user-profile', ['user_id' => $followersValue['id']]);
                    // $url = url('front-panel/user-profile').'?user_id='.$followersValue['id'];
                    $arrow_url = url('/front/images/chevron-right.svg');
                    $html .='<div class="follow-single-user follower-users-count"><div class="follow-username-img"><div class="followuser-img">';
                    if($followersValue['profile_picture'])
                    {
                    $html .='<img src="'.$path.$followersValue['profile_picture'].'" alt="">';
                    }else{
                    $profile_url = url('/front/images/placeholder-user.png');
                    $html .='<img src="'.$profile_url.'" alt="Lock">';
                    }
                    $html .='</div><div class="followuser-name"><a href="'.$url.'">'.$followersValue['first_name'].' '.$followersValue['last_name'].'</a>';
                    $html .='<div class="user-post-followers"><span class="total-posts">'.$followersValue['post_count'].' Posts</span>';
                    $html .='<span class="total-followers">'.$followersValue['follower_count'].' followers</span>';
                    $html .='</div></div></div><div class="follow-btn">';
                    $html .='<a href="'.$url.'"><img src='.$arrow_url.' alt=""></a></div></div>';
                }
            }
            else
            {
                $view = view('frontend.common.no-data');
                $html= $view->render();
            }
            $response['followers_list_html'] =$html;

            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
          
        return $response;
    }

    public function followingList(Request $request){
    	$response = [];
        $user = Auth::user();
        $search_keyword = '';
            if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $id_arr=$request->id_arr;
            $users = UserFollow::with([
                'getFollower'=>function($query) use($user){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    // $query->whereRaw('posts.user_id != '.$user->id);
                },
            ])
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.action_user_id','users_follows.status')
            ->whereHas('getFollower', function ($query) use($search_keyword,$id_arr){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('concat(users.first_name, " ", users.last_name) like "%'.strtolower($search_keyword).'%"');
                    
                        });
                }
                if(!empty($id_arr)){
                        $query->whereNotIn('users.id',$id_arr);
                }
            })
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
                $query->where('users_follows.status', 1);
            })
            ->orderByRaw($userSortBy)
            ->paginate(100);
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

            $response['followingusers'] = $NewUsers->toArray();
            $html='';
            $html2='';
            $userFolderName = config('constants.user_image.upload_path');
            $path = getS3Url($userFolderName);

            

            if(count($response['followingusers']) > 0)
            {
                foreach($response['followingusers'] as $followingValue)
                {
                    $url=route('user-profile', ['user_id' => $followingValue['id']]);
                    // $url = url('front-panel/user-profile').'?user_id='.$followingValue['id'];
                    $html .='<div class="follow-single-user following-users-count"><div class="follow-username-img"><div class="followuser-img">';
                    if($followingValue['profile_picture'])
                    {
                    $html .='<img src="'.$path.$followingValue['profile_picture'].'" alt="">';
                    }else{
                    $profile_url = url('/front/images/placeholder-user.png');
                    $html .='<img src="'.$profile_url.'" alt="Lock">';
                    }
                    $html .='</div><div class="followuser-name"><a href="'.$url.'">'.$followingValue['first_name'].' '.$followingValue['last_name'].'</a>';
                    $html .='<div class="user-post-followers"><span class="total-posts">'.$followingValue['post_count'].' Posts</span>';
                    $html .='<span class="total-followers">'.$followingValue['follower_count'].' followers</span>';
                    $html .='</div></div></div><div class="follow-btn">';
                    $html .='<button onclick="removeFollowing('.$followingValue['id'].')" class="btn btn-secondary">Unfollow</button>';
                    $html .='</div></div>';

                    $identity=env("APP_ENV").'_'.$followingValue['id'];
                    $html2 .='<div class="follow-single-user group_user_popup" id="'.$identity.'"><div class="follow-username-img">';
                    if($followingValue['profile_picture'])
                    {
                        $html2 .='<div class="followuser-img"><img src="'.$path.$followingValue['profile_picture'].'" alt=""></div>';
                    }else{
                    $profile_url = url('/front/images/placeholder-user.png');
                       $html2 .='<div class="followuser-img"><img src="'.$profile_url.'" alt=""></div>';
                    }
                  
                    $html2 .='<div class="followuser-name"><a href="#">'.$followingValue['first_name'].' '.$followingValue['last_name'].'</a></div></div><div class="follow-btn">';
                    $html2 .='<button id="text_change_add_remove_'.$followingValue['id'].'" type="button" data_id="'.$followingValue['id'].'" class="btn btn-secondary add_remove_group">';
                    $html2 .='+ Add</button></div></div>';
                    
                }
            }
            else
            {
                $view = view('frontend.common.no-data');
                $html= $view->render();
                $html2= $view->render();
            }
            
            $response['following_list_html'] =$html;
            $response['following_list_create_group'] =$html2;

            
            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
       
        return $response;
    }


    public function followUser(UserFollowRequest $request){
    	//prd($request->all());
        $user = Auth::guard('web')->user();
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
                sendNotificationWithData($user->id,$request->follower_id,5,['user_id'=>$user->id,'notification_type'=>5]);
            }
            $userFollow->save();
            $response['follower_id'] = $userFollow->id;
            $response['status'] = $userFollow['status'];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        $msg=623;
        if($request->public_visible==0)
        {
         $msg=659;
        }
        return getResponse($msg,200,$response);
    }
   
    public function unfollowUser(Request $request){
    	// prd($request->all());
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

    
    public function pendingRequest(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        $id = Auth::user()->id;

        $request->merge([
            'user_id' => $id
        ]);
        $search_keyword = '';
        if(empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $userSortBy = 'id desc';
            $userId = $request->user_id;
            $users = UserFollow::with([
                'getUser'=>function($query) use($user,$search_keyword){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
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
            $response['title'] ='Pending Request';
        return view('frontend.user-profile.pending-request',$response);
    }

    public function acceptFollowRequest(Request $request){
    	$response = [];
        $user = Auth::user();
        try{
            if(empty($request->user_id)){
                throw new \Exception(__('api-messages.6000')); }
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
            $users = User::select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility'])
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
                            // $query->orWhereRaw('LOWER(users.email) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('concat(users.first_name, " ", users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->orderByRaw($userSortBy)
            ->paginate(100);
            $response['users'] = $users->toArray();
            $html = '';

            if(count($users)>0)
            {
        foreach ($users as $key => $value) {
            $userData= getProfileData($value->id);
            $is_follow=$userData['is_follow'];
            $url=  route('user-profile', ['user_id' => $value->id]);
            //$url = url('front-panel/user-profile').'?user_id='.$value->id;

            $profile_url = url('/front/images/placeholder-user.png');
            if($userData['profile_picture']){
                $profile_url=$response['path']['user_media'].$userData['profile_picture'];
             }
            $html .= '<div class="follow-single-user">';
            $html .='<div class="follow-username-img">';
            $html .='<input type="hidden" id="public_visible_'.$userData['id'].'" value="'.$userData['is_public_visibility'].'"/>';
            $html .='<div class="followuser-img"><img src="'.$profile_url.'" alt=""></div>';
            $html .='<div class="followuser-name"><a href="'.$url.'">'.$value->first_name.' '.$value->last_name.'</a></div>';
            $html .= '</div>';
            $html .= '<div class="follow-btn d-flex">' ;
            if($is_follow==1)
            {
                $html .='<input type="hidden" id="follow_val_'.$value->id.'" value="1"></input><button  type="button" class="btn" onclick="followUnfollowUser('.$value->id.')" id="btn_txt_follow_unfollow_'.$value->id.'"> Following</button>';                         
            }
            else if($is_follow==2)
            {
                $html .='<input type="hidden" id="follow_val_'.$value->id.'" value="1"></input><button  type="button" class="btn btn-secondary" onclick="followUnfollowUser('.$value->id.')" id="btn_txt_follow_unfollow_'.$value->id.'"> Requested</button>';                         
            }
            else
            {
                $html .='<input type="hidden" id="follow_val_'.$value->id.'" value="0"></input><button type="button" class="btn btn-secondary" onclick="followUnfollowUser('.$value->id.')" id="btn_txt_follow_unfollow_'.$value->id.'">Follow</button>'; 
            }
            $html .= '<input type="hidden" id="search_bar_check" value="1">' ;
            $html .= '</div></div>';
        }
    }
        else
        {
            $html .= '<div class="follow-single-user">';
            $html .='<div class="follow-username-img">';
            $html .='<div class="followuser-img"></div>';
            $html .='<div class="followuser-img"></div>';
            $html .='<div class="followuser-name">No User found</div>';
            $html .= '</div>';
            $html .= '<div class="follow-btn d-flex">' ;
            $html .= '</div></div>';
            $response['search_html'] = $html;
        }
        $response['search_html'] = $html;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    

    public function publicVisibilityChange(Request $request){

        $user = Auth::user();
        $userData = User::where([['id', $user->id]])->first();
        $userData->is_public_visibility = ($userData->is_public_visibility == 1 ? 0 : 1);
        $userData->save();
        if($userData->is_public_visibility == 1){
            acceptAllFollowingRequest($user->id);
        }
        $response = ['is_public_visibility'=>$userData->is_public_visibility];
        return getResponse(111,200,$response);
    }


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



    public function branchIoUrl(Request $request){
            $data_final = [];     
            try{
              
                    $post_id =$request->input('post_id');
                    $postModal=Post::find($post_id);
                    $data['postDesc']='Description';
                    $data['postTitle']='Title';
                    $data['postPreview']='';
                    $data['post_id']=$post_id;
                    $branch_key=  env('BRANCH_IO_KEY');  //piyush 
                    // $branch_key= 'key_live_de18cel0xL79Tw4G2N79MelgrCaI9iSR'; 
                    // $url = env('FCM_URL'); 

                    $value='explore-view/'.$post_id;
                    $dynamic_url = url($value);
                    $receiptData = '{
                        "branch_key": "'.$branch_key.'",
                        "channel": "facebook",
                        "feature": "onboarding",
                        "campaign": "new product",
                        "stage": "new user",
                        "tags": ["one", "two", "three"],
                        "data": {
                        "$canonical_identifier": "content/123",
                        "$og_title": "Isit",
                        "$og_description": "Isit post",
                        "$og_image_url": "'.$dynamic_url.'",
                        "$desktop_url": "'.$dynamic_url.'",
                        "postDesc" :"'.$data['postDesc'].'",
                        "postTitle" :"'.$data['postTitle'].'",
                        "postPreview" : "'.$data['postPreview'].'",
                        "post_id" :"'.$data['post_id'].'"

                        }
                    }';
                        
                    
                    $url = "https://api.branch.io/v1/url";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $errno = curl_errno($ch);
                    $response = curl_exec($ch);
                    $curl_getinfo = curl_getinfo($ch);
                    curl_close($ch);
                    $receiptInfo = json_decode($response,true);
                    if(isset($receiptInfo['url'])){
                        $url = $receiptInfo['url'];
                    }else{
                        $url = '';
                    }       
                    $data_final['url'] = $url;
                
            }catch(\Exception $e){
                $this->optionalData['message'] = $e->getMessage();
                return getResponse(6000,403,$data_final,$this->optionalData);
            }
            return getResponse(103,200,$data_final);

        }


        
    

    public function userGoLive(Request $request){
    	// prd($request->all());
        $user = Auth::user();
        $postId = 0;
        $title = '';
        // echo $user->id;exit;
        if(empty($request->is_publisher) && empty($request->user_id)){ throw new \Exception(__('api-messages.6000')); }
        // echo $user->id;exit;
        $isPublisher = $request->is_publisher;
        $userData = User::where([['id', $request->user_id]])->first();
        $eventName = $userData->first_name;
        $fullName = trim($userData->first_name).' '.($userData->last_name);
        $eventId = $request->user_id;
        $userId = $request->user_id;
        $loggedUserId = $user->id;
        $channelName = $eventId.'_'.$eventName;
        $token = getAgoraToken($eventName,$eventId,$loggedUserId,$isPublisher,3600);
        
        if($isPublisher==2 && $userData->is_live==0){
            throw new \Exception('Broadcaster already stop stream.');
        }
        $userData->is_live = 1;
        if($isPublisher == 1){
            $postName = $title = $request->title; 
            $postId = createPost($eventId,$postName,$postName,5);
            $userData->live_post_id = $postId;
        }else{
            $postId = $userData->live_post_id;
            $title = getPostName($userData->live_post_id);
        }
        $RecToken =1;
        if($request->is_recorded == 1){
            $RecToken = getAgoraToken($eventName,$eventId,1,0,3600);
        }
        $userData->is_live = 1;
        if($isPublisher == 1){
            $postName = $title = $request->title;
            $isRecorded = $request->is_recorded;
            $postId = createPost($eventId,$postName,'',5,$isRecorded);
            $userData->live_post_id = $postId;
            // if recording is yes
        }else{
            $postId = $userData->live_post_id;
            $title = getPostName($userData->live_post_id);
        }
        $userData->save();
        $response = ['token'=>$token,'recToken'=>$RecToken,'channelName'=>$channelName,'postId'=>$postId,'title'=>($title == NULL ? "" : $title),'live_user_count'=>$userData->live_user_count];

        
      
        $response['post_id']=$postId;
        $response['broadcast'] = view('frontend.agora.broadcast',$response)->render();
        $response['audience_html'] = view('frontend.agora.audience',$response)->render();
        $response['is_live'] =  $userData->is_live;
        if($request->is_publisher == 1){
            sendLiveNotificationToFollowers($fullName,$loggedUserId);
        }
        return getResponse(103,200,$response);
    }



    public function userStopLive(Request $request){
        $user = Auth::user();
        $response = [];
        try{
            $user = Auth::user();
            // echo $user->id;exit;
            $userData = User::where([['id', $user->id]])->first();
            $userData->is_live = 0;
            $userData->live_post_id = NULL;
            $userData->live_user_count = 0;
            $userData->save();
            $response = ['is_live'=>$userData->is_live];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(661,200,$response);
    }

    public function updateUserLiveCount(Request $request){
        //prd($request->all());
        $user = Auth::user();
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
    
    // public function sendWebNotification(Request $request)
    // {
    //     $url = 'https://fcm.googleapis.com/fcm/send';
    //     $user = User::find(146);
    //     // dd($user);
    //     // $FcmToken = User::whereNotNull('device_id')->pluck('device_id')->all();
    //     $serverKey = env('FCM_SERVER_KEY');
    //     $json_arr['payload']=[
    //         'RoomStatus' => 'completed',
    //         'RoomType' => 'group',
    //         'RoomSid' => 'RMcb2838afc47318b6303be816d396115d',
    //         'RoomName' => 'local_1',
    //         'RoomDuration' => '237',
    //         'SequenceNumber' => '5',
    //         'StatusCallbackEvent' => 'room-start',
    //         'Timestamp' => '2022-08-31T13:01:24.005Z',
    //         'AccountSid' => 'AC_REDACTED',
    //     ];
    //     $result= sendFcmPushNotification($user['device_id'],'You have a call?',$json_arr,3);
    //     // $result= sendFcmPushNotification($user['device_id'],'You have a call?',[],3);
    //     return $result;  
    // }

    public function storeToken(Request $request)
    {
        
            $user = Auth::guard('web')->user();
            $user->device_id =$request->device_id;
            $user->device_type =$request->device_type;
            $user->save();
            return response()->json([$user]);
    }


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
  

    public function getUserLiveList(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');        
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $page = $request->page;
        $paginate = 10;
        $user_id=$user->id;
        try{
            if(empty($user->id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $liverUserListing = getUserFollowerAndFollowingList($user_id,1);
            $slice = array_slice($liverUserListing->toArray(), $paginate * ($page - 1), $paginate);
            $result = json_decode(json_encode($slice), true);
            // prd($liverUserListing);
            $response['users'] = $result;
            
        // return getResponse(103,200,$response);  
        
            $response['title']="Live Users";
            return view('frontend.agora.agora-live-users',$response); 
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
    }
    
    public function startAgoraRecording(Request $request){
        $response = [];
        $user = Auth::user();
        try{
            if(empty($request->channelName) && empty($request->agoraToken)){ throw new \Exception(__('api-messages.6000')); }
            $recordingUid = 1;
            $resourceId = agoraRecAcuireToken($request->channelName,$request->agoraToken,$recordingUid);
            $sid = agoraRecStart($request->channelName,$resourceId,$request->agoraToken,$recordingUid,1);
            $response = ['resourceId'=>$resourceId,'sid'=>$sid];
        }catch(\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
  
    public function stopAgoraRecording(Request $request){
        $response = [];
        $user = Auth::user();
        try{
           
            if(empty($request->channelName) && empty($request->agoraToken) && empty($request->sid) && empty($request->resourceId) && empty($request->postId)){
                throw new \Exception('Error data-missing'); 
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
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        uploadMediaPostFromS3($request->postId,$response);
        return getResponse(668,200,$response);
    }

}