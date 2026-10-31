<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use DataTables;
use Carbon\Carbon;

use App\Http\Requests\backend\AdminPostRequest;
use App\Http\Requests\frontend\AdminCmsUpdate;

use DB;
use File;
use Auth;
use Storage;
use FFMpeg;

class ExplorePostController extends Controller
{
    public function __construct(Route $route){
        // $this->middleware(function ($request, $next) use ($route){
        //     // Getting controller function
        //     $routeNameStr = $route->getActionName();
        //     $routeName = explode('@',$routeNameStr);

        //     $moduleName = request()->segment(2);
        //     return $next($request);
        // });
    }


    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */

    public function index(Request $request){
        $response = [];
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $user = Auth::user();
        $search_keyword = '';

            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $postSortBy = 'id desc';
            $userId = $user->id;
            // $userId = 2;
            $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            }
            $posts = Post::with([
                'getMedia'=>function($query){
                    $query->select(['posts_media.id as media_id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
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
                // $query->where('is_public_visibility',1);
            })
            ->whereNotIn('posts.user_id', [$blockUserIds])
            ->orderByRaw($postSortBy)
            ->groupBy('posts.id')
            ->paginate(10);
            // $NewPosts = $posts->getCollection()->transform(function ($value) {
              
            //     if(isset($value['getMedia'][0])){
            //         return $value['getMedia'][0];
            //     }
            // });
            // $NewPosts = $NewPosts->filter(function ($value, $key) {
            //     return $value != null;
            // })->values();
            // prd($NewPosts->toArray());
            $response['posts'] = $posts;
            $response['title'] = 'Explore';
            if($request->ajax()){
                $view = view('frontend.dashboard.post-index',$response)->render();
                return response()->json(['html'=>$view]);
            }
            return view ('frontend.dashboard.index',$response);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function view(Request $request,$post_id)
    {
        $id=$post_id;
        // $id=$request->post_id;
        $request->post_id=$id;
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->post_id)){ 
            }
            $isBlockedAdmin = isPostBlockByAdmin($request->post_id);
            if($isBlockedAdmin == 0){
              
                $response['title'] = 'Post details';
                return view('frontend.common.no-post',$response);
            }
            $isDeleted = isPostDeleted($request->post_id);
            if($isDeleted == 1){
                $response['title'] = 'Post details';
                return view('frontend.common.no-post',$response);
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
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('posts.id','posts.heading','posts.description','posts.post_type','posts.country_id','posts.category_id','posts.city','posts.zip_code','posts.user_id','posts.created_at','posts.updated_at','posts.no_of_view')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(posts.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($postId){
                $query->where('posts.id', $postId);
                $query->whereRaw('posts.deleted_at IS NULL AND posts.admin_status=1');
            })
            ->first();
            // var_dump($posts);
            if(empty($posts) || $posts == null){
                return view('frontend.common.no-post',$response);
            }else if($posts->getUser->is_block == 1){

                return view('frontend.common.no-post',$response);
            }
            $response['posts'] = $posts;
            $response['title'] = 'Post details';
            $response['is_viewed']=1 ;
            $response['no_of_view'] = addViewCountPost($posts->id,$user->id);
            return view('frontend.explore.explore-details',$response);

        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();

        }


    }

    
}
