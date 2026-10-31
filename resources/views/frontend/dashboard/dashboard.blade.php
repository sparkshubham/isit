
@extends('frontend.layouts.app')
@section('content')
<section class="main-wrapper">
    <div class="home-page">
        <div class="follow-users-section video_time_hide">
            <button type="button" class="btn-close"></button>
            <ul class="more-links-sidebar">
                <li>
                    <a  href="{{route('live-user-list') }}" class="d-flex align-items-center active"><span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14">
                      <path id="Icon_material-tv" data-name="Icon material-tv" d="M16.955,4.5H3.045A1.555,1.555,0,0,0,1.5,6.056v9.333a1.555,1.555,0,0,0,1.545,1.556H6.909V18.5h6.182V16.944h3.864a1.549,1.549,0,0,0,1.538-1.556L18.5,6.056A1.555,1.555,0,0,0,16.955,4.5Zm0,10.889H3.045V6.056H16.955Z" transform="translate(-1.5 -4.5)" fill="#e85922"/>
                    </svg>

                    </span> Live User</a>
                </li>
                
                @if(Auth::user()->is_live==1)
                    <li>
                        <a  class="d-flex align-items-center" href="#" onclick="disconnectAndcontinueGolive()" ><span class="icon">
                        <img src="{{asset('front/images/go-live.svg') }}" alt=""></span>  Go Live</a>
                    </li>
                @else
                    <li>
                        <a  class="d-flex align-items-center" href="#"  id="user_go_live"><span class="icon">
                        <img src="{{asset('front/images/go-live.svg') }}" alt=""> </span>Go Live</a>
                    </li>
                @endif
            
                <!-- <li>
                     <a  href=" {{route('store-other') }}" class="d-flex align-items-center active"><span class="icon"><img src="{{asset('front/images/shopping-bag.svg') }}" alt=""></span> Store</a>
                </li>
                <li>
                    <a  href="{{route('event-list-other') }}" class="d-flex align-items-center"><span class="icon"><img src="{{asset('front/images/calendar.svg') }}"  alt=""></span> Events</a>
                </li> -->
                <li> 
                    <a href="{{route('pending-request')}}" class="d-flex align-items-center"><span class="icon"><img src="{{asset('front/images/users.svg') }}" alt=""></span> Requests <span class="badge">{{$request_user_count}}</span></a>
                </li>
            </ul>
            @if(Auth::user()->id)

            @php   $handicon=""; $urlroute=route('follower-unfollower', ['user_id' => Auth::user()->id,'type' => 'following']); @endphp
            @endif
            <div class="following-title d-flex align-items-center justify-content-between mb-2">
                <h4 class="font-14 mb-2">Following</h4> 
                <a href="{{$urlroute}}" class="font-12 mb-2">See All</a>
            </div>
            <div class="following-users scrollbar-outer">           
            <!-- <div class="d-flex align-items-center  justify-content-between"><h2 class="font-14">Following</h2> 
            <a href="{{$urlroute}}"  class="font-12">See All</a></div> -->

                @if(count($following['followingusers'])>0)
                    @foreach($following['followingusers'] as $followingValue)
                        <div class="follow-single-user">
                            <div class="follow-username-img">
                                <div class="followuser-img">
                                    @if($followingValue['profile_picture'])
                                                <img src="{{$path['user_media'].$followingValue['profile_picture']}}" alt="">
                                    @else
                                    @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                    <img src="{{$profile_url}}" alt="Lock">
                                    @endif
                                
                                </div>
                                <div class="followuser-name">
                                    <a href="{{route('user-profile', ['user_id' => $followingValue['id']])}}">{{$followingValue['first_name'].' '.$followingValue['last_name']}}</a>
                                    <div class="user-post-followers">
                                        <span class="total-posts">{{$followingValue['post_count']}} Posts</span>
                                        <span class="total-followers">{{$followingValue['follower_count']}} followers</span>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                @php $data_arr=['error_name'=>'No users found']; @endphp
                   @include('frontend.common.no-data',$data_arr)
                @endif

               
            </div>
        </div>
        

        <div class="store-events-section video_time_hide">
            <div class="scrollbar-outer">
                @include('frontend.agora.agora-index',['data'=>$available_events])
            
                
                <div class="hom-sidebar-slider">
                    <div class="sidebar-slider-title">
                        <h4>Store</h4>
                        <a  href="{{route('store-other')}}">See All</a>
                    </div>

                    @if(count($products)>0)
                    <div class="owl-carousel home-sidebar-slider owl-theme same-height">
                            @foreach($products as $product)
                                <article class="card thumbnail item">  
                                    <a class="home_sliderimg" href="{{ route('product-view',['product_id'=>$product->id]) }}" >
                                        <!-- @if(!empty($product->getMedia))
                                            @if(isset($product->getMedia[0]->file_name))
                                                <img src="{{$path['product_media'].$product->getMedia[0]->file_name}}" class="img-responsive" />
                                            @else
                                                <img src="https://unsplash.it/660/455?image=842" class="img-responsive" />
                                            @endif -->



                                    @php
                                        $media= $product->getMedia;
                                        $file_name='';                                         
                                    @endphp      
                                    @if(isset($media[0]))
                                      @php $file_name=$path['product_media'].$media[0]->file_name;@endphp
                                    @else
                                      @php $file_name =url('/front/images/placeholder-store.png');@endphp
                                    @endif
                                        @php $file_type=getFileTypeUsingName($file_name);@endphp
                                    @if($file_type==2)
                                         @php  $file_name=$path['product_media'].$media[0]->thumb_image_name;@endphp
                                    @endif

                                            <img src="{{$file_name}}" alt="#">

                                    @endif
                                    </a>
                                    <div class="caption">
                                        <h3>{{($product->price_type==1)?'Free':$product->currency.' '.$product->price;}}</h3>
                                        <a  href="#">{{$product->name}}</a>
                                        <p><i class="uil uil-map-marker"></i> {{getCountryNameById($product->country_id)}} {{$product->city}} {{$product->zip}}</p>
                                    </div>  
                                </article>
                            @endforeach
                    </div>
                  
                    @else
                        @php $data_arr=['error_name'=>'No product found']; @endphp
                        @include('frontend.common.no-data',$data_arr)
                    @endif
                    <!-- #owl-demo-2 -->
                </div>
                
                <div class="hom-sidebar-slider">
                    <div class="sidebar-slider-title">
                        <h4>Events</h4>
                        <a href="{{route('event-list-other')}}">See All</a>
                    </div>
                    
                    @if(count($events)>0)
                    <div class="owl-carousel home-sidebar-slider owl-theme">
                        @foreach($events as $event)
                            <div class="card single-event">
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

                                                <div class="item home_sliderimg" data-merge="{{$event->id}}">
                                                        <img src="{{$file_name}}" alt="#">
                                                </div>
                                            
                                            </div>      
                                        </a>
                                </div>
                                
                                <h4>{{$event->name}}</h4>
                                <ul>
                                    <li><i class="uil uil-calender"></i> {{convertIntTodate($event->date,'d M, Y')}}</li>
                                        @php   
                                        $time_zone='Asia/Calcutta';
                                            $startDate= convertTimeToUSERzone(convertIntTodate($event->start_time),$time_zone);
                                            $endDate=convertTimeToUSERzone(convertIntTodate($event->end_time),$time_zone);
                                            $startDateNew = date('h:i A', strtotime($startDate));
                                            $endDateNew = date('h:i A', strtotime($endDate));
                                        @endphp
                                    <li><i class="uil uil-clock"></i>{{$startDateNew}}-{{$endDateNew}} </li>
                                    <!-- <li><i class="uil uil-map-marker"></i> </li> -->
                                </ul>
                             <div class="text-center">

                                @if($event->is_saved==1)
                                <a class="btn btn-primary" onclick="removeSaveEvent({{$event->id}},{{Auth::user()->id}})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16">
                                        <path class="bookmark_{{$event->id}}" id="Icon_feather-bookmark" data-name="Icon feather-bookmark" d="M15.167,17.5l-5.5-4.167L4.167,17.5V4.167A1.621,1.621,0,0,1,5.738,2.5H13.6a1.621,1.621,0,0,1,1.571,1.667Z" transform="translate(-3.667 -2)" fill="#fff" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                    </svg>
                                    <span id="evnt_txt_{{$event->id}}"> Unsave the Date</span></a>
                                    <input type="hidden" id="is_saved_event_{{$event->id}}" value="{{$event->is_saved}}">
                                @else
                                <a class="btn btn-primary" onclick="removeSaveEvent({{$event->id}},{{Auth::user()->id}})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16">
                                        <path class="bookmark_{{$event->id}}" id="Icon_feather-bookmark" data-name="Icon feather-bookmark" d="M15.167,17.5l-5.5-4.167L4.167,17.5V4.167A1.621,1.621,0,0,1,5.738,2.5H13.6a1.621,1.621,0,0,1,1.571,1.667Z" transform="translate(-3.667 -2)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                    </svg>
                                    
                                    <span id="evnt_txt_{{$event->id}}"> Save the Date</span></a>
                                    <input type="hidden" id="is_saved_event_{{$event->id}}" value="{{$event->is_saved}}">
                                @endif
</div>
                                    
                            </div>
                            @endforeach
                       </div>
                    @else
                            @php $data_arr=['error_name'=>'No events found']; @endphp
                            @include('frontend.common.no-data',$data_arr)
                    @endif
                </div>
            </div>
        </div>

        <div class="storyset-section">    
            <div class="container-card" id="video_stream_go_live" >
                
            </div>                        
            <div class="container-card" id="post_board_data" >
                    @include('frontend.dashboard.dashboard-mid')
            </div>
            
            @if(count($posts)>0)

            @else
                    @php $data_arr=['error_name'=>'No posts found']; @endphp
                    @include('frontend.common.no-data',$data_arr)
            @endif
        </div>

    </div>
    
</section>
@endsection
