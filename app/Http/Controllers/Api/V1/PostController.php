<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SavedPost;
use App\Models\PostLike;
use App\Models\UserFollow;
use App\Models\PostComment;
use App\Models\ReportedPost;
use App\Models\Event;
use App\Models\Product;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use Response;
use Hash;
use DB;
use URL;
use Auth;
use Mail;
use Storage;
use Carbon\Carbon;


use App\Http\Requests\Api\GeneralPostRequest;
use App\Http\Requests\Api\AnointedPostRequest;
use App\Http\Requests\Api\PostMediaRequest;
use App\Http\Requests\Api\SavePostRequest;
use App\Http\Requests\Api\LikePostRequest;
use App\Http\Requests\Api\ReportPostRequest;
use App\Http\Requests\Api\CommentPostRequest;
use App\Http\Requests\Api\LikeCommentRequest;
use App\Http\Requests\Api\PostWithMediaRequest;

class PostController extends Controller
{
    /**
     * @summary : Api for create/update General Posts
     * @param : heading, description, user_id
     * @type : post
     * @return : json
     * @author Ashok Dadhich
     * @created_at 17 Dec 2021
     */
    public function generalPost(GeneralPostRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            $user = Auth::user();
            if($request->filled('id')){
                $post = Post::findOrFail($request['id']);
                if($post->user_id != $user->id){
                    return getResponse(640,404);
                }
                if(isset($request['deleted_media_ids']) && $request['deleted_media_ids']!=''){
                    $array = array_filter(array_unique(explode(',',$request['deleted_media_ids'])));
                    deleteMediaPost($array);
                }
                $code = 619;
            }else{
                $post = new Post();
                $code = 618;
            }
            $post->user_id = $user->id;
            $post->heading = $request['heading'];
            $post->description = $request['description'];
            $post->post_type = 1;
            $post->save();
            $response['post_id'] = $post->id;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @summary : Api for create/update Anointed Posts
     * @type : post
     * @param : heading, description, user_id, category_id, country_id, zipcode, 
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function anointedPost(AnointedPostRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            $user = Auth::user();
            if($request->filled('id')){
                $post = Post::findOrFail($request['id']);
                if($post->user_id != $user->id){
                    return getResponse(640,404);
                }
                if(isset($request['deleted_media_ids']) && $request['deleted_media_ids']!=''){
                    $array = array_filter(array_unique(explode(',',$request['deleted_media_ids'])));
                    deleteMediaPost($array);
                }
                $code = 619;
            }else{
                $post = new Post();
                $code = 618;
            }
            $post->user_id = $user->id;
            $post->heading = $request['heading'];
            $post->description = $request['description'];
            $post->category_id = $request['category_id'];
            $post->country_id = $request['country_id'];
            $post->zip_code = $request['zip_code'];
            $post->city = $request['city'];
            $post->post_type = 2;
            $post->save();
            $response['post_id'] = $post->id;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @summary : Api for upload Post Media
     * @type : post
     * @param : post_id, file, media_type, media_order, is_default
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function uploadPostMedia(PostMediaRequest $request){
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
            $mediaRes = getPostMediaCount($request['post_id']);
            if($mediaRes == 0){
                return getResponse(670,404);
            }
            $destinationPath = config('constants.post_media.upload_path');
            if($request->hasFile('file')){
                $file = $request->file('file');
                if($request['media_type'] == 2){
                    // prd($file[0]->getMimeType());
                    $extension = $file[0]->extension();    
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    // echo $postMedia->file_name;
                    if(isset($postMedia->file_name) && $postMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
                        Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->file_name);
                        // unlink($destinationPath.$postMedia->file_name);
                    }
                    // $file[0]->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file[0]));
                    $postMedia->file_name = $fileName;
                    // echo $postMedia->file_name;exit;
                    // if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif')
                    {
                        $extension = $file[1]->extension();    
                        $fileName = 'thumb_'.rand().time().'.'.$extension;
                        $pathToUpload = $destinationPath.'/'.$fileName;
                        //Move Uploaded File
                        if(isset($postMedia->thumb_image_name) && $postMedia->thumb_image_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->thumb_image_name)){
                            Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->thumb_image_name);
                            // unlink($destinationPath.$postMedia->file_name);
                        }
                        Storage::disk('s3')->put($pathToUpload, file_get_contents($file[1]));
                        // $file[1]->move($destinationPath,$fileName);
                        $postMedia->thumb_image_name = $fileName;
                        
                    }
                }else{
                    
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    if(isset($postMedia->file_name) && $postMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
                        // unlink($destinationPath.$postMedia->file_name);
                        Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->file_name);
                    }
                    // $file->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    $postMedia->file_name = $fileName;

                }
            }            
            $postMedia->media_type = $request['media_type'];
            $postMedia->post_id = $request['post_id'];
            $postMedia->media_order = ($request->filled('media_order') ? $request['media_order'] : 1);
            $postMedia->is_default = ($request->filled('is_default') ? $request['is_default'] : 0);
            $postMedia->width = ($request->filled('width') ? $request['width'] : 0);
            $postMedia->height = ($request->filled('height') ? $request['height'] : 0);
            $postMedia->is_preview_media = ($request->filled('is_preview_media') ? $request['is_preview_media'] : 0);
            $save = $postMedia->save();
            $response['post_media_id'] = $postMedia->id;
        }catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-posts
     * @summary : Get All Post By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function getPosts(Request $request){
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
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height','posts_media.is_preview_media']);
                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at','posts.no_of_view')
            ->where(function($query) use($userId){
                $query->where('posts.user_id', $userId);
                if($userId !=1){
                    $query->whereRaw('posts.deleted_at IS NULL AND posts.post_type NOT IN (3,4,5) AND posts.admin_status=1');
                }else{
                    $query->whereRaw('posts.deleted_at IS NULL AND posts.post_type NOT IN (4,5) AND posts.admin_status=1');
                }
            })
            ->orderByRaw($postSortBy)
            ->paginate(10);
            $response['posts'] = $posts->items();
            
            $response['paging'] = [
                'total_item'=>$posts->total(),
                'item_per_page'=>$posts->perPage(),
                'current_page'=>$posts->currentPage(),
                'last_page'=>$posts->lastPage(),
                'prev'=>$posts->previousPageUrl(),
                'next'=>$posts->nextPageUrl(),
                'first'=>$posts->url(1),
                'last'=>$posts->url($posts->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-post
     * @summary : Get Post By Id
     * @param :
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
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
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height','posts_media.is_preview_media']);
                    $query->orderBy('posts_media.is_preview_media','desc');

                },
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.is_star']);
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
            $postArr['no_of_view'] = addViewCountPost($postArr['id'],$user->id);
            $response['data'] = $postArr;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @summary : Api for explore the general Post with media array
     * @type : post
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 03 Jan 2022
     */
    public function getGeneralExplore(Request $request){
        $response = [];
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->page)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $postSortBy = 'id desc';
            $userId = $user->id;
            // $userId = 2;
            $blockUserIds = getBlockUserIds($userId);
            // echo $blockUserIds;exit;
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $posts = Post::with([
                'getMedia'=>function($query){
                    $query->select(['posts_media.id as media_id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.thumb_image_name','posts_media.width','posts_media.height','posts_media.is_preview_media']);
                },
            ])
            ->select('posts.id','posts.heading','posts.description','posts.user_id')
            ->where(function($query) use($search_keyword,$userId,$blockUserIds){
                $query->where('posts.post_type', 1);
                $query->where('posts.user_id', '<>', $userId);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.user_id NOT IN ('.$blockUserIds.')');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(posts.heading) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(posts.description) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(posts.city) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->whereHas('getMedia', function ($query){
                $query->where('is_default',1);
            })
            ->whereHas('getUser', function ($query){
                $query->where('users.is_public_visibility',1);
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->whereNotIn('posts.user_id', [$blockUserIds])
            ->orderByRaw($postSortBy)
            ->paginate(10);
            $NewPosts = $posts->getCollection()->transform(function ($value) {
                // Your code here
                // prd($value);
                if(isset($value['getMedia'][0])){
                    return $value['getMedia'][0];
                }
            });
            $NewPosts = $NewPosts->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            $response['posts'] = $NewPosts;
            $response['paging'] = [
                'total_item'=>$posts->total(),
                'item_per_page'=>$posts->perPage(),
                'current_page'=>$posts->currentPage(),
                'last_page'=>$posts->lastPage(),
                'prev'=>$posts->previousPageUrl(),
                'next'=>$posts->nextPageUrl(),
                'first'=>$posts->url(1),
                'last'=>$posts->url($posts->lastPage()),
            ];
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @summary : Api for explore the general Post with media array
     * @type : post
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 03 Jan 2022
     */
    public function getAnointedExplore(Request $request){
        $response = [];
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $user = Auth::user();
        $search_keyword = '';
        try{
            if(empty($request->page)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $postSortBy = 'posts.id desc';
            $userId = $user->id;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $posts = Post::with([
                'getMedia'=>function($query){
                    $query->select(['posts_media.id as media_id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
                },
            ])
            ->select('posts.id','posts.heading','posts.post_type','posts.description')
            ->where(function($query) use($search_keyword,$userId,$blockUserIds){
                $query->where('posts.post_type', 2);
                $query->where('posts.user_id', '<>', $userId);
                $query->whereRaw('posts.deleted_at IS NULL AND  posts.admin_status=1 AND posts.user_id NOT IN ('.$blockUserIds.')');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(posts.heading) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(posts.description) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(posts.city) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->where(function($query) use($userId){
                $query->where('posts.post_type', 2);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1');
            })
            ->whereHas('getMedia', function ($query){
                $query->where('is_default',1);
            })
            ->whereHas('getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->whereNotIn('posts.user_id', [$blockUserIds])
            ->orderByRaw($postSortBy)
            ->paginate(10);
            $NewPosts = $posts->getCollection()->transform(function ($value) {
                // Your code here
                if(isset($value['getMedia'][0]))
                    return $value['getMedia'][0];
            });
            $NewPosts = $NewPosts->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            $response['posts'] = $NewPosts;
            $response['paging'] = [
                'total_item'=>$posts->total(),
                'item_per_page'=>$posts->perPage(),
                'current_page'=>$posts->currentPage(),
                'last_page'=>$posts->lastPage(),
                'prev'=>$posts->previousPageUrl(),
                'next'=>$posts->nextPageUrl(),
                'first'=>$posts->url(1),
                'last'=>$posts->url($posts->lastPage()),
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
     * @api : save-post
     * @summary : Api for mark as post as save
     * @param : post_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 17 Jan 2022
     */
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
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : remove-save-post
     * @summary : Api for mark as post as remove from saved
     * @param : post_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 17 Jan 2022
     */
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
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-saved-posts
     * @summary : Get All Saved Post By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 18 Jan 2021
     */
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
                    $query->select(['posts.id','posts.user_id','posts.heading','posts.description','post_type','country_id','category_id','city','zip_code','created_at','posts.no_of_view']);
                    $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type NOT IN (3,4,5) AND posts.user_id NOT IN ('.$blockUserIds.')');
                    // $query->whereNotIn('posts.user_id', [$blockUserIds]);
                },
                'getPost.getMedia'=>function($query){
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
                    $query->where('posts_media.is_default',1);
                },
                'getPost.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('saved_posts.id','saved_posts.user_id','saved_posts.post_id','saved_posts.created_at')
            ->where(function($query) use($userId){
                $query->where('saved_posts.user_id', $userId);
            })
            ->whereHas('getPost.getUser', function ($query){
                $query->whereRaw('users.deleted_at IS NULL');
            })
            ->orderByRaw($postSortBy)
            ->paginate(10);
            // ->toArray();
            $NewPosts = $posts->getCollection()->transform(function ($value) {
                // Your code here
                if(isset($value['getPost']))
                    return $value['getPost'];
            });
            $NewPosts = $NewPosts->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);
            $response['posts'] = $NewPosts->toArray();
            $response['paging'] = [
                'total_item'=>$posts->total(),
                'item_per_page'=>$posts->perPage(),
                'current_page'=>$posts->currentPage(),
                'last_page'=>$posts->lastPage(),
                'prev'=>$posts->previousPageUrl(),
                'next'=>$posts->nextPageUrl(),
                'first'=>$posts->url(1),
                'last'=>$posts->url($posts->lastPage()),
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
     * @api : like-post
     * @summary : Api for like a post
     * @param : post_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 31 Jan 2022
     */
    public function likePost(LikePostRequest $request){
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
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unlike-post
     * @summary : Api for remove from like
     * @param : post_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 31 Jan 2022
     */
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
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-saved-posts
     * @summary : Get All Saved Post By User
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 18 Jan 2021
     */
    public function getLikedUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('posts_likes.id','posts_likes.user_id','posts_likes.post_id','posts_likes.created_at')
            ->where(function($query) use($postId){
                $query->where('posts_likes.post_id', $postId);
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
            // prd($posts);
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
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-posts-board
     * @summary : Get All Post follower on post board
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function getPostsBoardV2(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $dailtDevotionalArr = Array();
        try{
            $setting = getGlobalSetting();
            $postSortBy = 'posts.created_at desc';
            $userId = $user->id;
            $page = $request->page;
            if($setting['is_ads_active'] == 1){
                $paginate = 7;
            }else{
                $paginate = 10;
            }
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
                    $join->on('posts.user_id', '=', 'users.id')->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.admin_status=1 AND posts.post_type NOT IN(2,3,4,5) AND posts.user_id NOT IN ('.$blockUserIds.')');
                })
                ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                // ->leftJoin('posts_media', 'posts.id', '=', 'posts_media.post_id')
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.is_star',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.is_star',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
                'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                'categories.name as category_name','categories.id as categoryId','posts.post_type')
                            ->whereRaw("user_id = ".$userId." AND posts.deleted_at IS NULL AND users.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type NOT IN(2,3,4,5) AND posts.admin_status=1")
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
                $i = 0;
                if($page == 1){
                    $dailtDevotionalArr = getDailyDevotionalPost(1);
                    if(!empty($dailtDevotionalArr))
                        $finalResult[0] = $dailtDevotionalArr;
                        $i++;            
                }
                
                $arrRange=range(0, count($result), 1);
                shuffle($arrRange); // randomize the order
                $_2indexArr = array_slice($arrRange, 0, 2);
                // prd($result);
                foreach($result as $key => $value){
                    if($setting['is_ads_active'] == 1){
                        if(in_array($key, $_2indexArr)){
                            // echo "123";exit;
                            $finalResult[$i] = getAdsBlankObj(0);
                            $i++;
                            // prd($finalResult);
                        }
                    }
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
                    $arr['updated_at'] = strtotime($value['updated_at']);
                    $arr['no_of_view'] = $post->no_of_view;
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
                        $arr['get_user']['is_star'] = $value['is_star'];
                        $env = env("APP_ENV");
                        $arr['get_user']['twillio_identity'] = $env.'_'.$value['userID'];;
                        
                    }else{
                        $arr['get_user'] = null;
                    }
                    $getMedia = $post->getMedia()->get()->toArray();
                    // sort_array_of_array($getMedia,'is_preview_media');
                    if(!empty($getMedia)){
                        $arr['get_media'] = $getMedia;
                    }else{
                        $arr['get_media'] = [];
                    }
                    if(empty($dailtDevotionalArr))
                        $finalResult[$i] = $arr;
                    else
                        $finalResult[($i)] = $arr;

                    // pr($finalResult);
                    $i++;
                }
                // sort_array_of_array($finalResult,'created_at');
                // $result = Paginator::make($slice, count($items), $paginate);
                /* prd($slice);
                $result = DB::table(DB::raw("({$c->toSql()}) AS a"))
                ->mergeBindings($c->getQuery())
                ->orderBy('a.type')
                ->select('a.name', 'a.type')
                ->limit(20);
                prd($result); */
                

            // prd($data);
            /* $posts = UserFollow::with([
                'getFollowerPost.country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'getFollowerPost.Category'=>function($query){
                    $query->select(['categories.id','categories.name']);
                },
                'getFollowerPost.getMedia'=>function($query){
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
                },
                'getFollowerPost.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture']);
                }
            ])
            // ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at')
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.created_at')
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
            })
            ->whereHas('getFollowerPost', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->orderByRaw($postSortBy)
            ->paginate(10); */
            /* $NewPost = $slice->getCollection()->map(function ($value,$key) {
                $arr = Array();
                $post = Post::findOrFail($value->postID);
                // $arr = new stdClass();
                $arr['id'] = $value->postID;
                $arr['user_id'] = $value->userID;
                $arr['heading'] = $value->heading;
                $arr['description'] = $value->description;
                $arr['post_type'] = $value->post_type;
                $arr['country_id'] = $value->country_id;
                $arr['category_id'] = $value->category_id;
                $arr['city'] = $value->city;
                $arr['zip_code'] = $value->zip_code;
                $arr['is_saved'] = $post->is_saved;
                $arr['is_liked'] = $post->is_liked;
                $arr['total_likes'] = $post->total_likes;                
                $arr['is_commented'] = $post->is_commented;
                $arr['total_comments'] = $post->total_comments;                
                $arr['created_at'] = strtotime($value->created_at);
                $arr['updated_at'] = strtotime($value->updated_at);
                if($value->country_name!=''){
                    $arr['country']['name'] = $value->country_name;
                    $arr['country']['id'] = $value->country_id;
                }else{
                    $arr['country'] = null;
                }
                if($value->category_name!=''){
                    $arr['category']['name'] = $value->category_name;
                    $arr['category']['id'] = $value->category_id;
                }else{
                    $arr['category'] = null;
                }
                if($value->userID!='' && $value->userID!=null){
                    $arr['get_user']['id'] = $value->userID;
                    $arr['get_user']['first_name'] = $value->first_name;
                    $arr['get_user']['last_name'] = $value->last_name;
                    $arr['get_user']['profile_picture'] = $value->profile_picture;
                }else{
                    $arr['get_user'] = null;
                }
                $getMedia = $post->getMedia()->get()->toArray();
                if(!empty($getMedia)){
                    $arr['get_media'] = $getMedia;
                }else{
                    $arr['get_media'] = [];
                }
                return $arr;
                // prd($value);
            }); */

            $response['posts'] = $finalResult;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : delete-post
     * @summary : Api for delete the post
     * @param : post_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 17 Jan 2022
     */
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
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : comment-post
     * @summary : Api for comment a post
     * @param : post_id, user_id, comment_id, comment
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 Feb 2022
     */
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
            $savedPost['tag_json'] = $request->tag_json;
            $savedPost->save();
            $response['post_comment_id'] = $savedPost->id;
            if(trim($request->tag_json) !='' && strlen(trim($request->tag_json)) !=0){
                addUserTaggedData($response['post_comment_id'],$request->tag_json,1,$request->user_id,$request->post_id);
            }
            // prd($response);
            // Notification Send ($senderId,$reciverId,$notificationMsgKey,$data)
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
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unlike-post
     * @summary : Api for remove comment from post
     * @param : post_id, comment_id, user_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 15 Feb 2022
     */
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
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-saved-posts
     * @summary : Get All comment listing for the Post
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 18 Jan 2021
     */
    public function getPostCommentListing(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
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
            $comments = PostComment::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
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
            ->paginate(10);
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
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-saved-posts
     * @summary : Get All comment listing for the Post
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 18 Jan 2021
     */
    public function getRepliedComments(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        try{
            if(empty($request->post_id) || empty($request->comment_id)){ throw new \Exception(__('api-messages.6000')); }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
                return getResponse(632,400);
            }
            $likeSortBy = 'id desc';
            $postId = $request->post_id;
            $commentId = $request->comment_id;
            $comments = PostComment::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
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
            ->paginate(10);
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
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @summary : Api for report and post to admin
     * @type : post
     * @param : post_id, user_id, description
     * @return : json
     * @author Ashok Dadhich
     * @created_at 28 Feb 2022
     */
    public function reportPost(ReportPostRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            if(empty($request->post_id)){ throw new \Exception(__('api-messages.6000')); }
            $user = Auth::user();
            $postUserId = getPostUserId($request->post_id);
            $isBlocked = getBlockByUser($user->id,$postUserId);
            if($isBlocked){
                return getResponse(640,400);
            }
            $reportedPost = new ReportedPost();
            $reportedPost->user_id = $user->id;
            $reportedPost->post_id = $request['post_id'];
            $reportedPost->description = $request['description'];
            $reportedPost->category = (empty($request->category) ? '' : $request->category);
            $reportedPost->save();
            $response['report_post_id'] = $reportedPost->id;
            $code = 633;
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-posts-board-new-v2
     * @summary : Get All Post follower on post board
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function getPostsBoardNewV2(Request $request){
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
        /* prd($your_array); */
        try{
            $setting = getGlobalSetting();
            $postSortBy = 'posts.created_at desc';
            $userId = $user->id;
            $page = $request->page;
            if($setting['is_ads_active'] == 1){
                $paginate = 8;
            }else{
                $paginate = 10;
            }
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
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.is_star',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at','users.is_star',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                $i = 0;
                $arrRange=range(0, count($result), 1);
                shuffle($arrRange); // randomize the order
                $_2indexArr = array_slice($arrRange, 0, 2);
                // pr($_2indexArr);
                foreach($result as $key => $value){
                    if($setting['is_ads_active'] == 1){
                        if(in_array($key, $_2indexArr)){
                            // echo "123";exit;
                            $finalResult[] = getAdsBlankObj(0);
                            $i++;
                            // prd(getAdsBlankObj());
                        }
                    }
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
                    $arr['no_of_view'] = $post->no_of_view;
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
                        $arr['get_user']['twillio_identity'] = $env.'_'.$value['userID'];
                        $arr['get_user']['is_star'] = $value['is_star'];

                    }else{
                        $arr['get_user'] = null;
                    }
                    $getMedia = $post->getMedia()->get()->toArray();
                    sort_array_of_array($getMedia,'is_preview_media');
                    if(!empty($getMedia)){
                        $arr['get_media'] = $getMedia;
                    }else{
                        $arr['get_media'] = [];
                    }
                    if(empty($dailtDevotionalArr))
                        $finalResult[] = $arr;
                    else
                        $finalResult[($i+1)] = $arr;

                    $i++;
                }
                /* if(empty($finalResult)){
                    // case when no daily devotional and no user post 
                    $finalResult[0] = getAdsBlankObj(0);
                    $finalResult[1] = getAdsBlankObj(0);
                }else if(count($finalResult) == 1){
                    // case when only one daily devotional and no user post 
                    $finalResult[1] = getAdsBlankObj(0);
                    $finalResult[2] = getAdsBlankObj(0);
                } */
            $response['posts'] = $finalResult;
            // echo count($finalResult);exit;
            /********* Events Data */
            if($page == 1){
                $eventSortBy = 'id desc';
                $search_keyword = '';
                $userId = $user->id;
                $events = Event::with([
                    'getUser'=>function($query){
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.is_star']);
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
                        $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.is_star']);
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
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : like-comment
     * @summary : Api for like a comment
     * @param : comment_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 30 Aug 2022
     */
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
                sendNotificationWithData($request->user_id,$commentUserId,19,['post_id'=>$postId,'comment_id'=>$request->comment_id,'notification_type'=>19]);
            }
        } catch (\Exception $e){
            // prd($e);
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(662,200,$response);
    }
    /**
     * @header : Authorization=> api-key, Accept: application/json, Api-Key:
     * @type : post
     * @api : unlike-comment
     * @summary : Api for remove from like from comment
     * @param : comment_id
     * @return : json
     * @author Ashok Dadhich
     * @created_at 31 Jan 2022
     */
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
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-like-comment-user-listing
     * @summary : Get All user liked comment
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 18 Jan 2021
     */
    public function getCommentLikedUserList(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at','users.is_star']);
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
            // prd($posts);
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
     * @summary : Api for create/update Post with media and add clickable parameter
     * @param : heading, description, user_id, is_clickable
     * @type : post
     * @return : json
     * @author Ashok Dadhich
     * @created_at 04 Oct 2022
     */
    public function createPostWithMedia(GeneralPostRequest $request){
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $post = Post::findOrFail($request['id']);
                $code = 619;
            }else{
                $post = new Post();
                $code = 618;
            }
            $user = Auth::user();
            $post->user_id = $user->id;
            $post->heading = $request['heading'];
            $post->description = $request['description'];
            $post->post_type = 1;
            $post->is_clickable = (!empty($request->is_clickable) ? $request->is_clickable : 0);
            $post->save();
            $response['post_id'] = $post->id;
            $destinationPath = config('constants.post_media.upload_path');
            if($request->hasFile('image')){
                $postMedia = new PostMedia();
                $file = $request->file('image');
                if($request['media_type'] == 2){
                    // prd($file[0]->getMimeType());
                    $extension = $file[0]->extension();    
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    // echo $postMedia->file_name;
                    if(isset($postMedia->file_name) && $postMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
                        Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->file_name);
                        // unlink($destinationPath.$postMedia->file_name);
                    }
                    // $file[0]->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file[0]));
                    $postMedia->file_name = $fileName;
                    // echo $postMedia->file_name;exit;
                    // if($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'gif')
                    {
                        $extension = $file[1]->extension();    
                        $fileName = 'thumb_'.rand().time().'.'.$extension;
                        $pathToUpload = $destinationPath.'/'.$fileName;
                        //Move Uploaded File
                        if(isset($postMedia->thumb_image_name) && $postMedia->thumb_image_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->thumb_image_name)){
                            Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->thumb_image_name);
                            // unlink($destinationPath.$postMedia->file_name);
                        }
                        Storage::disk('s3')->put($pathToUpload, file_get_contents($file[1]));
                        // $file[1]->move($destinationPath,$fileName);
                        $postMedia->thumb_image_name = $fileName;
                        
                    }
                }else{
                    
                    $extension = $file->extension();
                    $fileName = rand().time().'.'.$extension;
                    $pathToUpload = $destinationPath.'/'.$fileName;
                    //Move Uploaded File
                    if(isset($postMedia->file_name) && $postMedia->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
                        // unlink($destinationPath.$postMedia->file_name);
                        Storage::disk('s3')->delete($destinationPath.'/'.$postMedia->file_name);
                    }
                    // $file->move($destinationPath,$fileName);
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    $postMedia->file_name = $fileName;

                }
                $postMedia->media_type = $request['media_type'];
                $postMedia->post_id = $response['post_id'];
                $postMedia->media_order = ($request->filled('media_order') ? $request['media_order'] : 1);
                $postMedia->is_default = ($request->filled('is_default') ? $request['is_default'] : 0);
                $postMedia->width = ($request->filled('width') ? $request['width'] : 0);
                $postMedia->height = ($request->filled('height') ? $request['height'] : 0);
                $save = $postMedia->save();
                $response['post_media_id'] = $postMedia->id;
            }

        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        DB::commit();
        return getResponse($code,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-posts-board
     * @summary : Get All Post follower on post board
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
    public function getPostsBoard(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        $search_keyword = '';
        $dailtDevotionalArr = Array();
        try{
            $postSortBy = 'posts.created_at desc';
            $userId = $user->id;
            $page = $request->page;
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
                    $join->on('posts.user_id', '=', 'users.id')->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1 AND posts.admin_status=1 AND posts.post_type NOT IN(2,3,4,5) AND posts.user_id NOT IN ('.$blockUserIds.')');
                })
                ->leftJoin('countries', 'posts.country_id', '=', 'countries.id')
                ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                // ->leftJoin('posts_media', 'posts.id', '=', 'posts_media.post_id')
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
                'countries.name as country_name','countries.id as countryId','posts.created_at as createdAt',
                'categories.name as category_name','categories.id as categoryId','posts.post_type')
                            ->whereRaw("user_id = ".$userId." AND posts.deleted_at IS NULL AND users.deleted_at IS NULL AND posts.admin_status=1 AND posts.post_type NOT IN(2,3,4,5) AND posts.admin_status=1")
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
                    $arr['updated_at'] = strtotime($value['updated_at']);
                    $arr['no_of_view'] = $post->no_of_view;
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
                    sort_array_of_array($getMedia,'is_preview_media');
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
                // sort_array_of_array($finalResult,'created_at');
                // $result = Paginator::make($slice, count($items), $paginate);
                /* prd($slice);
                $result = DB::table(DB::raw("({$c->toSql()}) AS a"))
                ->mergeBindings($c->getQuery())
                ->orderBy('a.type')
                ->select('a.name', 'a.type')
                ->limit(20);
                prd($result); */
                

            // prd($data);
            /* $posts = UserFollow::with([
                'getFollowerPost.country'=>function($query){
                    $query->select(['countries.id','countries.name']);
                },
                'getFollowerPost.Category'=>function($query){
                    $query->select(['categories.id','categories.name']);
                },
                'getFollowerPost.getMedia'=>function($query){
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
                },
                'getFollowerPost.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture']);
                }
            ])
            // ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at')
            ->select('users_follows.id','users_follows.user_id','users_follows.follower_id','users_follows.created_at')
            ->where(function($query) use($userId){
                $query->where('users_follows.user_id', $userId);
            })
            ->whereHas('getFollowerPost', function ($query) use($search_keyword){
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                            $query->orWhereRaw('LOWER(users.first_name) like "%'.strtolower($search_keyword).'%"');
                            $query->orWhereRaw('LOWER(users.last_name) like "%'.strtolower($search_keyword).'%"');
                        });
                }
            })
            ->orderByRaw($postSortBy)
            ->paginate(10); */
            /* $NewPost = $slice->getCollection()->map(function ($value,$key) {
                $arr = Array();
                $post = Post::findOrFail($value->postID);
                // $arr = new stdClass();
                $arr['id'] = $value->postID;
                $arr['user_id'] = $value->userID;
                $arr['heading'] = $value->heading;
                $arr['description'] = $value->description;
                $arr['post_type'] = $value->post_type;
                $arr['country_id'] = $value->country_id;
                $arr['category_id'] = $value->category_id;
                $arr['city'] = $value->city;
                $arr['zip_code'] = $value->zip_code;
                $arr['is_saved'] = $post->is_saved;
                $arr['is_liked'] = $post->is_liked;
                $arr['total_likes'] = $post->total_likes;                
                $arr['is_commented'] = $post->is_commented;
                $arr['total_comments'] = $post->total_comments;                
                $arr['created_at'] = strtotime($value->created_at);
                $arr['updated_at'] = strtotime($value->updated_at);
                if($value->country_name!=''){
                    $arr['country']['name'] = $value->country_name;
                    $arr['country']['id'] = $value->country_id;
                }else{
                    $arr['country'] = null;
                }
                if($value->category_name!=''){
                    $arr['category']['name'] = $value->category_name;
                    $arr['category']['id'] = $value->category_id;
                }else{
                    $arr['category'] = null;
                }
                if($value->userID!='' && $value->userID!=null){
                    $arr['get_user']['id'] = $value->userID;
                    $arr['get_user']['first_name'] = $value->first_name;
                    $arr['get_user']['last_name'] = $value->last_name;
                    $arr['get_user']['profile_picture'] = $value->profile_picture;
                }else{
                    $arr['get_user'] = null;
                }
                $getMedia = $post->getMedia()->get()->toArray();
                if(!empty($getMedia)){
                    $arr['get_media'] = $getMedia;
                }else{
                    $arr['get_media'] = [];
                }
                return $arr;
                // prd($value);
            }); */

            $response['posts'] = $finalResult;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
    /**
     * @header : Authorization=> Bearer key, Accept: application/json, Api-Key:
     * @type : post
     * @api : get-posts-board-new
     * @summary : Get All Post follower on post board
     * @param : page
     * @return : json
     * @author Ashok Dadhich
     * @created_at 20 Dec 2021
     */
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
            $page = $request->page;
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
                ->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at',
                        'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                $users = DB::table("posts")->select('users.id as userID','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.twilio_s_id','users.deleted_at',
                'posts.id as postID','posts.heading','posts.description','posts.created_at','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.updated_at','posts.no_of_view',
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
                    $arr['created_at'] = strtotime($value['created_at']);
                    $arr['updated_at'] = strtotime($value['updated_at']);
                    $arr['no_of_view'] = $post->no_of_view;
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
                    sort_array_of_array($getMedia,'is_preview_media');
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
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }
}
