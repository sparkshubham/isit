
@extends('frontend.layouts.app')
@section('content')


   <section class="main-wrapper">
        <div class="container">                
            <div class="container-card">
                <div class="user-profile-section">
                    <div class="user-profile">
                        
                        <div class="user-profile-img" style="background: url('{{ asset('front/images/userprofile-bg.svg')}}');">
                        @if($user['profile_picture'])
                        <img src="{{$path.$user['profile_picture']}}" alt="Lock">
                        @else
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                        <img height="45px" width="45px" src="{{$profile_url}}" alt="Lock">
                        @endif
                            
                        </div>
                       
                        <div class="user-profile-name">
                            <h2>{{$user['first_name']." ".$user['last_name']}} </h2>
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
                            <li><a href="{{route('-my')}}"><img src="{{asset('front/images/calendar.svg') }}"alt=""> Event</a></li>
                            <li><a href="#"><img src="{{asset('front/images/go-live.svg') }}" alt=""> Go Live</a></li>
                            <li><a href="{{route('store-my')}}"><img src="{{asset('front/images/shopping-bag.svg') }}"  alt=""> Store</a></li>
                            <li><a href="#"><img src="{{asset('front/images/mail-message.svg') }}" alt=""> Message</a></li>
                            <!-- <li><a href=""><img src="images/settings.svg" alt=""> Settings</a></li> -->
                        </ul>
                    </div>

                    @else
                    <div class="profile-follows">
                        <div class="totalfollowers">
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
                        <div class="totalfollowers">
                            <a href="#" class="btn"><img  src="{{asset('front/images/mail-message.svg') }}" alt=""> Message</a>
                        </div>
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
                        </ul>
                    @endif

                      <div class="tab-content" id="tabs-tabContent">
                          
                        <div class="tab-pane fade show active" id="my-posts" role="tabpanel" aria-labelledby="my-posts-tab">                            
                            <!--- \\\\\\\Post-->
                            @if($user['is_public_visibility']==1|| Auth::user()->id==$user['id'])
                                @if(count($my_post['posts'])>0)
                                    @foreach($my_post['posts'] as $my_post_value)
                                   @php 
                                        $response_arr['posts'] =$my_post_value;
                                        $response_arr['path']['user_media'] =$my_post['path']['user_media'];
                                        $response_arr['path']['post_media'] =$my_post['path']['post_media'];
                                    @endphp

                                        @include('frontend.common.post-view',$response_arr)

                                    @endforeach
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
                            @if(!empty($saved_post['posts']))
                                @foreach($saved_post['posts'] as $my_post_save_value)
                                        @php 
                                            $response_arr['posts'] =$my_post_save_value;
                                            $response_arr['path']['user_media'] =$my_post['path']['user_media'];
                                            $response_arr['path']['post_media'] =$my_post['path']['post_media'];
                                        @endphp
                                           @include('frontend.common.post-view',$response_arr)
                                @endforeach
                            @else  
                                @include('frontend.common.no-data')
                            @endif
                        </div>
                        @endif
                      </div>
                      
                </div>
        </div>
    </section>

    
@endsection
