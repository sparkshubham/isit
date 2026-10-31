<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\Event;
use App\Models\Post;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests\backend\AdminCmsUpdate;

use DB;
use File;
use Auth;
use DateTime;

class EventController extends Controller
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
            'title' => __('language.event_title'),
            'changeStatusURL' => url('admin-panel/event/change-status')
        );
        return view ('backend.event.index')->with($data);
    }

    /**
     * @summary
     * @param
     * @return view
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function data(Request $request){
        $helpStatus = config('constants.helpStatus');
        $event = Event::with([
                        'getUser'=>function($query){
                            $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                        }])->whereHas('getUser', function ($query){
                            $query->whereRaw('users.deleted_at IS NULL');
                        })->where(function($query) use ($request){
                            $query->whereRaw('events.deleted_at IS NULL');
                        })->orderBy('id', 'DESC')->get();
        // prd($event->toArray());
        return DataTables::of($event)
        ->addColumn('name', function ($event)
        {
            if(strlen($event->name)>70)
                $name = substr($event->title, 0,70).'...';
            else
                $name = $event->name;
            return $name;
        })
        ->addColumn('username', function ($event)
        {
            if(!isset($event->getUser->first_name)){
                return '';
            }
            $name = str_replace('“', '', $event->getUser->first_name.$event->getUser->last_name);
            $name = str_replace('”', '', $name);
            $name = addslashes(substr($name,0,30));
            return strlen($event->getUser->first_name.$event->getUser->last_name) > 30 ? $name."..." : $event->getUser->first_name.' '.$event->getUser->last_name;
        })
        ->addColumn('event_date', function ($event)
        {
            // return $event->date.' - '.Carbon::parse($event->date)->format('d/m/Y');
            return Carbon::parse($event->date)->format('d/m/Y');
        })
        ->addColumn('start_time', function ($event)
        {
            $timezone = $event->timezone;
            if($timezone==NULL || $timezone == ''){
                $timezone = 'Asia/Kolkata';
            }
            $timestamp = date('Y-m-d H:i:s',$event->start_time);
            return convertTimeToUSERzone($timestamp,$timezone,'h:i A');

        })
        ->addColumn('end_time', function ($event)
        {
            $timezone = $event->timezone;
            if($timezone==NULL || $timezone == ''){
                $timezone = 'Asia/Kolkata';
            }
            $timestamp = date('Y-m-d H:i:s',$event->end_time);
            return convertTimeToUSERzone($timestamp,$timezone,'h:i A');
        })
        ->addColumn('timezone', function ($event)
        {
            return $event->timezone;
        })
        ->addColumn('action', function ($event)
        {

            if($event->admin_status == 1){
                $html = '<button class="dt-button btn btn-sm accept-btn block" tabindex="0" aria-controls="tbl-career" type="button"><span>Accepted</span></button>';
            }else if($event->admin_status == 2){
                $html = '<button class="dt-button btn btn-sm decline-btn block" tabindex="0" aria-controls="tbl-career" type="button"><span>Declined</span></button>';
            }else{
                $html = '<button data-id="'.$event->id.'" admin-status=1  class="dt-button btn btn-sm accept-btn block testimonial-check" tabindex="0" aria-controls="tbl-career" type="button"><span>Accept</span></button>
                <button admin-status=2 data-id="'.$event->id.'" class="dt-button btn btn-sm decline-btn block testimonial-check" tabindex="0" aria-controls="tbl-career" type="button"><span>Decline</span></button>';
            }
            return $html;
            // return $ShippingStatus[$order->shipping_status];
        })
        ->rawColumns(['action'=>'action'])->addIndexColumn()->make(true);
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
        $backButton = url('admin-panel/event');
        $saveButton = url('admin-panel/event/save');

        if( $id == null ){

            // Return With Data Array
            $data = array(
                            'title' => __('language.help_user_add'),
                            'backBtnLink' => $backButton,
                            'saveBtn' => 'Save',
                            'save' => url('admin-panel/event/save'),
                        );
        } else {
            $pageData = Event::where('id', "=", $id)->orderBy('id', 'ASC')->first();
            $data = array(
                            'title' => __('language.help_user_edit'),
                            'pageData' => $pageData,
                            'saveBtn' => 'Update',
                            'backBtnLink' => $backButton,
                            'save' => $saveButton,
                        );

        }

        return view('backend.event.add')->with($data);
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
                $help_user = Event::findOrFail($request['id']);
            }else{
                $help_user = new Event;
            }            
            $help_user->description = $request['description'];
            $help_user->save();
        } catch (\Exception $e){
            DB::rollback();
            $error_message = $e->getMessage();
            p($error_message);
            return redirect()->back()->withErrors($error_message);
        }
        DB::commit();
        $suceessMsg = __('messages.success',['attribute1'=>'CMS Pages','attribute2'=>'updated']);
        return redirect('admin-panel/event')->with("message",$suceessMsg);
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
        $pageData = Event::where('id', "=", $id)->orderBy('id', 'ASC')->first();
        // prd($pageData);
        // Return With Data Array
        $data = array(
            'title' => __('language.help_user_view'),
            'pageData' => $pageData,
            'backBtnLink' => url('admin-panel/event'),
        );
        return view ('backend.event.detail')->with($data);
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
        if($request->filled('id') && $request->filled('status')){
            try{
                $status = 0;
                $event = Event::findOrFail($request['id']);
                $eventId = $event->id;
                $startTime = date('Y-m-d H:i:s',$event->start_time);
                $endTime = date('Y-m-d H:i:s',$event->end_time);
                $events = Event::select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.deleted_at')
                ->where(function($query) use($eventId,$startTime,$endTime){
                    $query->whereRaw('events.deleted_at IS NULL');
                    $query->whereRaw('events.admin_status = 1');
                    $query->whereRaw('events.id != '.$eventId);
                    // $query->whereRaw('events.end_time >= "'.gmdate('Y-m-d H:i:s').'"');
                })->get()->toArray();
                // prd($events);
                foreach($events as $key => $value){
                    $startTimeEve = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s',$value['start_time']));
                    $endTimeEve = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s',$value['end_time']));
                    $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime);
                    if($startTime->between($startTimeEve, $endTimeEve, true)){
                        $status = 2;
                        break;
                    }
                    $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime);
                    if($endTime->between($startTimeEve, $endTimeEve, true)){
                        $status = 2;
                        break;
                    }
                    if($startTimeEve->between($startTime, $endTime, true)){
                        $status = 2;
                        break;
                    }
                    if($endTimeEve->between($startTime, $endTime, true)){
                        $status = 2;
                        break;
                    }
                }
                // exit;
                // $status = 2 Means You can not accept the event beacuse another event is at same time
                if($status == 2 && $request['status'] == 1){
                    $response = ['status'=>2];    
                }else{
                    if($request['status'] == 1){
                        $post_id = createPost($event->user_id,$event->name,'',4);
                        $event->post_id = $post_id;
                        $event->save();
                    }
                    Event::where('id', '=', $request['id'])->update(['admin_status' => $request['status']]);
                    $response = ['status'=>1];
                }
            } catch (\Exception $e){
                prd($e);
                $response = ['status'=>0];
            }
        }else{
            $response = ['status'=>0];
        }

        return Response::json($response, 200);
    }
}
