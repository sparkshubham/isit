<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\backend\AdminPostRequest;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;
use DB;
use File;
use Auth;
use Storage;
use FFMpeg;

class GeneralPostController extends Controller
{
    public function __construct(Route $route){
        $this->middleware(function ($request, $next) use ($route){
            // Getting controller function
            $routeNameStr = $route->getActionName();
            $routeName = explode('@',$routeNameStr);

            $moduleName = request()->segment(2);
            return $next($request);
        });
    }


    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function index()
    {
        
        $data = array(
            'title' => __('language.general_post_title'),
            'changeStatusURL' => url('admin-panel/general-post/change-status')
        );
        return view ('backend.general-post.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        $generalPost = Post::where(function($query) use ($request){
            $query->where(['post_type'=>1]);
            $query->whereRaw('deleted_at IS NULL');
        })->with([
            'getMedia'=>function($query){
                $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
            },
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
            }
        ])->whereHas('getUser', function ($query){
            $query->whereRaw('users.deleted_at IS NULL');
        })->orderBy('updated_at', 'ASC')->get();
        // prd($generalPost);
        return DataTables::of($generalPost)
        ->addColumn('image_name', function ($generalPost)
        {
            // echo $generalPost->media_type;
            if(isset($generalPost->getMedia[0]->file_name) && $generalPost->getMedia[0]->file_name){
                $folderName = config('constants.post_media.display_path');
                $displayImagePath = getS3Url($folderName);
                // echo $displayImagePath;exit;
                if($generalPost->getMedia[0]->media_type == 1){
                    $image_path = url($displayImagePath.$generalPost->getMedia[0]->file_name);
                    return '<a href="'.$image_path.'" target=”_blank”><img src="'.$image_path.'" alt="Post Image" class="img-responsive" width="50px"></a>';
                }
                else {
                    $image_path = url($displayImagePath.$generalPost->getMedia[0]->thumb_image_name);
                    $file_path  = url($displayImagePath.$generalPost->getMedia[0]->file_name);
                    return '<a href="'.$file_path.'" target=”_blank”><img src="'.$image_path.'" alt="Post Image" class="img-responsive" width="50px"></a>';
                }
            }else{
                $img = url("/img").'/no_image.png';
                return '<img src="'.$img.'" alt="Post Image" class="img-responsive" width="50px">';
            }

        })
        ->addColumn('username', function ($generalPost)
        {   
            // echo $generalPost->user_id;
            if(!isset($generalPost->getUser->first_name)){
                return '';
            }
            $name = str_replace('“', '', $generalPost->getUser->first_name.$generalPost->getUser->last_name);
            $name = str_replace('”', '', $name);
            $name = addslashes(substr($name,0,30));
            return strlen($generalPost->getUser->first_name.$generalPost->getUser->last_name) > 30 ? $name."..." : $generalPost->getUser->first_name.' '.$generalPost->getUser->last_name;
        })
        ->addColumn('heading', function ($generalPost)
        {
            if(strlen($generalPost->heading)>70)
                $title = substr($generalPost->heading, 0,70).'...';
            else
                $title = $generalPost->heading;
            return $title;
        })
        ->addColumn('description', function ($generalPost)
        {
            if(strlen($generalPost->description)>70)
                $title = substr($generalPost->description, 0,70).'...';
            else
                $title = $generalPost->description;
            return $title;
        })
        ->addColumn('status', function ($generalPost)
        {
            $disable = '';
            $state = $generalPost->admin_status==1?'checked':'';
            return '<div class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$generalPost->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$generalPost->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$generalPost->id.'" class="label-success status-lable"></label></div>';
        })
        ->addColumn('created_at', function ($generalPost)
        {
            return date('d/m/Y',$generalPost->created_at);
        })
        ->addColumn('action', function ($generalPost)
        {
            // <a href="#" onclick="ConfirmDialog(\''.url('admin-panel/general-post/delete/'.$generalPost->id).'\')" class="action-control" title="Delete"><i class="fa fa-trash"></i></a>
            return '<a href="'.url('admin-panel/general-post/view/'.$generalPost->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a>';
        })
        ->rawColumns(['action' => 'action','image_name'=>'image_name','status'=>'status'])->addIndexColumn()->make(true);
    }


    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function create(Request $request, $id=null)
    {
        $backButton = url('admin-panel/general-post');
        $saveButton = url('admin-panel/general-post/save');
        $mediaTypeArr = [1=>'Image',2=>'Video'];
        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.general_post_title'),
                            'subTitle' => __('language.general_post_edit'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/general-post/save'),
                            'mediaTypeArr' => $mediaTypeArr,
                        );
        } else {
            $pageData = Post::where('id', "=", $id)->whereRaw('post_type = 3')->with([
                'getMedia'=>function($query){
                    $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
                }
            ])->orderBy('id', 'ASC')->first();
            // prd($pageData);
            $folderName = config('constants.post_media.display_path');
            $displayImagePath = getS3Url($folderName);
            if(isset($pageData->getMedia[0]->file_name)){
                $pageData['media_type'] = $pageData->getMedia[0]->media_type;
                if($pageData->getMedia[0]->media_type == 1){
                    $pageData['image_path'] = url($displayImagePath.$pageData->getMedia[0]->file_name);
                    $pageData['file'] = $pageData->getMedia[0]->file_name;
                }else{
                    $pageData['image_path'] = url($displayImagePath.$pageData->getMedia[0]->thumb_image_name);
                    $pageData['file'] = $pageData->getMedia[0]->file_name;
                }
            }else{
                $pageData['image_path'] = '';
                $pageData['file'] = '';
                $pageData['media_type'] = 1;
            }
            // echo $mediaType;exit;
            $data = array(
                            'subTitle' => __('language.general_post_add'),
                            'title' => __('language.general_post_title'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                            'mediaTypeArr' => $mediaTypeArr,
                        );

        }

        return view('backend.general-post.add')->with($data);
    }    

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function show($id)
    {
        $pageData = Post::where('id', "=", $id)->with([
            'getMedia'=>function($query){
                $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
            },
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
            }
        ])->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $folderName = config('constants.post_image.upload_path');
        $displayImagePath = getS3Url($folderName);
        $pageData['displayImagePath'] = $displayImagePath.'posts/';
        // prd($pageData->getMedia);
        $data = array(
            'title' => __('language.general_post_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/general-post'),
        );
        return view ('backend.general-post.detail')->with($data);
    }
    /**
     * @summary to change status
     * @param
     * @return jsonResponse
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function changeStatus(Request $request)
    {
        $response = [];
        if($request->filled('id') && $request->filled('status')){
            try{
                Post::where('id', '=', $request['id'])->update(['admin_status' => $request['status']]);
                $response = ['status'=>true];
            } catch (\Exception $e){
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }
    /**
     * @summary delete record from database
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 29 Oct 2019
     */
    public function destroy($id = null)
    {
        $user = Post::findOrFail($id);
        if ($user){
            DB::beginTransaction();
            try
            {
                $user->deleted_at = date('Y-m-d H:i:s');
                $user->save();
                $success_msg = __('messages.delete_success',['attribute1'=>'Record']);
            }
            catch (\Exception $e)
            {
                DB::rollback();
                $error_message = __('messages.delete_failure');
                echo $e->getMessage();
                exit;
                return redirect()->back()->withErrors($error_message);
            }
            DB::commit();
            return redirect('/admin-panel/general-post')->with('message', $success_msg);

        } else {
            $error_message = __('messages.not_found',['attribute1'=>'Record']);
            return redirect()->back()->withErrors($error_message);
        }

    }
}
