
@extends('frontend.layouts.app')
@section('content')

<section class="main-wrapper">
        <div class="container">     
            <div class="main-title d-flex align-items-center justify-content-between">
                <h3>Events</h3>
                <a class="btn btn-secondary" href="#" data-bs-toggle="modal" onclick ="CreateEventModal()">Create Event</a>
            </div>
            @php
                $link = request()->url();
                $link_array = explode('/',$link);
                $valueUrl= end($link_array);
            @endphp
             @if($valueUrl=='event-list-my')
                <div class="mypost-and-save-post-section">
                        <ul class="nav nav-tabs mb-3" id="tabs-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                             <button class="nav-link save_unsave_event_filter  {{$event_type=='my'?'active':''}}  {{request()->type=='my'?'active':''}}" data-type="my" id="my-event-tab" data-bs-toggle="pill" data-bs-target="#my-event" type="button" role="tab" aria-controls="my-posts" aria-selected="true">My Events</button>
                            </li>
                            <li class="nav-item" role="presentation">
                             <button class="nav-link  save_unsave_event_filter  {{$event_type=='saved'?'active':''}}  {{request()->type=='saved'?'active':''}}" data-type="saved" id="saved-event-tab" data-bs-toggle="pill" data-bs-target="#saved-event" type="button" role="tab" aria-controls="saved-posts" aria-selected="false">Saved Events</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="tabs-tabContent">
                            <div class="tab-pane fade  {{$event_type=='my'?'show active':''}}  {{request()->type=='my'?'show active':''}}" id="my-event" role="tabpanel" aria-labelledby="my-event-tab">                               
                                @if(count($my_events) > 0)
                                    <div class="row" id="event_list">
                                        @php 
                                            $data_arr['events']=$my_events;
                                         
                                        @endphp
                                        @include('frontend.event.list',$data_arr)
                                    </div>
                                @else
                                    @php 
                                        $data_arr=['error_name'=>'No event found'];
                                    @endphp
                                    @include('frontend.common.no-data',$data_arr)
                                @endif
                            </div>
                            <div class="tab-pane fade {{$event_type=='saved'?'show active':''}}  {{request()->type=='saved'?'show active':''}}" id="saved-event" role="tabpanel" aria-labelledby="saved-event-tab">
                                @if(count($saved_events) > 0)
                                    <div class="row" id="saved_event_list">
                                        @php
                                         
                                             $data_arr['events']=$saved_events; 
                                        @endphp
                                        @include('frontend.event.list',$data_arr)
                                    </div>
                                @else
                                    @php  
                                        $data_arr=['error_name'=>'No saved event found'];
                                    @endphp
                                    @include('frontend.common.no-data',$data_arr)
                                @endif
                            </div>
                        </div>  
                </div>
             @else

                    @if(count($events) > 0)
                        <div class="row" id="event_list">
                           @php
                             $data_arr['events']=$events;@endphp
                            @include('frontend.event.list',$data_arr)
                        </div>
                    @else
                        @php 
    
                            $data_arr=['error_name'=>'No event found'];
                        @endphp
                        @include('frontend.common.no-data',$data_arr)
                    @endif

             @endif
           
        </div>
        @include('frontend.event.create-event')
    </section>

   

@endsection
