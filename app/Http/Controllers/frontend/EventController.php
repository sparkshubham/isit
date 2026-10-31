<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Response;
use App\Models\AdminPost;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Event;
use DataTables;
use Carbon\Carbon;

use App\Http\Requests\backend\AdminPostRequest;
use App\Http\Requests\frontend\AdminCmsUpdate;

use App\Http\Requests\Api\SaveEventRequest;
use App\Http\Requests\Api\EventRequest;

use App\Models\SavedEvent;

use DB;
use File;
use Auth;
use Storage;
use FFMpeg;
use Redirect;

class EventController extends Controller
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
     * @author matellio
     * @created_at 17 Oct 2019
     */
    public function index(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $response['title'] = 'Event';
        $response['user'] = $user;
        $response['my_events'] =$this->getMyEvent($request);
        $response['saved_events'] =$this->getSavedEvents($request);

        $my_events=$response['my_events'];
        $saved_events=$response['saved_events'];
        if($request->ajax()){
            $data_list=[];
            // if(count($my_events)==0 || count($saved_events)==0  ){
            //     // $data_arr=['error_name'=>'No event found'];
            //     // $view = view('frontend.event.list',$response)->render();
            //     // return response()->json(['html'=>$view]);
            // }else{ 
                // saved_event_html
                // my_event_html
                $response['events']=$response['my_events'];

                $my_event = view('frontend.event.list',$response)->render();
                $response['events']=$response['saved_events'];
                $save_event = view('frontend.event.list',$response)->render();

                return response()->json(['my_event_html'=>$my_event,'saved_event_html'=>$save_event]);
            // }
        }

        if($request->type=='saved')
        {
            $response['event_type']='saved';
        }else{
           
            $response['event_type']='my';
        }

       
        return view('frontend.event.event-list',$response);
    }
 
    public function getSavedEvents($request){
        $response = [];
        $user = Auth::user();
        $search_keyword = '';
        $userId = $user->id;
        // echo $user->id;exit;
        try{

            if(empty($userId)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $postSortBy = 'id desc';    
            // $userId = $request->user_id;
           

            // echo $userId;exit;
            /* $blockUserIds = getBlockUserIds($userId);
            if($blockUserIds == ''){
                $blockUserIds = "''";
            } */
            $events = SavedEvent::with([
                'getEvent'=>function($query) use($user){
                    $query->select(['events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.created_at','events.admin_status','events.image']);
                    $query->whereRaw('events.deleted_at IS NULL');
                    // $query->whereNotIn('posts.user_id', [$blockUserIds]);
                },
                'getEvent.getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                    $query->whereRaw('users.deleted_at IS NULL');
                    
                }
            ])
            ->select('saved_events.id','saved_events.user_id','saved_events.event_id','saved_events.created_at')
            ->where(function ($query) use ($search_keyword) {
                if($search_keyword !=''){
                    $query->with('getEvent')->whereHas('getEvent', function ($query) use ($search_keyword) {
                        $query->whereRaw('LOWER(events.name) like "%' . strtolower($search_keyword) . '%"');
                    });
                }
            })
            ->where(function($query) use($userId){
                $query->where('saved_events.user_id', $userId);
            })
            ->orderByRaw($postSortBy)
            ->paginate(10);

            // $response = $events;
            $NewEvents = $events->getCollection()->transform(function ($value) {
                // Your code here
                if(isset($value['getEvent']))
                    return $value['getEvent'];
            });
            $NewEvents = $NewEvents->filter(function ($value, $key) {
                return $value != null;
            })->values();
            // prd($NewPosts->toArray());
            // prd($posts);
            $response = $NewEvents;
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return $response;
        }
        return $response;
    }


    

    private function getMyEvent($request){
        $user = Auth::user();
        $user_id=$user->id;
        $search_keyword = '';
        try{
            if(empty($user_id)){ throw new \Exception(__('api-messages.6000')); }
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            if(getBlockByUser($user_id,$user_id)){
                return getResponse(638,404);
            }
            $eventSortBy = 'id desc';
            $userId = $user_id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            ->where(function($query) use($userId,$search_keyword){
                $query->where('events.user_id', $userId);
                $query->whereRaw('events.deleted_at IS NULL');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                    });
                }
            })
            //  ->whereRaw('events.date >= "'.gmdate('Y-m-d').'"')
            ->orderByRaw($eventSortBy)
            ->paginate(10);
            $response=$events;
            
    
        } catch (\Exception $e){
           
            return $response;
        }
        return $response;
    }

    public function createEvent(Request $request){
        $response = [];
        DB::beginTransaction();
        try{
            if($request->filled('event_hidden_id')){
                $event = Event::findOrFail($request['event_hidden_id']);
                if($event->admin_status == 1){
                    $response_type='error';
                    $response_message="Action can not perfomed. Event approved by Admin.";
                    return Redirect::back()->with($response_type,$response_message);

                }
                $code = 648;
            }else{
                $event = new Event();
                $code = 647;
            }
        
            $currentTime=carbon::now();

            $e=Carbon::parse($request['end_time']);
            $end_time_s =$e->format('G:i');

            $s=Carbon::parse($request['start_time']);
            $start_time_s =$s->format('G:i');      

            $end_time=$request['date'].' '.$end_time_s.':01';
            $start_time=$request['date'].' '.$start_time_s.':01';


            $start_time_final= convertTimeToUTCzone($start_time,$request['timezone']);
            $end_time_final= convertTimeToUTCzone($end_time,$request['timezone']);


            $current_data= strtotime(carbon::now());
            if($current_data >= strtotime($start_time_final)){
                $response_type='error';
                $response_message='Action can not performed. Event time must be greater then current time. ';
                return Redirect::back()->with($response_type,$response_message);
            }

            $user = Auth::user();
            $event->user_id = $user->id;
            $event->name = $request['name'];
            $event->date = $request['date'];
            $event->start_time = $start_time_final;
            $event->end_time = $end_time_final;
            $event->timeoffset = $request['timeoffset'];
            $event->timezone = $request['timezone'];
            $event->admin_status = 0;
            // dd($request->hasFile('event_picture'));
            if($request->hasFile('event_picture')){
                $file = $request->file('event_picture');
                $destinationPath = config('constants.event_media.upload_path');
                $extension = $file->extension();
                $fileName = rand().time().'.'.$extension;
                $pathToUpload = $destinationPath.'/'.$fileName;
                //Move Uploaded File
                if(isset($event->image) && $event->image !=NULL && Storage::disk('s3')->exists($destinationPath.'/'.$event->image)){
                    Storage::disk('s3')->delete($destinationPath.'/'.$event->image);
                    // unlink($destinationPath.$user->profile_picture);
                }
                Storage::disk('s3')->put($pathToUpload, file_get_contents($file));
                // $file->move($destinationPath,$fileName);
                $event->image = $fileName;
            }
            $event->save();
            $response['event_id'] = $event->id;
            if($event){
                $response_type='success';
                if($request->filled('event_hidden_id')){
                    $response_message='Event edit successfully';
                }
                else{
                    $response_message='Event added successfully';
                }
            }else{
                $response_type='error';
                $response_message='Error occoured, Please try again.';
            }
        } catch(\Exception $e){
            DB::rollback();
            $this->optionalData['message'] = $e->getMessage();
            $response_type='error';
            return Redirect::back()->with($response_type,$this->optionalData['message']);;
            
        }
        DB::commit();
        return Redirect::back()->with($response_type,$response_message);

    }


     public function getPostboardEvent(Request $request){
        $response = [];
        $userFolderName = config('constants.user_image.upload_path');
        $postFolderName = config('constants.post_media.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['post_media'] = getS3Url($postFolderName);
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $user = Auth::user();
        $search_keyword = '';
        // echo $user->id;exit;
        try{
            if($request->filled('search_keyword')){ $search_keyword = $request->search_keyword;}
            $eventSortBy = 'events.date asc';
            $userId = $user->id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility','users.deleted_at']);
                    $query->whereRaw('users.deleted_at IS NULL');
                }
            ])
            ->select('events.id','events.name','events.user_id','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            ->where(function($query) use($userId,$search_keyword){
                $query->whereRaw('events.user_id NOT IN ('.$userId.')');
                $query->where('events.admin_status', 1);
                $query->whereRaw('events.deleted_at IS NULL');
                // $query->whereRaw('events.date >= "'.gmdate('Y-m-d').'"');
                $query->whereRaw('events.end_time >= "'.gmdate('Y-m-d H:i:s').'"');
                if($search_keyword!=''){
                    $query->where(function($query) use($search_keyword){
                        $query->orWhereRaw('LOWER(events.name) like "%'.strtolower($search_keyword).'%"');
                    });
                }
            })
            ->orderByRaw($eventSortBy)
            ->paginate(2);
            $response['events'] = $events;

            if($request->ajax()){
                $view = view('frontend.event.list',$response)->render();
                return response()->json(['html'=>$view]);
            }
            // prd($response['events']);
           
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        $response['title'] = 'Event';
        return view ('frontend.event.event-list',$response);
    }


    public function unsaveEvent(Request $request){
    	$user = Auth::user();
        $response = [];
        try{
            if(empty($request->user_id) || empty($request->event_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $savedEvent = SavedEvent::where(['event_id'=>$request->event_id,'user_id'=>$request->user_id])->first();
            if(empty($savedEvent)){
                return getResponse(653,400);
            }
            $savedEvent->delete();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(655,200,$response);
    }

    public function saveEvent(SaveEventRequest $request){
        $response = [];
    	$user = Auth::user();
        try{
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $savedEvent = new SavedEvent();
            $savedEvent['event_id'] = $request->event_id;
            $savedEvent['user_id'] = $request->user_id;
            $savedEvent->save();
            $response['save_event_id'] = $savedEvent->id;
            $eventUserId = getEventUserId($request->event_id);
            // Notification Send
            // sendNotificationWithData($request->user_id,$eventUserId,7,['event_id'=>$request->event_id,'notification_type'=>7]);
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(654,200,$response);
    }


    public function getEvent(Request $request){
        $response = [];
       
        $userFolderName = config('constants.user_image.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);

        $user = Auth::user();
        // echo $user->id;exit;
        try{
            $userID = $user->id;
            if(empty($request->event_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                return getResponse(650,400);
            }
            $eventId = $request->event_id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(events.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($eventId){
                $query->where('events.id', $eventId);
                $query->whereRaw('events.deleted_at IS NULL');
            })
            ->first();
             $events['min_date']=carbon::now()->format('Y-m-d');
             
             $events['formated_date']=  convertTimeToUSERzone(convertIntTodate($events->date),$request->time_zone,'Y-m-d');
             $events['formated_start_time']=convertTimeToUSERzone(convertIntTodate($events->start_time),$request->time_zone,'h:iA');
             $events['formated_end_time']=convertTimeToUSERzone(convertIntTodate($events->end_time),$request->time_zone,'h:iA');
             
            // var_dump($events->getUser->is_block);
            if(empty($events) || $events == null){
                return getResponse(644,400);
            }
            
            $response['event'] = $events;
            // $response['html']=  view('frontend.event.edit-event',$response)->render(); 
            $response['event'] = $events->toArray();

            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(103,200,$response);
    }


    

    public function deleteEvent(Request $request){
        $response = [];
    	$user = Auth::user();
        try{
            if(empty($request->event_id)){ 
                throw new \Exception(__('api-messages.6000')); 
            }
            $event = Event::findOrFail($request->event_id);
            if(empty($event)){
                return getResponse(112,400);
            }
            $event->deleted_at = date('Y-m-d H:i:s');
            $event->save();
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
        return getResponse(649,200,$response);
    }

    public function createPopup(Request $request){

        $response = array(
            'title' =>'Create event',
        );
        $view =view('frontend.event.create-event',$response)->render(); 
        $response['event']=$view;
        return getResponse(103,200,$response);

    }



    public function view(Request $request,$event_id){
        
        $response = [];
       
        $userFolderName = config('constants.user_image.upload_path');
        $eventFolderName = config('constants.event_media.upload_path');
        $response['path']['user_media'] = getS3Url($userFolderName);
        $response['path']['event_media'] = getS3Url($eventFolderName);
        $response['title'] = 'Event details';
        $user = Auth::user();

        try{
            $userID = $user->id;
            if(empty($request->event_id)){ throw new \Exception(__('api-messages.6000')); }
            $isDeleted = isEventDeleted($request->event_id);
            if($isDeleted == 1){
                // return getResponse(650,400)
                $response['error_name']='No event found';
                return view('frontend.common.no-post',$response);
            }
            $eventId = $request->event_id;
            $events = Event::with([
                'getUser'=>function($query){
                    $query->select(['users.id','users.first_name','users.last_name','users.profile_picture','users.is_public_visibility']);
                }
            ])
            ->select('events.id','events.user_id','events.name','events.date','events.start_time','events.end_time','events.timeoffset','events.admin_status','events.image')
            // ->select(DB::raw('UNIX_TIMESTAMP(STR_TO_DATE(events.created_at, "Y-m-d h:is")) as created_at_timestamp'))
            ->where(function($query) use($eventId){
                $query->where('events.id', $eventId);
                $query->whereRaw('events.deleted_at IS NULL');
            })
            ->first();
             $events['min_date']=carbon::now()->format('Y-m-d');
             
            //  $events['formated_date']=  convertTimeToUSERzone(convertIntTodate($events->date),$request->time_zone,'Y-m-d');
            //  $events['formated_start_time']=convertTimeToUSERzone(convertIntTodate($events->start_time),$request->time_zone,'h:iA');
            //  $events['formated_end_time']=convertTimeToUSERzone(convertIntTodate($events->end_time),$request->time_zone,'h:iA');
             
            // var_dump($events->getUser->is_block);
            if(empty($events) || $events == null){
                // return getResponse(644,400);
                $response['error_name']='No event found';
                return view('frontend.common.no-post',$response);
            }
            
            $response['event'] = $events;
            // $response['event'] = $events->toArray();

            
        } catch (\Exception $e){
            $this->optionalData['message'] = $e->getMessage();
            return getResponse(6000,403,$response,$this->optionalData);
        }
      
        return view('frontend.event.event-view',$response);
    }
   
}
