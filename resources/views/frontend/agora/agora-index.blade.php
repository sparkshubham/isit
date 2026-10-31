@if(!empty($available_events) && count($available_events)>0)
<div class="liveusers-group-card card mb-4 px-2">
    <div class="follow-single-user border-0"id="hide_show_join_watch_call">
        <div class="follow-username-img">
                <div class="followuser-img">
                    @if($data['get_user']['profile_picture'])
                        <img src="{{$path['user_media'].$data['get_user']['profile_picture']}}" alt="Lock">
                    @else
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                        <img height="45px" width="45px" src="{{$profile_url}}" alt="Lock">
                    @endif
                </div>
                <div class="followuser-name" >
                
                        <input  value="{{env('AGORA_APP_ID')}}" type="text" id="form-appid" class="form-control hide">
                        <input  value="{{$data['token']}}" type="text" id="form-token" class="form-control hide">
                        <input  value="{{$data['recToken']}}" type="text" id="form-recToken" class="form-control hide">

                        <input  value="{{$data['channel_name']}}" type="text" id="form-channel" class="form-control hide">
                        <input  value="{{$data['user_id']}}" type="number" id="form-uid" class="form-control hide"  data-decimals="0"/>   
                        
                        @if($data['isOwner']==1)
                           <a href="#"> All set for {{$data['name']}}</a>
                        @else
                            <a href="#">{{$data['get_user']['first_name'].' '.$data['get_user']['last_name']}} is live</a>
                        @endif

                    <div class="user-post-followers">
                        <!-- <span class="total-posts"> <img src="images/users-icon.svg" alt=""> 48 Users</span> -->
                    </div>
                    <div class="follow-btn">

                        @if($data['isOwner']==1)
                            <button id="join-channel" data-event-id="{{$data['id']}}" post_id="{{$data['post_id']}}"  class="btn btn-primary w-auto"> <svg  class="me-1"  xmlns="http://www.w3.org/2000/svg" width="14" height="14"  viewBox="0 0 24 24" fill="none"   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-monitor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>Start</button>
                        @else
                        <input type="hidden" id="broadcaster_name" value="{{$data['get_user']['first_name'].' '.$data['get_user']['last_name']}}"> 
                    @if($data['get_user']['profile_picture'])
                         <input type="hidden" id="broadcaster_img" value="{{$path['user_media'].$data['get_user']['profile_picture']}}"> 
                    @else
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                        <input type="hidden" id="broadcaster_img" value="{{$profile_url}}"> 
                    @endif
                            <button id="watch-live-btn"  data-event-id="{{$data['id']}}" post_id="{{$data['post_id']}}"  class="btn btn-primary w-auto d-flex align-items-center justify-content-center">
                            <svg  class="me-1"  xmlns="http://www.w3.org/2000/svg" width="14" height="14"  viewBox="0 0 24 24" fill="none"   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-monitor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>Watch</button> 

<!-- <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tv"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg> Watch</button> -->
                        @endif
                    </div>
            </div>
        </div>
    </div>
</div>

@else

<div class="liveusers-group-card card mb-4 px-2 no_data_found-mob">
        @php $data_arr=['error_name'=>'No Upcoming events found']; @endphp
        @include('frontend.common.no-data',$data_arr)
        
</div>
@endif