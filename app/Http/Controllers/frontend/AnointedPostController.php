<?php

namespace App\Http\Controllers\frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Category;
use App\Models\Country;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminPostRequest;
use App\Http\Requests\frontend\AdminCmsUpdate;
use App\Http\Requests\AnointedPostRequest;
use DB;
use File;
use Auth;
use Storage;
use FFMpeg;

class AnointedPostController extends Controller{
    public function __construct(Route $route){
        // $this->middleware(function ($request, $next) use ($route){
        //     // Getting controller function
        //     $routeNameStr = $route->getActionName();
        //     $routeName = explode('@',$routeNameStr);

        //     $moduleName = request()->segment(2);
        //     return $next($request);
        // });
    }

    public function index(Request $request){
        $response = [];
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $user = Auth::user();
        $search_keyword = '';        
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
        ->whereNotIn('posts.user_id', [$blockUserIds])
        ->orderByRaw($postSortBy)
        ->paginate(10);

        // $NewPosts = $posts->getCollection()->transform(function ($value) {
        //     // Your code here
        //     if(isset($value['getMedia'][0]))
        //         return $value['getMedia'][0];
        // });
        // $NewPosts = $NewPosts->filter(function ($value, $key) {
        //     return $value != null;
        // })->values();
        // prd($NewPosts->toArray());
        $response['posts'] = $posts;
       
        if($request->ajax()){
            $view = view('frontend.dashboard.post-index',$response)->render();
            return response()->json(['html'=>$view]);
        }
        $response['title'] ='Anointed Post';
        return view ('frontend.dashboard.index',$response);
    }


    
   


    public function store(Request $request){
        try{
         
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
            $post->category_id = $request['category'];
            $post->country_id = $request['country'];
            $post->city = $request['city_name'];
            $post->zip_code = $request['zip_code'];
            $post->description = $request['description'];
            // $post->post_type =$request['post_type'];
            $post->post_type = 2;
            $post->draft_status = $request['draft_status_anointed'];
            $post->save();
            /*****SAGE PRODUCT GALLERY******/
          

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

            /*****SAGE PRODUCT GALLERY******/
            if($post){
                $response_type='success';   
                // $response_message='Post added successfully';
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
        }catch (Exception $e){
            $response_type='error';
            $response_message=$e->getMessage();
        }
        return redirect()->route('post-index')->with($response_type,$response_message);
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
                    if(!empty($request->thumbFileName))
                    {
                        $thumbFileName = $request->thumbFileName;
                        $old_path_tmp = $destinationPathTmp.'/'.$thumbFileName; 
                        $new_path_tmp = $destinationPathNew.'/'.$thumbFileName;
                        Storage::disk('s3')->move($old_path_tmp, $new_path_tmp);
                        $postMedia->thumb_image_name = $thumbFileName; 
                    }
                    $postMedia->file_name = $fileName;  
                  

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

}
