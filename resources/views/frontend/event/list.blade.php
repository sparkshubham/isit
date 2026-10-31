@if(!empty($events))
               @foreach($events as $event)
                <div class="col-lg-4 col-sm-6 mb-3  @if($event->is_saved == 0) event-count @else  save-event-count @endif "  id="hide_show_event_{{$event['id']}}">
                    <div class="card single-event single-store h-100">   
                       <div class="store-img image-wrap">           
                            
                            <a href="{{ route('event-view',['event_id'=>$event->id]) }}"> 
                                <div class="owl-carousel owl-theme"> 
                                        @php
                                            $file_name='';                                         
                                        @endphp      
                                        @if(isset($event->image))
                                           @php $file_name=$path['event_media'].$event->image;@endphp
                                        @else
                                           @php $file_name =url('/front/images/events-placeholder.png');@endphp
                                        @endif

                                     <div class="item" data-merge="{{$event->id}}">
                                            <img src="{{$file_name}}" alt="#">
                                    </div>
                                </div>      
                            </a>
                       </div>

                            
                        <h4>{{$event['name']}}</h4>

                        @if(Auth::user()->id==$event->user_id)
                            <div class="event-button-cover">
                                @if($event->admin_status==0)
                                    <a class="btn btn-warning" onclick="#">
                                    <span id="evnt_txt">Pending</span></a>
                                @elseif($event->admin_status==2)
                                    <a class="btn btn-primary" onclick="#">
                                    <span id="evnt_txt">Declined</span></a>
                                @else
                                     <a class="btn btn-success" onclick="#">
                                     <span id="evnt_txt">Accepted</span></a>
                                @endif
                            </div>
                        @endif
                        <ul>
                            <li><i class="uil uil-calender"></i> {{convertIntTodate($event['date'],'d M, Y')}}</li>  
                                    @php   
                                    $time_zone='Asia/Calcutta';
                                        $startDate= convertTimeToUSERzone(convertIntTodate($event->start_time),$time_zone);
                                        $endDate=convertTimeToUSERzone(convertIntTodate($event->end_time),$time_zone);
                                        $startDateNew = date('h:i A', strtotime($startDate));
                                        $endDateNew = date('h:i A', strtotime($endDate));
                                    @endphp
                            <li><i class="uil uil-clock"></i>{{$startDateNew}} - {{$endDateNew}} </li><!-- <li><i class="uil uil-map-marker"></i> San Jose</li> -->
                        </ul>
                        @if($event->user_id != Auth::user()->id)
                        
                            @if($event->is_saved == 1)
                                <a class="btn btn-primary"  onclick="removeSaveEvent({{$event->id}},{{Auth::user()->id}})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16">
                                        <path id="Icon_feather-bookmark" class="bookmark_{{$event->id}}" data-name="Icon feather-bookmark" d="M15.167,17.5l-5.5-4.167L4.167,17.5V4.167A1.621,1.621,0,0,1,5.738,2.5H13.6a1.621,1.621,0,0,1,1.571,1.667Z" transform="translate(-3.667 -2)" fill="#fff" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                    </svg>
                                <span id="evnt_txt_{{$event->id}}"> Unsave the Date</span></a>
                                    <input type="hidden" id="is_saved_event_{{$event->id}}" value="{{$event->is_saved}}">
                            @else
                                <a class="btn btn-primary"   onclick="removeSaveEvent({{$event->id}},{{Auth::user()->id}})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16">
                                        <path id="Icon_feather-bookmark" class="bookmark_{{$event->id}}" data-name="Icon feather-bookmark" d="M15.167,17.5l-5.5-4.167L4.167,17.5V4.167A1.621,1.621,0,0,1,5.738,2.5H13.6a1.621,1.621,0,0,1,1.571,1.667Z" transform="translate(-3.667 -2)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                    </svg>
                                <span id="evnt_txt_{{$event->id}}">Save the Date</span></a>
                                    <input type="hidden" id="is_saved_event_{{$event->id}}" value="{{$event->is_saved}}">
                            @endif
                        
                        @endif

                        <div class="row event-list-btn-cover">
                        @if($event['user_id']==Auth::user()->id)
                                @if($event->admin_status==0 || $event->admin_status==2)
                                    <div class="col-6">
                                        <div  class="btn btn-secondary event-edit-delet-button cursor-pointer" onclick="editEvent({{$event['id']}})" >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit       
                                        </div>
                                    </div>
                                    @endif
                                <div class="col-6">
                                    <div class="btn btn-secondary event-edit-delet-button sold cursor-pointer" onclick="deleteEvent({{$event['id']}})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                            Delete          
                                    </div>
                                </div>
                                    
                                @endif

                        </div>
                        
                       

                        


                    </div>
                </div>
                @endforeach

            @endif