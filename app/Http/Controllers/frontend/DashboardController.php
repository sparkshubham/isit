<?php


namespace App\Http\Controllers\frontend;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CmsPages;
use Auth;
use App\Http\Controllers\Controller;
use DB;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SavedPost;
use App\Models\PostLike;
use App\Models\UserFollow;
use App\Models\PostComment;
use App\Models\ReportedPost;
use App\Models\Event;
use App\Models\Product;
use App\Models\UserNotification;
use App\Models\UserHelp;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth', ['except' => ['verfiyMail','termsOfUse','privacyPolicy','aboutUs']]);
    }
    /**
     * @summary to get message email verification
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2019
     */
    public function verfiyMail($verifyKey)
    {
        $user = User::select(['id','verifiy_token','first_name','last_name'])->whereRaw('verifiy_token="'.$verifyKey.'"')->first();
        if(empty($user)){
            $code = 605;
        }else{
            $code = 606;      
            $user->is_verified = 1;
            $user->verifiy_token = '';
            $fullName = $user->first_name.' '.$user->last_name;
            $profileImageUrl = '';
            // echo $fullName;exit;
            $user->twilio_s_id = createUserTwillio($user->id,$fullName,$profileImageUrl);
            $user->save();
        }
        $message = __('api-messages.'.$code);
        return view('home.verify-mail',[
            'message' => $message
        ]);
    }
    /**
     * @summary to get terms of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function termsOfUse(){
        $content = "Terms and Condition Page Comming Soon!";
        $cms = CmsPages::select(['id','description'])->whereRaw('title="TERMS AND CONDITIONS"')->first();
        if(!empty($cms)){
            $content = $cms->description;
        }
        return view('frontend.layouts.terms_of_uses',[
            'content' => $content,
            'title' => __('language.terms_of_uses'),
        ]);
    }
    /**
     * @summary to get privacy of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function privacyPolicy(){
        $content = "Privacy Policy Page Comming Soon!";
        $cms = CmsPages::select(['id','description'])->whereRaw('title="PRIVACY POLICY"')->first();
        if(!empty($cms)){
            $content = $cms->description;
        }
        return view('frontend.layouts.privacy_policy',[
            'content' => $content,
            'title' => __('language.privacy_policy'),
        ]);
    }
    /**
     * @summary to get about us of use page
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 09 Dec 2021
     */
    public function aboutUs(){
        Auth::logout();
        $cms = CmsPages::select(['id','description'])->whereRaw('title="ABOUT US"')->first();
        if(!empty($cms)){
            echo $cms->description;
        }else{
            echo "Privacy Policy Page Comming Soon!";
        }
    }


    public function getPostsBoard(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $dailtDevotionalArr = Array();
        try{
            $postSortBy = 'posts.created_at desc';
            $userId = $user->id;
            $page = 1;
            if(!empty($request->page))
            {
                $page = $request->page;
            }
            
          
            $paginate = 10;
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            // echo $userId;exit;
            // getDailyDevotionalPost(2);
            //echo $userId;exit;
            // $userId = 2;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            // echo $blockUserIds;exit;
            $posts = DB::table('users_follows')
                ->join('users', function ($join) {
                    $join->on('users_follows.follower_id', '=', 'users.id');
                })
                ->join('posts', function ($join) use($blockUserIds){
                    $join->on('posts.user_id', '=', 'users.id')->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.admin_status=1 AND posts.post_type != 3 AND posts.user_id NOT IN ('.$blockUserIds.')');
                })
                ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                // ->leftJoin('posts_media', 'posts.id', '=', 'posts_media.post_id')
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at',
                        'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                        'categories.name as category_name','categories.id as categoryId','posts.post_type')
                // ->whereNotIn('users_follows.follower_ids', [$blockUserIds])
                ->where(function($query) use($userId){
                    $query->orWhereRaw('posts.user_id IN ('.$userId.') AND posts.post_type!=3');
                    $query->orWhereRaw("users_follows.user_id = ".$userId." AND users_follows.status = 1");
                    // to show the post on post board logged in user
                    
                })
                /* ->where(function($query) use($userId){
                    // $query->whereRaw('users.is_public_visibility = 1');
                }) */
                ->orderByRaw($postSortBy)
                ->groupBy('posts.id');
                // ->paginate(10)
                // ->get();
                // prd($posts);
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at',
                'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                'categories.name as category_name','categories.id as categoryId','posts.post_type')
                            ->whereRaw("user_id = ".$userId." AND posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type!=3 AND posts.admin_status=1")
                            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                            ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                            ->union($posts)
                            ->orderByRaw('createdAt DESC')
                            ->get();
                //prd($users->count());
                $slice = array_slice($users->toArray(), $paginate * ($page - 1), $paginate);
                $result = json_decode(json_encode($slice), true);
                // prd($result);
                $finalResult = Array();
                if($page == 1){
                    $dailtDevotionalArr = getDailyDevotionalPost(1);
                    if(!empty($dailtDevotionalArr))
                        $finalResult[0] = $dailtDevotionalArr;                
                }
                foreach($result as $key => $value){
                    $arr = Array();
                    $post = Post::findOrFail($value['postID']);
                    // $arr = new stdClass();
                    $arr['id'] = $value['postID'];
                    $arr['user_id'] = $value['userID'];
                    $arr['heading'] = $value['heading'];
                    $arr['description'] = $value['description'];
                    $arr['post_type'] = $value['post_type'];
                    $arr['country_id'] = $value['country_id'];
                    $arr['category_id'] = $value['category_id'];
                    $arr['city'] = $value['city'];
                    $arr['zip_code'] = $value['zip_code'];
                    $arr['is_saved'] = $post->is_saved;
                    $arr['is_liked'] = $post->is_liked;
                    $arr['total_likes'] = $post->total_likes;                
                    $arr['is_commented'] = $post->is_commented;
                    $arr['total_comments'] = $post->total_comments;                
                    $arr['is_reported'] = $post->is_reported;
                    $arr['created_at'] = strtotime($value['created_at']);
                    $arr['updated_at'] = strtotime($value['updated_at']);
                    if($value['country_name']!=''){
                        $arr['country']['name'] = $value['country_name'];
                        $arr['country']['id'] = $value['country_id'];
                    }else{
                        $arr['country'] = null;
                    }
                    if($value['category_name']!=''){
                        $arr['category']['name'] = $value['category_name'];
                        $arr['category']['id'] = $value['category_id'];
                    }else{
                        $arr['category'] = null;
                    }
                    if($value['userID']!='' && $value['userID']!=null){
                        $arr['get_user']['id'] = $value['userID'];
                        $arr['get_user']['first_name'] = $value['first_name'];
                        $arr['get_user']['last_name'] = $value['last_name'];
                        $arr['get_user']['profile_picture'] = $value['profile_picture'];
                    }else{
                        $arr['get_user'] = null;
                    }
                    $getMedia = $post->getMedia()->get()->toArray();
                    if(!empty($getMedia)){
                        $arr['get_media'] = $getMedia;
                    }else{
                        $arr['get_media'] = [];
                    }
                    if(empty($dailtDevotionalArr))
                        $finalResult[] = $arr;
                    else
                        $finalResult[($key+1)] = $arr;
                }                
            $response['posts'] = $finalResult;
            /********* Events Data */
            if($page == 1){
                $eventSortBy = 'date asc';
                $search_keyword = '';
                $userId = $user->id;
                $events = Event::with([
                    'getUser'=>function($query){
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    }
                ])
                ->select('events.id','events.name','events.user_id','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
                ->where(function($query) use($userId,$search_keyword){
                    $query->whereRaw('events.user_id NOT IN ('.$userId.')');
                    $query->where('events.admin_status', 1);
                    $query->whereRaw('events.deleted_at IS NULL');
                    // $query->whereRaw('events.date >= "'.gmdate('Y-m-d').'"');
                    $query->whereRaw('events.start_time >= "'.gmdate('Y-m-d H:i:s').'"');
                    // $query->whereRaw('events.end_time >= "'.gmdate('Y-m-d H:i:s').'"');
                    if($search_keyword!=''){
                        $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                        });
                    }
                })
                ->orderByRaw($eventSortBy)
                ->paginate(10);
                $response['events'] = $events->items();
            }
            /********* */
            /********* Products Data */
            if($page == 1){
                $productSortBy = 'id desc';
                $userId = $user->id;
                $productType = 0;
                $blockUserIds = getBlockUserIds($userId);
                // echo $blockUserIds;exit;
                if($blockUserIds == ''){
                    $blockUserIds = "''";
                }
                $products = Product::with([
                    'Country'=>function($query){
                        $query->select(['countries.id','countries.name']);
                    },
                    'Category'=>function($query) use($search_keyword){
                        $query->select(['categories.id','categories.name']);
                    },
                    'getMedia'=>function($query){
                        $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                    },
                    'getUser'=>function($query){
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    }
                ])
                ->select('products.id','products.name','products.user_id','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.status','products.price_type','products.price','products.currency')
                ->orWhere(function ($query) use ($search_keyword) {
                    $query->with('Category')->whereHas('Category', function ($query) use ($search_keyword) {
                        $query->whereRaw('LOWER(categories.name) like "%' . strtolower($search_keyword) . '%"');
                    });
                })
                ->where(function($query) use($userId,$productType,$search_keyword,$blockUserIds){
                    $query->whereRaw('products.user_id NOT IN ('.$userId.') AND products.user_id NOT IN ('.$blockUserIds.')');
                    if($productType !=0){
                        if($productType == 1){
                            $productType = 2;
                        }else if($productType == 2){
                            $productType = 1;
                        }
                        $query->where('products.product_type', $productType);
                    }
                    $query->where('products.status', 1);
                    $query->whereRaw('products.deleted_at IS NULL');
                })
                ->orWhere(function($query) use($search_keyword){
                    if($search_keyword!=''){
                        $query->orWhere(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(products.name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.description) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.city) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.zip_code) like "%'.strtolower($search_keyword).'%"');
                        });
                    }
                })
                ->orderByRaw($productSortBy)
                ->paginate(10);
                $response['products'] = $products->items();            
            }   
            /********* Products Data */
            if($page == 1){
                $response['unreadNotificationCount'] = getUserNotificationUnreadCount($user->id);
            }
            if($page == 1){
                $request->merge([
                    'user_id' => $userId
                ]);
                $followingList=app(\App\Http\Controllers\frontend\ProfileController::class);
                $response['following']=$followingList->followingList($request);
            }

            if($page==1)
            {
                $evetsGets=app(\App\Http\Controllers\AgoraVideoController::class);
                $response['available_events']=$evetsGets->getAvailableEvents($request);
                // $response['available_events']=[];

                //  dd($evetsGets->getAvailableEvent($request));

            }
            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
            $response['title'] = 'Post-board';

            if($request->ajax()){
                $view = view('frontend.dashboard.dashboard-mid',$response)->render();
                return response()->json(['html'=>$view]);
            }
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        
        return view('frontend.dashboard.dashboard',$response);

    }


    public function getPostsBoardNew(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $productFolderName = config('constants.product_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['product_media'] = getS3Url($productFolderName);
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $dailtDevotionalArr = Array();
        try{
            $postSortBy = 'posts.created_at desc';
            $userId = $user->id;
            // $page = $request->page;
            $page = 1;
            if(!empty($request->page))
            {
                $page = $request->page;
            }
            $paginate = 10;
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            // echo $userId;exit;
            // getDailyDevotionalPost(2);
            //echo $userId;exit;
            // $userId = 2;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            // echo $blockUserIds;exit;
            $posts = DB::table('users_follows')
                ->join('users', function ($join) {
                    $join->on('users_follows.follower_id', '=', 'users.id')->whereRaw('users.deleted_at IS NULL');
                })
                ->join('posts', function ($join) use($blockUserIds){
                    $join->on('posts.user_id', '=', 'users.id')->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.admin_status=1 AND posts.post_type NOT IN (2,3,4,5) AND posts.user_id NOT IN ('.$blockUserIds.')');
                })
                ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                // ->leftJoin('posts_media', 'posts.id', '=', 'posts_media.post_id')
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.username',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at',
                        'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                        'categories.name as category_name','categories.id as categoryId','posts.post_type')
                // ->whereNotIn('users_follows.follower_ids', [$blockUserIds])
                ->where(function($query) use($userId){
                    $query->orWhereRaw('posts.user_id IN ('.$userId.') AND posts.post_type NOT IN(2,3,4,5) AND users.deleted_at IS NULL');
                    $query->orWhereRaw("users_follows.user_id = ".$userId." AND users_follows.status = 1");
                    // to show the post on post board logged in user
                    
                })
                /* ->where(function($query) use($userId){
                    // $query->whereRaw('users.is_public_visibility = 1');
                }) */
                ->orderByRaw($postSortBy)
                ->groupBy('posts.id');
                // ->paginate(10)
                // ->get();
                // prd($posts);
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.username',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at',
                'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                'categories.name as category_name','categories.id as categoryId','posts.post_type')
                            ->whereRaw("user_id = ".$userId." AND users.deleted_at IS NULL AND posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type NOT IN(2,3,4,5) AND posts.admin_status=1")
                            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                            ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                            ->union($posts)
                            ->orderByRaw('createdAt DESC')
                            ->get();
                //prd($users->count());
                $slice = array_slice($users->toArray(), $paginate * ($page - 1), $paginate);
                $result = json_decode(json_encode($slice), true);
                // prd($result);
                $finalResult = Array();
                if($page == 1){
                    $dailtDevotionalArr = getDailyDevotionalPost(1);
                    if(!empty($dailtDevotionalArr))
                        $finalResult[0] = $dailtDevotionalArr;                
                }
                foreach($result as $key => $value){
                    $arr = Array();
                    $post = Post::findOrFail($value['postID']);
                    // $arr = new stdClass();
                    $arr['id'] = $value['postID'];
                    $arr['user_id'] = $value['userID'];
                    $arr['heading'] = $value['heading'];
                    $arr['description'] = $value['description'];
                    $arr['post_type'] = $value['post_type'];
                    $arr['country_id'] = $value['country_id'];
                    $arr['category_id'] = $value['category_id'];
                    $arr['city'] = $value['city'];
                    $arr['zip_code'] = $value['zip_code'];
                    $arr['is_saved'] = $post->is_saved;
                    $arr['is_liked'] = $post->is_liked;
                    $arr['total_likes'] = $post->total_likes;                
                    $arr['is_commented'] = $post->is_commented;
                    $arr['total_comments'] = $post->total_comments;                
                    $arr['is_reported'] = $post->is_reported;
                    $arr['no_of_view'] = $post->no_of_view;
                    $arr['created_at'] = strtotime($value['created_at']);
                    $arr['updated_at'] = strtotime($value['updated_at']);
                    if($value['country_name']!=''){
                        $arr['country']['name'] = $value['country_name'];
                        $arr['country']['id'] = $value['country_id'];
                    }else{
                        $arr['country'] = null;
                    }
                    if($value['category_name']!=''){
                        $arr['category']['name'] = $value['category_name'];
                        $arr['category']['id'] = $value['category_id'];
                    }else{
                        $arr['category'] = null;
                    }
                    if($value['userID']!='' && $value['userID']!=null){
                        $arr['get_user']['id'] = $value['userID'];
                        $arr['get_user']['first_name'] = $value['first_name'];
                        $arr['get_user']['last_name'] = $value['last_name'];
                        $arr['get_user']['profile_picture'] = $value['profile_picture'];
                        $arr['get_user']['twilio_s_id'] = $value['twilio_s_id'];
                        $env = env("APP_ENV");
                        $arr['get_user']['twillio_identity'] = $env.'_'.$value['userID'];;
                    }else{
                        $arr['get_user'] = null;
                    }
                    $getMedia = $post->getMedia()->get()->toArray();
                    if(!empty($getMedia)){
                        $arr['get_media'] = $getMedia;
                    }else{
                        $arr['get_media'] = [];
                    }
                    if(empty($dailtDevotionalArr))
                        $finalResult[] = $arr;
                    else
                        $finalResult[($key+1)] = $arr;
                }                
            $response['posts'] = $finalResult;
            /********* Events Data */
            if($page == 1){
                $eventSortBy = 'id desc';
                $search_keyword = '';
                $userId = $user->id;
                $events = Event::with([
                    'getUser'=>function($query){
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id']);
                    }
                ])
                ->select('events.id','events.name','events.user_id','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
                ->where(function($query) use($userId,$search_keyword){
                    $query->whereRaw('events.user_id NOT IN ('.$userId.')');
                    $query->where('events.admin_status', 1);
                    $query->whereRaw('events.deleted_at IS NULL');
                    $query->whereRaw('events.end_time >= "'.gmdate('Y-m-d H:i:s').'"');
                    if($search_keyword!=''){
                        $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                        });
                    }
                })
                ->whereHas('getUser', function ($query){
                    $query->whereRaw('users.deleted_at IS NULL');
                })
                ->orderByRaw($eventSortBy)
                ->paginate(10);
                $response['events'] = $events->items();
            }
            /********* */
            /********* Products Data */
            if($page == 1){
                $productSortBy = 'id desc';
                $userId = $user->id;
                $productType = 0;
                $blockUserIds = getBlockUserIds($userId);
                // echo $blockUserIds;exit;
                if($blockUserIds == ''){
                    $blockUserIds = "''";
                }
                $products = Product::with([
                    'Country'=>function($query){
                        $query->select(['countries.id','countries.name']);
                    },
                    'Category'=>function($query) use($search_keyword){
                        $query->select(['categories.id','categories.name']);
                    },
                    'getMedia'=>function($query){
                        $query->select(['products_media.id','products_media.product_id','products_media.media_type','products_media.file_name','products_media.media_order','products_media.is_default','products_media.thumb_image_name','products_media.width','products_media.height']);
                    },
                    'getUser'=>function($query){
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id']);
                    }
                ])
                ->select('products.id','products.name','products.user_id','products.description','products.product_type','products.country_id','products.category_id','products.city','products.zip_code','products.user_id','products.created_at','products.updated_at','products.status','products.price_type','products.price','products.currency')
                ->orWhere(function ($query) use ($search_keyword) {
                    $query->with('Category')->whereHas('Category', function ($query) use ($search_keyword) {
                        $query->whereRaw('LOWER(categories.name) like "%' . strtolower($search_keyword) . '%"');
                    });
                })
                ->where(function($query) use($userId,$productType,$search_keyword,$blockUserIds){
                    $query->whereRaw('products.user_id NOT IN ('.$userId.') AND products.user_id NOT IN ('.$blockUserIds.')');
                    if($productType !=0){
                        if($productType == 1){
                            $productType = 2;
                        }else if($productType == 2){
                            $productType = 1;
                        }
                        $query->where('products.product_type', $productType);
                    }
                    $query->where('products.status', 1);
                    $query->whereRaw('products.deleted_at IS NULL');
                })
                ->orWhere(function($query) use($search_keyword){
                    if($search_keyword!=''){
                        $query->orWhere(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(products.name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.description) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.city) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(products.zip_code) like "%'.strtolower($search_keyword).'%"');
                        });
                    }
                })
                ->whereHas('getUser', function ($query){
                    $query->whereRaw('users.deleted_at IS NULL');
                })
                ->orderByRaw($productSortBy)
                ->paginate(10);
                $response['products'] = $products->items();            
            }   
            /********* Products Data */
            if($page == 1){
                $response['unreadNotificationCount'] = getUserNotificationUnreadCount($user->id);
            }
            if($page == 1){
                $request->merge([
                    'user_id' => $userId
                ]);
                $followingList=app(\App\Http\Controllers\frontend\ProfileController::class);
                $response['following']=$followingList->followingList($request);
            }

            if($page==1)
            {
                $evetsGets=app(\App\Http\Controllers\AgoraVideoController::class);
                $response['available_events']=$evetsGets->getAvailableEvents($request);
                // $response['available_events']=[];

                //  dd($evetsGets->getAvailableEvent($request));

            }
            $response['request_user_count'] = UserFollow::where(['follower_id'=>$userId,'status'=>2])->count();
            $response['title'] = 'Post-board';

            if($request->ajax()){
                $view = view('frontend.dashboard.dashboard-mid',$response)->render();
                return response()->json(['html'=>$view]);
            }
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        
        return view('frontend.dashboard.dashboard',$response);
    }


    
    public function help(Request $request){
        $response['title'] = 'Help';
        return view('frontend.user.help',$response);
    }


    public function helpUser(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->category)){
                
                return redirect()->back()->with("error","Category id not present");
            }
            $user = Auth::user();
            $UserHelp = new UserHelp();
            $UserHelp->user_id = $user->id;
            $UserHelp->description = $request['description'];
            $UserHelp->status = 0;
            $UserHelp->category = (empty($request->category) ? '' : $request->category);
            $UserHelp->save();

            if($UserHelp){
                $response_type='success';
                $response_message='Successfully submit your request';
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }
            
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            $response_type='error';
            return redirect()->route('help')->with($response_type,$this->optionalData['message']);
        }
        DB::commit();
        return redirect()->route('help')->with($response_type,$response_message);
    }


    public function reportPost(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->report_post_id)){
                 throw new \Exception(__('api-messages.6000')); 
                }
            $user = Auth::user();
            $postUserId = getPostUserId($request->report_post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $reportedPost = new ReportedPost();
            $reportedPost->user_id = $user->id;
            $reportedPost->post_id = $request['report_post_id'];
            $reportedPost->description = $request['description'];
            $reportedPost->category = (empty($request->category) ? '' : $request->category);
            $reportedPost->save();
            $response['report_post_id'] = $reportedPost->id;
            if($reportedPost){
                $response_type='success';
                $response_message='Successfully report post.';
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            // return getResponse(6000,403,$response,$this->optionalData);
            $response_type='error';
            $response_message=$this->optionalData['message'] ;
            return redirect()->back()->with($response_type,$response_message);
        }
        DB::commit();
        // return redirect()->route('post-board')->with($response_type,$response_message);
        return redirect()->back()->with($response_type,$response_message);
    }


    public function notification(Request $request){
    	$response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $user = Auth::user();
        $request->user_id=$user->id;
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
            ->paginate(100);
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
                if($value->other_id != 0 && ($notification_type == 1 || $notification_type == 2 || $notification_type == 4 || $notification_type == 8 || $notification_type == 3 || $notification_type == 11)){
                    // $arr['postData'] = [];
                    $arr['postData'] = getPostSingleMedia($value->other_id);
                    if(empty($arr['postData'])){
                        unset($arr['postData']);
                    }
                    $arr['post_id'] = $arr['other_id'];
                    unset($arr['other_id']);
                }else if($notification_type == 17){
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
            $response['notifications'] = $NewUsers;
           
            // $response['notifications'] = $NewUsers;
            $response['title'] = 'Notifications';
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        // return getResponse(103,200,$response);
        return view('frontend.user.notification',$response);
    }

}
