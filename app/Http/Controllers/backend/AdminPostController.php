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

class AdminPostController extends Controller
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
            'title' => __('language.daily_devotional_title'),
            'changeStatusURL' => url('admin-panel/admin-post/change-status')
        );
        return view ('backend.admin-post.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){

        $cms = Post::where(function($query) use ($request){
            $query->where(['post_type'=>3]);
            $query->whereRaw('deleted_at IS NULL');
        })->with([
            'getMedia'=>function($query){
                $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
            }
        ])->orderBy('updated_at', 'ASC')->get();
        // prd($cms);
        return DataTables::of($cms)
        ->addColumn('image_name', function ($cms)
        {
            // echo $cms->media_type;
            if(isset($cms->getMedia[0]->file_name) && $cms->getMedia[0]->file_name){
                $folderName = config('constants.post_media.display_path');
                $displayImagePath = getS3Url($folderName);
                // echo $displayImagePath;exit;
                if($cms->getMedia[0]->media_type == 1){
                    $image_path = url($displayImagePath.$cms->getMedia[0]->file_name);
                    return '<a href="'.$image_path.'" target=”_blank”><img src="'.$image_path.'" alt="Post Image" class="img-responsive" width="50px"></a>';
                }
                else {
                    $image_path = url($displayImagePath.$cms->getMedia[0]->thumb_image_name);
                    $file_path  = url($displayImagePath.$cms->getMedia[0]->file_name);
                    return '<a href="'.$file_path.'" target=”_blank”><img src="'.$image_path.'" alt="Post Image" class="img-responsive" width="50px"></a>';
                }
            }else{
                $img = url("/img").'/no_image.png';
                return '<img src="'.$img.'" alt="Post Image" class="img-responsive" width="50px">';
            }

        })
        ->addColumn('description', function ($cms)
        {
            if(strlen($cms->description)>70)
                $title = substr($cms->description, 0,70).'...';
            else
                $title = $cms->description;
            return $title;
        })
        ->addColumn('status', function ($cms)
        {
            $disable = '';
            $state = $cms->admin_status==1?'checked':'';
            return '<div class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$cms->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$cms->id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$cms->id.'" class="label-success status-lable"></label></div>';
        })
        ->addColumn('created_at', function ($cms)
        {
            return date('d/m/Y',$cms->created_at);
        })
        ->addColumn('action', function ($cms)
        {
            return '<a href="'.url('admin-panel/admin-post/edit/'.$cms->id).'" class="action-control" title="Edit"><i class="fa fa-edit"></i></a><a href="#" onclick="ConfirmDialog(\''.url('admin-panel/admin-post/delete/'.$cms->id).'\')" class="action-control" title="Delete"><i class="fa fa-trash"></i></a>';
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
        $backButton = url('admin-panel/admin-post');
        $saveButton = url('admin-panel/admin-post/save');
        $mediaTypeArr = [1=>'Image',2=>'Video'];
        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.daily_devotional_title'),
                            'subTitle' => __('language.daily_devotional_edit'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/admin-post/save'),
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
                            'subTitle' => __('language.daily_devotional_add'),
                            'title' => __('language.daily_devotional_title'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                            'mediaTypeArr' => $mediaTypeArr,
                        );

        }

        return view('backend.admin-post.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function store(AdminPostRequest $request){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        DB::beginTransaction();
        try{
            
            if($request->filled('id')){
                $AdminPost = Post::where('id', "=", $request['id'])->with([
                    'getMedia'=>function($query){
                        $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name']);
                    }
                ])->orderBy('id', 'ASC')->first();
            }else{
                $AdminPost = new Post;
            }
            // prd($request['remove_img']);exit;
            $env = env("APP_ENV");
            if($env == 'stage' || $env == 'production'){
                $AdminPost->user_id = 1;
            }else{
                $AdminPost->user_id = 4;
            }
            $AdminPost->description = $request['description'];
            $AdminPost->admin_status = $request['admin_status'];
            $AdminPost->post_type = 3;
            $AdminPost->save();
            $postId = $AdminPost->id;
            // echo $postId;exit;
            $destinationPath = config('constants.post_media.upload_path');
            // $request->hasFile('image_name')
            if($request->hasFile('file')){
                $postMedia =  new PostMedia();
                PostMedia::where('post_id', $postId)->delete();
                $file = $request->file('file');
                $extension = $file->extension();
                $fileName = rand().time().'.'.$extension;
                $pathToUpload = $destinationPath.$fileName;

                if(isset($AdminPost->getMedia[0]->file_name) && $AdminPost->getMedia[0]->file_name !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$postMedia->file_name)){
                    // unlink($destinationPath.$postMedia->file_name);
                    Storage::disk('s3')->delete($destinationPath.'/'.$AdminPost->getMedia[0]->file_name);
                }
                $width = 0;
                $height = 0;
                if($request['media_type'] == 1){
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    $image = getimagesize($file);
                    if(!empty($image)){
                        $width = $image[0];
                        $height = $image[1];
                    }
                    $postMedia->thumb_image_name = '';
                }else if($request['media_type'] == 2){
                    /*******Get Video Height and Width */
                    $ffprobe = FFMpeg\FFProbe::create();
                    $video = $ffprobe->streams($file)->videos()->first();
                    $width = $video->get('width');
                    $height = $video->get('height');
                    Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                    /*******Get Video Height and Width */
                    // echo $width.'~~~~ '.$height;exit;

                    /*******Get Thumb image and upload to s3 */
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
                    $postMedia->thumb_image_name = $thumbFileName;
                    /*******Get Thumb image and upload to s3 */
                }
                $postMedia->file_name = $fileName;
                $postMedia->media_type = $request['media_type'];
                $postMedia->post_id = $postId;
                $postMedia->media_order = 1;
                $postMedia->is_default = 1;
                $postMedia->width = $width;
                $postMedia->height = $height;
                $save = $postMedia->save();
                // echo $save;exit;
            }else{
                if($request['remove_img'] == 1){
                    PostMedia::where('post_id', $postId)->delete();
                }
            }        

        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'Daily Devotional','attribute2'=>'updated']);
        return redirect('admin-panel/admin-post')->with("message",$suceessMsg);
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
            }
        ])->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $folderName = config('constants.post_image.upload_path');
        $displayImagePath = getS3Url($folderName);
        $pageData['image_path'] = url($displayImagePath.$pageData->getMedia[0]->file_name);
        $data = array(
            'title' => __('language.daily_devotional_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/admin-post'),
        );
        return view ('backend.admin-post.detail')->with($data);
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
            return redirect('/admin-panel/admin-post')->with('message', $success_msg);

        } else {
            $error_message = __('messages.not_found',['attribute1'=>'Record']);
            return redirect()->back()->withErrors($error_message);
        }

    }
}
