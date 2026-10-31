<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\CmsPages;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;

use DB;
use File;
use Auth;

class CmsController extends Controller
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
            'title' => __('language.cms_title'),
            'changeStatusURL' => url('admin-panel/cms-page/change-status')
        );
        return view ('backend.cms.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){

        $cms = CmsPages::where(function($query) use ($request){})->orderBy('updated_at', 'ASC')->get();
        // prd($cms);
        return DataTables::of($cms)
        /*->addColumn('name', function ($cms)
        {
            return ucwords($cms->name);
        })*/
        ->addColumn('title', function ($cms)
        {
            if(strlen($cms->title)>70)
                $title = substr($cms->title, 0,70).'...';
            else
                $title = $cms->title;
            return $title;
        })
        ->addColumn('description', function ($cms)
        {
            if(strlen($cms->description)>70)
                $description = substr($cms->description, 0,70).'...';
            else
                $description = $cms->description;
            return $description;
        })
        ->addColumn('created_at', function ($cms)
        {
            return Carbon::parse($cms->created_at)->format('d/m/Y');
        })
        ->addColumn('updated_at', function ($cms)
        {
            return Carbon::parse($cms->updated_at)->format('d/m/Y');
        })
        ->addColumn('action', function ($cms)
        {
            return '<a href="'.url('admin-panel/cms-page/view/'.$cms->id).'" class="action-control" title="View"><i class="fa fa-eye"></i></a><a href="'.url('admin-panel/cms-page/edit/'.$cms->id).'" class="action-control" title="Edit"><i class="fa fa-edit"></i></a>';
        })
        ->rawColumns(['action' => 'action'])->addIndexColumn()->make(true);
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
        $backButton = url('admin-panel/cms-page');
        $saveButton = url('admin-panel/cms-page/save');

        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.cms_add'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/cms/save'),
                        );
        } else {
            $pageData = CmsPages::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.cms_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.cms.add')->with($data);
    }

    /**
     * @summary to save or update record
     * @param
     * @return view
     * @author blitzinfosystem
     * @created_at 17 Oct 2019
     */
    public function store(AdminCmsUpdate $request){
        DB::beginTransaction();
        try{
            if($request->filled('id')){
                $cms = CmsPages::findOrFail($request['id']);
            }else{
                $cms = new CmsPages;
            }            
            $cms->description = $request['description'];
            $cms->save();
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'CMS Pages','attribute2'=>'updated']);
        return redirect('admin-panel/cms-page')->with("message",$suceessMsg);
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
        $pageData = CmsPages::where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $data = array(
            'title' => __('language.cms_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/cms-page'),
        );
        return view ('backend.cms.detail')->with($data);
    }
}
