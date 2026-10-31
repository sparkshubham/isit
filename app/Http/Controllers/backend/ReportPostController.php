<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\ReportedPost;
use App\Models\Post;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;

use DB;
use File;
use Auth;

class ReportPostController extends Controller
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
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function index()
    {
        $data = array(
            'title' => __('language.report_post_title'),
            'changeStatusURL' => url('admin-panel/report-post/change-status')
        );
        return view ('backend.report-post.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){

        $report_post = ReportedPost::with([
                        'getUser'=>function($query){
                            $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                        },
                        'getPost'=>function($query){
                            $query->select(['posts.id','posts.heading','posts.description','posts.post_type','posts.admin_status']);
                        }])->whereHas('getUser', function ($query){
                            $query->whereRaw('users.deleted_at IS NULL');
                        })->whereHas('getPost.getUser', function ($query){
                            $query->whereRaw('users.deleted_at IS NULL');
                        })->where(function($query) use ($request){})->orderBy('id', 'DESC')->get();
        // prd($report_post->toArray());
        return DataTables::of($report_post)
        /*->addColumn('name', function ($report_post)
        {
            return ucwords($report_post->name);
        })*/
        ->addColumn('category', function ($report_post)
        {
            if(strlen($report_post->category)>70)
                $category = substr($report_post->title, 0,70).'...';
            else
                $category = $report_post->category;
            return $category;
        })
        ->addColumn('description', function ($report_post)
        {
            if(strlen($report_post->description)>70)
                $description = substr($report_post->description, 0,70).'...';
            else
                $description = $report_post->description;
            return $description;
        })
        ->addColumn('username', function ($report_post)
        {   
            if(!isset($report_post->getUser->first_name)){
                return '';
            }   
            $name = str_replace('“', '', $report_post->getUser->first_name.$report_post->getUser->last_name);
            $name = str_replace('”', '', $name);
            $name = addslashes(substr($name,0,30));
            return strlen($report_post->getUser->first_name.$report_post->getUser->last_name) > 30 ? $name."..." : $report_post->getUser->first_name.' '.$report_post->getUser->last_name;
        })
        ->addColumn('post_title', function ($report_post)
        {
            if(strlen($report_post->getPost->heading)>70)
                $title = substr($report_post->getPost->heading, 0,70).'...';
            else
                $title = $report_post->getPost->heading;
            return $title;
        })
        // ->addColumn('post_description', function ($report_post)
        // {
        //     if(strlen($report_post->getPost->description)>70)
        //         $description = substr($report_post->getPost->description, 0,70).'...';
        //     else
        //         $description = $report_post->getPost->description;
        //     return $description;
        // })
        ->addColumn('post_type', function ($report_post)
        {
            // echo $report_post->getPost->post_type;exit;
            if($report_post->getPost->post_type == 1){
                return 'General';
            }else if($report_post->getPost->post_type == 2){
                return 'Anointed';
            }else{
                return 'Admin Post';
            }
        })
        ->addColumn('admin_status', function ($report_post)
        {
            $disable = '';
            $state = $report_post->getPost->admin_status==1?'checked':'';
            return '<div status = '.$report_post->getPost->admin_status.' class="material-switch pull-center"><input class="testimonial-check" id="someSwitchOptionSuccess_'.$report_post->id.'" name="someSwitchOption001" type="checkbox" '.$state.' data-id="'.$report_post->id.'" post-id="'.$report_post->post_id.'" '.$disable.'/><label for="someSwitchOptionSuccess_'.$report_post->id.'" class="label-success status-lable"></label></div>';
        })
        ->addColumn('created_at', function ($report_post)
        {
            return Carbon::parse($report_post->created_at)->format('d/m/Y');
        })
        ->addColumn('action', function ($report_post)
        {
            return '<a href="'.url('admin-panel/report-post/view/'.$report_post->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a>';
        })
        ->rawColumns(['admin_status' => 'admin_status','action'=>'action'])->addIndexColumn()->make(true);
    }


    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function create(Request $request, $id=null)
    {
        $backButton = url('admin-panel/report-post');
        $saveButton = url('admin-panel/report-post/save');

        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.report_post_add'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/report-post/save'),
                        );
        } else {
            $pageData = ReportedPost::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.report_post_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.report-post.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function store(AdminCmsUpdate $request){
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $report_post = ReportedPost::findOrFail($request['id']);
            }else{
                $report_post = new ReportedPost;
            }            
            $report_post->description = $request['description'];
            $report_post->save();
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'CMS Pages','attribute2'=>'updated']);
        return redirect('admin-panel/report-post')->with("message",$suceessMsg);
    }    

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function show($id)
    {
        $pageData = ReportedPost::with([
            'getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
            },
            'getPost'=>function($query){
                $query->select(['posts.*']);
            },
            'getPost.Country'=>function($query){
                $query->select(['countries.id','countries.name']);
            },
            'getPost.Category'=>function($query){
                $query->select(['categories.id','categories.name']);
            },
            'getPost.getMedia'=>function($query){
                $query->select(['posts_media.id','posts_media.post_id','posts_media.media_type','posts_media.file_name','posts_media.media_order','posts_media.is_default','posts_media.thumb_image_name','posts_media.width','posts_media.height']);
            },
            'getPost.getUser'=>function($query){
                $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
            }
            ])->where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData->getPost);
        // Return With Data Array
        $folderName = config('constants.post_image.upload_path');
        $displayImagePath = getS3Url($folderName);
        $pageData['displayImagePath'] = $displayImagePath.'posts/';
        $data = array(
            'title' => __('language.report_post_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/report-post'),
        );
        return view ('backend.report-post.detail')->with($data);
    }
    /**
     * @summary to change status
     * @param
     * @return jsonResponse
     * @author matellio
     * @created_at 29 Oct 2019
     */
    public function changeStatus(Request $request)
    {
        $response = [];
        if($request->filled('id') && $request->filled('status') && $request->filled('post_id')){
            try{
                ReportedPost::where('id', '=', $request['id'])->update(['status' => $request['status']]);
                Post::where('id', '=', $request['post_id'])->update(['admin_status' => $request['status']]);
                $response = ['status'=>true];
            } catch (\Exception $e){
                $response = ['status'=>false];
            }
        }else{
            $response = ['status'=>false];
        }

        return Response::json($response, 200);
    }
}
