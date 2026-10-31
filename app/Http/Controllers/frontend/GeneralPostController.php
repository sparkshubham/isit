<?php
namespace App\Http\Controllers\frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\backend\AdminPostRequest;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SavedPost;
use App\Http\Requests\Api\PostMediaRequest;
use App\Models\Category;
use App\Models\Country;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;
use App\Http\Requests\Frontend\GeneralPostRequest;
use DB;
use File;
use Auth;
use Storage;
use FFMpeg;
use App\Models\PostLike;
use App\Http\Requests\Api\LikePostRequest;
use App\Models\PostComment;
use App\Http\Requests\Api\SavePostRequest;
use App\Http\Requests\Api\CommentPostRequest;
use App\Http\Requests\Api\LikeCommentRequest;
use App\Models\CommentLike;



class GeneralPostController extends Controller{

    public function __construct(Route $route){
        $this->middleware(function ($request, $next) use ($route){
            // Getting controller function
            $routeNameStr = $route->getActionName();
            $routeName = explode('@',$routeNameStr);

            $moduleName = request()->segment(2);
            return $next($request);
        });
    }

    
    public function index(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at')
            ->where(function($query) use($userId){
                $query->where('posts.user_id', $userId);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1');
            })
            ->orderByRaw($postSortBy)
            ->get();            
            $response['posts'] = $posts;
            $response['title'] ='PostBoard';

            return view('frontend.dashboard.dashboard',$response);
        
    }

   
    public function create(Request $request, $id=null)
    {
        return view('frontend.dashboard.dashboard');
    }    

    public function gernalPostStore(Request $request){
        try{
            // dd($request->all());
          
            if($request->filled('hidden_id')){
                $post = Post::findOrFail($request['hidden_id']);
                if(isset($request['deleted_media_ids']) && $request['deleted_media_ids']!=''){
                    
                    $array = array_filter(array_unique($request['deleted_media_ids']));
                    deleteMediaPost($array);
                }
                
                $code = 619;
            }else{
                $post = new Post();
                $code = 618;
            }
            $user = Auth::user();
            $post->user_id = $user->id;
            $post->heading = $request['heading']?$request['heading']:'';
            $post->description = $request['description']?$request['description']:'';
            // $post->post_type =$request['post_type'];
            $post->post_type = 1;
            $request['draft_status']=1;
            $post->draft_status = $request['draft_status'];
            $post->save();
            if(!empty($request->file_media))
            {
            $width = 0;
            $height = 0;
            foreach ($request->file_media as $key => $value) {
                $media_type=getFileTypeUsingName($value);
                $is_default=0;
                if($key==0)
                {
                    $is_default=1;
                }
                $request->merge([
                    'media_type' => $media_type,
                    'post_id'=>$post->id,
                    'media_order'=>$key+1,
                    'is_default'=>$is_default,
                    'width'=>$width,
                    'height'=>$height,
                    'file_name'=>$value,
                ]);
                $this->movePostIntoS3($request);
             }
            }
            if($post){
                $response_type='success';
                if(!empty($request->filled('hidden_id'))){
                    $response_message='Post edited successfully';
                }
                else{
                    $response_message='Post added successfully';
                  
                }
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }
        }
        catch (Exception $e){
            $response_type='error';
            $response_message=$e->getMessage();
        }
        return redirect()->route('post-board')->with($response_type,$response_message);
        
    }

    public function storeMedia(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        $destinationPath = config('constants.temp_post_media.upload_path');
        $file = $request->file('file');
        $extension = $file->extension();
        // $fileType=1;
        $fileType=getFiletype($request->file('file'));
        // dd($request->file('file'));
        $thumbFileName='';
        if($fileType==2){

            $fileName = rand().time().'.'.$extension;
            $pathToUpload = $destinationPath.'/'.$fileName;
            Storage::disk('s3')->put($pathToUpload, file_get_contents($file)); 

            $ffprobe = FFMpeg\FFProbe::create();
            $video = $ffprobe->streams($file)->videos()->first();
            $width = $video->get('width');
            $height = $video->get('height');

            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($file);
            $path = public_path().'/'.config('constants.admin_image.upload_path').'frame.jpg';
            $img = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(3))->save($path);
            $frameImagePath = url(config('constants.admin_image.display_path').'frame.jpg');
            // echo $frameImagePath;exit;


            $thumbFileName = 'thumb_'.rand().time().'.jpg';
            $pathToUpload = $destinationPath.'/'.$thumbFileName;
            Storage::disk('s3')->put($pathToUpload, file_get_contents($frameImagePath));
            if(file_exists($path)){
               unlink($path);
            }
            // dd('sucess');
        }else{
            $fileName = rand().time().'.'.$extension;
            $pathToUpload = $destinationPath.'/'.$fileName;
            Storage::disk('s3')->put($pathToUpload, file_get_contents($file)); 
        }

        return response()->json([
            'name'          => $fileName,
            'thumbFileName' => $thumbFileName,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function movePostIntoS3($request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('post_media_id')){
                $postMedia = PostMedia::findOrFail($request['post_media_id']);
                $code = 617;
            }else{
                $postMedia = new PostMedia();
                $code = 616;
            }

            $destinationPathTmp = config('constants.temp_post_media.upload_path');
            $destinationPathNew = config('constants.post_media.upload_path');
            
            if($request->file_name){
                $file = $request->file_name;
                if($request['media_type'] == 2){
                    $fileName = $request->file_name;
                    $old_path= $destinationPathTmp.'/'.$fileName;
                    $new_path = $destinationPathNew.'/'.$fileName;

                    Storage::disk('s3')->move($old_path, $new_path);
                    $postMedia->file_name = $fileName;    
                       
                    if(!empty($request->thumbFileName))
                    {
                        $thumbFileName = $request->thumbFileName;
                        $old_path_tmp = $destinationPathTmp.'/'.$thumbFileName; 
                        $new_path_tmp = $destinationPathNew.'/'.$thumbFileName;
                        Storage::disk('s3')->move($old_path_tmp, $new_path_tmp);
                        $postMedia->thumb_image_name = $thumbFileName; 
                    }

                }else{

                    $fileName = $request->file_name;
                    $old_path= $destinationPathTmp.'/'.$fileName;
                    $new_path = $destinationPathNew.'/'.$fileName;
                    Storage::disk('s3')->move($old_path, $new_path);
                    $postMedia->file_name = $fileName;
                }
            }            
            $postMedia->media_type = $request['media_type'];
            $postMedia->post_id = $request['post_id'];
            $postMedia->media_order = ($request->filled('media_order') ? $request['media_order'] : 1);
            $postMedia->is_default = ($request->filled('is_default') ? $request['is_default'] : 0);
            $postMedia->width = ($request->filled('width') ? $request['width'] : 0);
            $postMedia->height = ($request->filled('height') ? $request['height'] : 0);
            $save = $postMedia->save();
            $response['post_media_id'] = $postMedia->id;
            
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return $this->optionalData;
        }
        DB::commit();
        return $response;
    }

  


    public function likePost(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            // echo $isDeleted;exit;
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = new PostLike();
            $savedPost['post_id'] = $request->post_id;
            $savedPost['user_id'] = $request->user_id;
            $savedPost->save();
            $response['post_like_id'] = $savedPost->id;

            // Notification Send
            if($postUserId != $request->user_id){
                sendNotificationWithData($request->user_id,$postUserId,1,['post_id'=>$request->post_id,'notification_type'=>1]);
            }

        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(625,200,$response);
    }
  
    public function unlikePost(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->post_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $likedPost = PostLike::where(['post_id'=>$request->post_id,'user_id'=>$request->user_id])->first();
            if(empty($likedPost)){
                return getResponse(621,400);
            }
            $likedPost->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(626,200,$response);
    }
   
    public function getLikedUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->post_id)){ throw new \Exception(__('api-messages.6000')); }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }

            $likeSortBy = 'id desc';
            $postId = $request->post_id;
            $users = PostLike::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('posts_likes.id','posts_likes.user_id','posts_likes.post_id','posts_likes.created_at')
            ->where(function($query) use($postId){
                $query->where('posts_likes.post_id', $postId);
            })
            ->orderByRaw($likeSortBy)
            ->paginate(100);
    
            // $NewUsers = $users->getCollection()->transform(function ($value) {
            //     // Your code here
            //     $arr = Array();
            //     if(isset($value['getUser'])){
            //         $arr = $value['getUser'];
            //         $arr['createdAt'] = $value['created_at'];
            //     }
            //     return $arr;
            // });
            // $NewUsers = $NewUsers->filter(function ($value, $key) {
            //     return $value != null;
            // })->values();
          
            $response['users'] = $users;
            if(count($users->toArray())==0)
            {
               return false;
            }
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

            if($request->ajax()){
                $view = view('frontend.dashboard.likes-list',$response)->render();
                return response()->json(['htmlData'=>$view]);
            }
            $view =view('frontend.dashboard.likes-list',$response)->render(); 
            return response()->json(['htmlData'=>$view]);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }

       
    }


    public function commentPost(CommentPostRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = new PostComment();
            $savedPost['post_id'] = $request->post_id;
            $savedPost['user_id'] = $request->user_id;
            $savedPost['parent_comment_id'] = $request->comment_id ? $request->comment_id : NULL;
            $savedPost['comment'] = $request->comment;
            $savedPost->save();
            $response['post_comment_id'] = $savedPost->id;
            /* if(empty($request->comment_id)){
                $post = PostComment::findOrFail($request->comment_id);
                $post->child_comment_id = $response['post_comment_id'];
                $post->save();
            } */
            
            if($savedPost['parent_comment_id'] == NULL){
                if($postUserId != $request->user_id){
                    $postType = getPostType($request->post_id);
                    // echo $postType;exit;
                    // Stop sending comment notification for live user and event
                    if($postType != 4 && $postType != 5){
                        sendNotificationWithData($request->user_id,$postUserId,2,['post_id'=>$request->post_id,'notification_type'=>2]);
                    }
                }
            }else if(!empty($request->comment_id)){
                $commentPost = PostComment::where(['id'=>$request->comment_id])->first();
                // prd($commentPost);
                // PostUserId
                // Comment User -> POwn post comment replied
                if(($postUserId != $request->user_id) && $commentPost->user_id != $postUserId){
                    $postType = getPostType($request->post_id);
                    // Stop sending comment notification for live user and event
                    if($postType != 4 && $postType != 5){
                        sendNotificationWithData($request->user_id,$postUserId,11,['post_id'=>$request->post_id,'notification_type'=>11]);
                    }
                }
                // Don't send notitifcation to user if user blocked vice versa
                // $isBlocked = getBlockByUser($commentPost->user_id,$request->user_id);
                // if(!$isBlocked)
                {
                    if($postUserId != $request->user_id && $commentPost->user_id != $request->user_id){
                        $postType = getPostType($request->post_id);
                        // Stop sending comment notification for live user and event
                        if($postType != 4 && $postType != 5){
                            sendNotificationWithData($request->user_id,$commentPost->user_id,3,['post_id'=>$request->post_id,'notification_type'=>3]);
                        }
                    }else if($commentPost->user_id != $request->user_id){
                        $postType = getPostType($request->post_id);
                        // Stop sending comment notification for live user and event
                        if($postType != 4 && $postType != 5){
                            sendNotificationWithData($request->user_id,$commentPost->user_id,3,['post_id'=>$request->post_id,'notification_type'=>3]);
                        }
                    }
                }
                // if(($postUserId != $commentPost->user_id && ($postUserId != $request->user_id))){
                // }else if(($commentPost->user_id != $request->user_id)){
                //     sendNotificationWithData($request->user_id,$commentPost->user_id,3,['post_id'=>$request->post_id,'notification_type'=>3]);
                // }
            }

            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(630,200,$response);
    }

    public function removeCommentPost(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->comment_id) || empty($request->user_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $commentPost = PostComment::where(['id'=>$request->comment_id,'user_id'=>$request->user_id])->first();
            if(empty($commentPost)){
                return getResponse(112,400);
            }
            $commentPost->deleted_at = date('Y-m-d H:i:s');
            $commentPost->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(631,200,$response);
    }
   

    public function getPostCommentListing(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->post_id)){ throw new \Exception(__('api-messages.6000')); }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $likeSortBy = 'id desc';
            if(!empty($request->orderBy))
            {
                $likeSortBy= 'id asc';
            }
            
            $postId = $request->post_id;
            $comments = PostComment::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                },
                'replies'=>function($query){
                    $query->select(['posts_comments.id','posts_comments.user_id','posts_comments.post_id','posts_comments.comment']);
                }
            ])
            ->select('posts_comments.id','posts_comments.user_id','posts_comments.post_id','posts_comments.created_at','posts_comments.comment','posts_comments.parent_comment_id','posts_comments.tag_json')
            ->where(function($query) use($postId){
                $query->where('posts_comments.post_id', $postId);
                // $query->where('posts_comments.parent_comment_id', 2);
                $query->whereRaw('posts_comments.parent_comment_id IS NULL');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($likeSortBy)
            ->paginate(100);
            // ->toArray();
            // prd($comments);
            $NewComments = $comments->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr['getUser'] = $value['getUser'];
                    $arr['createdAt'] = $value['created_at'];
                    $arr['comment'] = $value['comment'];
                    $arr['tag_json'] = $value['tag_json'];
                    $arr['comment_id'] = $value['id'];
                    $arr['total_replied'] = $value['total_replied'];
                    $arr['total_likes'] = $value['total_likes'];
                    $arr['is_liked'] = $value['is_liked'];
                }
                return $arr;
            });
            $NewComments = $NewComments->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['comments'] = $NewComments->toArray();
            $response['live_user_count'] = getPostLiveUserCount($postId);
            $response['post_id']=$postId;
            // $response['paging'] = [
            //     'total_item'=>$comments->total(),
            //     'item_per_page'=>$comments->perPage(),
            //     'current_page'=>$comments->currentPage(),
            //     'last_page'=>$comments->lastPage(),
            //     'prev'=>$comments->previousPageUrl(),
            //     'next'=>$comments->nextPageUrl(),
            //     'first'=>$comments->url(1),
            //     'last'=>$comments->url($comments->lastPage()),
            // ];

         

        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        // return getResponse(103,200,$response)

        $view =view('frontend.dashboard.comment-list',$response)->render();
        $agora_comment_html =view('frontend.agora.agora-comment',$response)->render();
          
        // if($request->ajax()){
        //     $view =view('frontend.dashboard.comment-list',$response)->render();
        //     $agora_comment_html =view('frontend.agora.agora-comment',$response)->render();
        //     return response()->json(['html_comment'=>$view,'agora_comment_html'=>$agora_comment_html]);
        // }
        
        return response()->json(['htmlData'=>$view,'agora_comment_html'=>$agora_comment_html,'response'=>$response]);
    }

    public function getRepliedComments(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->post_id) || empty($request->comment_id)){ throw new \Exception(__('api-messages.6000')); }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $likeSortBy = 'id asc';
            $postId = $request->post_id;
            $commentId = $request->comment_id;
            $comments = PostComment::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                    
                },
                'replies'=>function($query){
                    $query->select(['posts_comments.id','posts_comments.user_id','posts_comments.post_id','posts_comments.comment']);
                }
            ])
            ->select('posts_comments.id','posts_comments.user_id','posts_comments.post_id','posts_comments.created_at','posts_comments.comment','posts_comments.parent_comment_id','posts_comments.tag_json')
            ->where(function($query) use($postId,$commentId){
                $query->where('posts_comments.post_id', $postId);
                $query->where('posts_comments.parent_comment_id', $commentId);
            })
            ->orderByRaw($likeSortBy)
            ->paginate(100);
            // ->toArray();
            // prd($comments);
            $NewComments = $comments->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr['get_user'] = $value['getUser'];
                    $arr['createdAt'] = $value['created_at'];
                    $arr['comment'] = $value['comment'];
                    $arr['tag_json'] = $value['tag_json'];
                    $arr['comment_id'] = $value['id'];
                    $arr['total_likes'] = $value['total_likes'];
                    $arr['is_liked'] = $value['is_liked'];
                }
                return $arr;
            });
            $NewComments = $NewComments->filter(function ($value, $key) {
                return $value != null;
            })->values();
            $response['comments'] = $NewComments->toArray();
            $response['live_user_count'] = getPostLiveUserCount($postId);
            $response['paging'] = [
                'total_item'=>$comments->total(),
                'item_per_page'=>$comments->perPage(),
                'current_page'=>$comments->currentPage(),
                'last_page'=>$comments->lastPage(),
                'prev'=>$comments->previousPageUrl(),
                'next'=>$comments->nextPageUrl(),
                'first'=>$comments->url(1),
                'last'=>$comments->url($comments->lastPage()),
            ];
          
                if($request->ajax()){
                    $view =view('frontend.dashboard.reply-comment',$response)->render(); 
                    return response()->json(['htmlData'=>$view]);
                }
                $view =view('frontend.dashboard.reply-comment',$response)->render(); 
                return response()->json(['htmlData'=>$view]); 
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        
        // return getResponse(103,200,$response);
    }
    

    public function deletePost(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->post_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $post = Post::findOrFail($request->post_id);
            if(empty($post)){
                return getResponse(112,400);
            }
            $post->deleted_at = date('Y-m-d H:i:s');
            $post->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(629,200,$response);
    }


    public function unsavePost(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->post_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = SavedPost::where(['post_id'=>$request->post_id,'user_id'=>$request->user_id])->first();
            if(empty($savedPost)){
                return getResponse(621,400);
            }
            $savedPost->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(622,200,$response);
    }


    public function savePost(SavePostRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedPost = new SavedPost();
            $savedPost['post_id'] = $request->post_id;
            $savedPost['user_id'] = $request->user_id;
            $savedPost->save();
            $response['save_post_id'] = $savedPost->id;
            // Notification Send
            // sendNotificationWithData($request->user_id,$postUserId,8,['post_id'=>$request->post_id,'notification_type'=>8]);

        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(620,200,$response);
    }

    
    public function likeComment(LikeCommentRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $postId = getPostIdComment($request->comment_id);
            $isBlockedAdmin = isPostBlockByAdmin($postId);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($postId);
            // echo $isDeleted;exit;
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $commentUserId = getCommentUserId($request->comment_id);
            $isBlocked = getBlockByUser($user->id,$commentUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $savedComment = new CommentLike();
            $savedComment['comment_id'] = $request->comment_id;
            $savedComment['user_id'] = $request->user_id;
            $savedComment->save();
            $response['comment_like_id'] = $savedComment->id;
            // Notification Send
            if($commentUserId != $request->user_id){
                // sendNotificationWithData($request->user_id,$commentUserId,19,['post_id'=>$postId,'comment_id'=>$request->comment_id,'notification_type'=>19]);
            }
        } catch (\Exception $e){
            // prd($e);
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(662,200,$response);
    }
 
    public function unlikeComment(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->comment_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $postId = getPostIdComment($request->comment_id);
            $isBlockedAdmin = isPostBlockByAdmin($postId);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            
            $isDeleted = isPostDeleted($postId);
            if($isDeleted == 1){
                return getResponse(632,400);
            }            
            $commentUserId = getCommentUserId($request->comment_id);
            // echo $commentUserId;exit;
            $isBlocked = getBlockByUser($user->id,$commentUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $likedComment = CommentLike::where(['comment_id'=>$request->comment_id,'user_id'=>$request->user_id])->first();
            if(empty($likedComment)){
                return getResponse(664,400);
            }
            $likedComment->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(663,200,$response);
    }
  
    public function getCommentLikedUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->comment_id)){ throw new \Exception(__('api-messages.6000')); }
            $postId = getPostIdComment($request->comment_id);
            $isBlockedAdmin = isPostBlockByAdmin($postId);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($postId);
            if($isDeleted == 1){
                return getResponse(632,400);
            }

            $likeSortBy = 'id desc';
            $commentId = $request->comment_id;
            $users = CommentLike::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('comments_likes.id','comments_likes.user_id','comments_likes.comment_id','comments_likes.created_at')
            ->where(function($query) use($commentId){
                $query->where('comments_likes.comment_id', $commentId);
            })
            ->orderByRaw($likeSortBy)
            ->paginate(10);
            // ->toArray();
            // prd($users);
            $NewUsers = $users->getCollection()->transform(function ($value) {
                // Your code here
                $arr = Array();
                if(isset($value['getUser'])){
                    $arr = $value['getUser'];
                    $arr['createdAt'] = $value['created_at'];
                }
                return $arr;
            });
            $NewUsers = $NewUsers->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);likes-list
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
        // return getResponse(103,200,$response);
        $view =view('frontend.dashboard.comment-likes-list',$response)->render(); 
        return response()->json(['htmlData'=>$view]);
    }


    

    public function getPost(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->post_id)){ throw new \Exception(__('api-messages.6000')); }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                return getResponse(632,400);
            }
            $postId = $request->post_id;
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
            ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at','posts.no_of_view')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(posts.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($postId){
                $query->where('posts.id', $postId);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.post_type NOT IN (4,5) AND posts.admin_status=1');
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->first();
            //var_dump($posts->getUser->is_block);
            if(empty($posts) || $posts == null){
                return getResponse(621,400);
            }else if($posts->getUser->is_block == 1){

                return getResponse(638,400);
            }
            $postArr = $posts->toArray();
            $response['data'] = $postArr;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
}


