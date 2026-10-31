
@extends('frontend.layouts.app')
@section('content')


   <section class="main-wrapper">
        <div class="" id="video_stream_go_live"> 
        </div>
        <div class="container" id="user_profile_data">                
            <div class="container-card">
                <div class="user-profile-section">
                    <div class="user-profile">
                         @php @$user['is_star'] @endphp
                         
                        <div class="user-profile-img" style="background: url('{{ asset('front/images/userprofile-bg.svg')}}');">
                        @if($user['profile_picture'])
                        <img src="{{$path.$user['profile_picture']}}" alt="Lock">
                        <input type="hidden" id="broadcaster_img" value="{{$path.$user['profile_picture']}}"> 
                        @else
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                        <img height="45px" width="45px" src="{{$profile_url}}" alt="Lock">
                        <input type="hidden" id="broadcaster_img" value="{{$profile_url}}"> 
             
                        @endif

                            @if(@$user['is_star']==1)
                                <div class="star_div" style="position:absolute;">
                                    @php $star = url('/front/images/user-star.svg');@endphp
                                    <img height="10px" width="10px" src="{{$star}}" alt="Lock">
                                </div>
                            @endif
                        </div>
                        <input type="hidden" id="broadcaster_name" value="{{$user['first_name'].' '.$user['last_name']}}"> 
                        <div class="user-profile-name">
                       
                            <h2>{{$user['first_name']." ".$user['last_name']}}
                         
                                
                        </h2>
                            @if(Auth::user()->id==$user['id'])
                            <a class="viewprofile" href="{{url('profile')}}" >View Profile</a>
                            @endif
                        </div>
                       
                    </div>
                    <div class="profile-follows">
                        <div class="totalfollowers">
                        @php $urlroute="#"; $handicon="default-cursor";  @endphp
                        @if(Auth::user()->id==$user['id'])
                    
                        @php   $handicon=""; $urlroute=route('follower-unfollower', ['user_id' => $user['id'],'type' => 'following']); @endphp
                        @endif
                        @php $urlfollower="#" @endphp
                        @if(Auth::user()->id==$user['id'])
                        @php $urlfollower=route('follower-unfollower', ['user_id' => $user['id'],'type' => 'follower']); @endphp
                        @endif
                            <h4><a  class="{{$handicon}} color_black" href="{{$urlroute}}">{{$user['following_count']}}</a></h4>
                            <p>Following</p>
                            <input type="hidden" id="following_count" value="{{$user['following_count']}}"/>
                        </div>                            
                        <div class="totalfollowers">
                           
                            <h4><a class="color_black {{$handicon}}" href="{{$urlfollower}}" id="total_follow_count"> {{$user['follower_count']}}</a></h4>
                            <p>Followers</p>
                            <input type="hidden" id="follower_count" value="{{$user['follower_count']}}"/>
                        </div>
                    </div>
                    @if(Auth::user()->id==$user['id'])
                    <div class="profile-follows myposts-btns">
                        <ul>
                            <li><a href="{{route('event-list-my') }}"><img src="{{asset('front/images/calendar.svg') }}"alt=""> Event</a></li>
                             @if($user['is_live']==1)
                              <li><a href="#" onclick="disconnectAndcontinueGolive()" ><img src="{{asset('front/images/go-live.svg') }}" alt=""> Go Live</a></li>
                             @else
                              <li><a href="#"  id="user_go_live"><img src="{{asset('front/images/go-live.svg') }}" alt=""> Go Live</a></li>
                             @endif
                            <li><a href="{{route('store-my')}}"><img src="{{asset('front/images/shopping-bag.svg') }}"  alt=""> Store</a></li>
                            <!-- <li class="message_hide"><a href="#" data-user-twillio-id="{{$user['twilio_s_id']}}"   data-tc-fullname="{{$user['first_name']}} {{$user['last_name']}}"   data-tc-name="{{$user['first_name']}} {{$user['last_name']}}" data-tc-profile="{{$path.$user['profile_picture']}}"  data-tc-user-id="{{$user['id']}}"><img src="{{asset('front/images/mail-message.svg') }}" alt=""   > Message</a></li> -->
                            <!-- <li><a href=""><img src="images/settings.svg" alt=""> Settings</a></li> -->
                        </ul>
                        
                    </div>

                    @else
                    <div class="profile-follows">
                        <div class="totalfollowers border-0">
                                <input type="hidden" id="public_visible_{{$user['id']}}" value="{{$user['is_public_visibility']}}"/>
                                @if($user['is_follow']==1)
                                <input type="hidden" id="follow_val_{{$user['id']}}" value="1">
                                <img  id="img_follow_unfollow{{$user['id']}}" src="{{asset('front/images/unfollow-user.svg') }}" alt=""><button id="btn_txt_follow_unfollow_{{$user['id']}}" onclick="followUnfollowUser({{$user['id']}})" class="btn"> Unfollow</button>
                                @elseif($user['is_follow']==2)
                                <input type="hidden" id="follow_val_{{$user['id']}}" value="1">
                                <img id="img_follow_unfollow{{$user['id']}}" src="{{asset('front/images/unfollow-user.svg') }}" alt=""><button id="btn_txt_follow_unfollow_{{$user['id']}}" onclick="followUnfollowUser({{$user['id']}})" class="btn" disabled> Requested</button>
                                @else
                                <input type="hidden" id="follow_val_{{$user['id']}}" value="0">
                                <img id="img_follow_unfollow{{$user['id']}}" src="{{asset('front/images/follow.svg') }}" alt=""/><button id="btn_txt_follow_unfollow_{{$user['id']}}" onclick="followUnfollowUser({{$user['id']}})" class="btn ">Follow</button>
                                @endif
                            
                               
                               
                           
                        </div>                            
                        <div class="totalfollowers message_hide">
                           
                            @if($user['profile_picture'])
                                    @php $profile_url=$path.$user['profile_picture']@endphp
                                    @else
                                    @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                            @endif
                            <a  class="btn tc-users "  href="#" data-user-twillio-id="{{$user['twilio_s_id']}}"   data-tc-fullname="{{$user['first_name']}} {{$user['last_name']}}"   data-tc-name="{{$user['first_name']}} {{$user['last_name']}}" data-tc-profile="{{$profile_url}}"  data-tc-user-id="{{$user['id']}}"><img src="{{asset('front/images/mail-message.svg') }}" alt=""   > Message</a>
                        </div>
                        @if($user['is_live']==1)
                        <div class="totalfollowers border-left">
                            <a  id="user_join_live" other-user-id="{{$user['id']}}" class="btn d-flex align-items-center" href="#" ><img src="{{asset('front/images/tv-solid.svg') }}" alt=""   > Watch</a>
                        </div>
                        @endif
                    </div>
                    @endif  
                    <div class="aboutuser">
                        <p> {{$user['bio_text']}}</p>
                    </div>
                </div>
                <div class="mypost-and-save-post-section">
                    @if(Auth::user()->id==$user['id'])
                        <ul class="nav nav-tabs mb-3" id="tabs-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="my-posts-tab" data-bs-toggle="pill" data-bs-target="#my-posts" type="button" role="tab" aria-controls="my-posts" aria-selected="true">My Posts</button>
                            </li>
                            <li class="nav-item" role="presentation">
                            <button class="nav-link" id="saved-posts-tab" data-bs-toggle="pill" data-bs-target="#saved-posts" type="button" role="tab" aria-controls="saved-posts" aria-selected="false">Saved</button>
                            </li>
                            <input type="hidden" id="my_save_post_flag" value="0">
                        </ul>
                        
                    @endif

                      <div class="tab-content" id="tabs-tabContent">
                          
                        <div class="tab-pane fade show active" id="my-posts" role="tabpanel" aria-labelledby="my-posts-tab">                            
                            <!--- \\\\\\\Post-->
                            @if($user['is_follow']==1 || $user['is_public_visibility']==1|| Auth::user()->id==$user['id'])
                                @if(count($my_post['posts'])>0)
                                        @include('frontend.user-profile.my-post')
                                @else  
                                        @include('frontend.common.no-data')
                                @endif
                            @else
                            @php $data_arr=['error_name'=>'Private User']; @endphp
                               @include('frontend.common.no-data')
                            @endif
                        </div>
                        @if(Auth::user()->id==$user['id'])
                        <div class="tab-pane fade" id="saved-posts" role="tabpanel" aria-labelledby="saved-posts-tab">
                        @if($user['is_follow']==1 ||  $user['is_public_visibility']==1|| Auth::user()->id==$user['id'])
                                @if(count($saved_post['posts'])>0)
                                @php  $my_post['posts']=$saved_post['posts'];@endphp
                                         @include('frontend.user-profile.my-post',$my_post['posts'])
                                @else  
                                        @include('frontend.common.no-data')
                                @endif
                            @else
                            @php $data_arr=['error_name'=>'Private User']; @endphp
                               @include('frontend.common.no-data')
                            @endif
                        </div>
                        @endif
                      </div>
                      
                </div>
        </div>
    </section>

    
@endsection
