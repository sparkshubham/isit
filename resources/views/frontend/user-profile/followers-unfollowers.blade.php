@extends('frontend.layouts.app')
@section('content')

   
<section class="main-wrapper">
    <div class="container-card">
        <div class="card followers-card">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link {{request()->type=='follower'?'active':''}}" id="followers-tab" data-bs-toggle="tab" data-bs-target="#followers" type="button" role="tab" aria-controls="followers" aria-selected="true">Followers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link  {{request()->type=='following'?'active':''}}" id="following-tab" data-bs-toggle="tab" data-bs-target="#following" type="button" role="tab" aria-controls="following" aria-selected="false">Following</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade {{request()->type=='follower'?' show active':''}}" id="followers" role="tabpanel" aria-labelledby="followers-tab">
                        <div class="search-users">
                            <form class="search_follower_following">
                                <input class="search-icon" type="search" id="follower_search" placeholder="Search">
                                <!-- <img src="{{ asset('front/images/icons8-x-50.png')}}" id="follower_search_arrow" alt=""> -->
                            </form>
                        </div>
                        <div class="following-users scrollbar-outer"  id="followers_users">
                        @if(!empty($followers))
                            @if(!empty($followers['followersusers']))
                                @foreach($followers['followersusers'] as $followersValue)
                                        <div class="follow-single-user follower-users-count">
                                            <div class="follow-username-img">
                                                <div class="followuser-img">
                                                @if($followersValue['profile_picture'])
                                                <img src="{{$path.$followersValue['profile_picture']}}" alt="">
                                                @else
                                                @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                <img src="{{$profile_url}}" alt="Lock">
                                                @endif
                                                </div>
                                                <div class="followuser-name">
                                                    <a href="{{route('user-profile', ['user_id' => $followersValue['id']])}}">{{$followersValue['first_name']}} {{$followersValue['last_name']}}</a>
                                                    <div class="user-post-followers">
                                                        <span class="total-posts">{{$followersValue['post_count']}} Posts</span>
                                                        <span class="total-followers">{{$followersValue['follower_count']}} followers</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="follow-btn ">
                                            <a href="{{route('user-profile', ['user_id' => $followersValue['id']])}}"><img src="{{ asset('front/images/chevron-right.svg')}}" alt=""></a>
                                            
                                                <!-- <button onclick="removeFollower({{$followersValue['id']}})" class="btn btn-secondary">Remove</button> -->
                                            </div>
                                        </div>
                                @endforeach
                            @else
                          
                            @include('frontend.common.no-data')
                            @endif
                        @endif
                        </div>  
                    </div>
                    <div class="tab-pane fade {{request()->type=='following'?' show active':''}}" id="following" role="tabpanel" aria-labelledby="following-tab">
                        <div class="search-users">
                            <form class="search_follower_following">
                                <input class="search-icon" type="search" id="following_search"  placeholder="Search">
                                <!-- <img class="search_icon_list" src="{{ asset('front/images/icons8-x-50.png')}}" id="following_search_arrow" alt=""> -->
                            </form>
                        </div>
                        <div class="following-users scrollbar-outer" id="following_users">
                         
                        @if(!empty($following))
                            @if(!empty($following['followingusers']))
                                @foreach($following['followingusers'] as $followingValue)
                                        <div class="follow-single-user following-users-count" id="follow-single-user_{{$followingValue['id']}}">
                                            <div class="follow-username-img">
                                                <div class="followuser-img">
                                                @if($followingValue['profile_picture'])
                                                <img src="{{$path.$followingValue['profile_picture']}}" alt="">
                                                @else
                                                @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                <img src="{{$profile_url}}" alt="Lock">
                                                @endif
                                               
                                                </div>
                                                <div class="followuser-name">
                                                    <a href="{{route('user-profile', ['user_id' => $followingValue['id']])}}">{{$followingValue['first_name']}} {{$followingValue['last_name']}}</a>
                                                    <div class="user-post-followers">
                                                        <span class="total-posts">{{$followingValue['post_count']}} Posts</span>
                                                        <span class="total-followers">{{$followingValue['follower_count']}} followers</span>
                                                  
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="follow-btn">  
                                                @php $userData= getProfileData($followingValue['id']);
                                                $is_follow=$userData['is_follow'];
                                                @endphp

                                                @if($is_follow==1)
                                                <button  type="button" class="btn btn-secondary" onclick="removeFollowing({{$followingValue['id']}})" id="btn_txt_follow_unfollow_{{$followingValue['id']}}"> Unfollow</button>
                                                @else
                                               
                                                <button type="button" class="btn btn-secondary" onclick="removeFollowing({{$followingValue['id']}})" id="btn_txt_follow_unfollow_{{$followingValue['id']}}">Follow</button>
                                                @endif
                                            </div>
                                        </div>
                                @endforeach
                            @else
                            @include('frontend.common.no-data')
                            @endif
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>

@endsection




@section('page_wise_scripts')
<script>
        function removeFollowing(id)
        {
            var data ={
                follower_id:id
            };
            var url = `{{ url('unfollow-user') }}`;
            var methodType='Post';
            callajax(url,methodType,data)
            $('#follow-single-user_'+id).addClass('hide');
            var length=$('.following-users-count').length;
            var blockLen= length-1
            if(blockLen==0)
            {
                    var path=baseurl+'/front/images/nodata.svg';
                    html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No data found</p></div>';
                    $('#following_users').html(html);
            }
        }
        function removeFollower(id)
        {
            var data ={
                follower_id:id
            };
            var url = `{{ url('unfollow-user') }}`;
            var methodType='Post';
            callajax(url,methodType,data)

            var length=$('.follower-users-count').length;
            var blockLen= length-1
            if(blockLen==0)
            {
                    var path=baseurl+'/front/images/nodata.svg';
                    html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No data found</p></div>';
                    $('#followers_users').html(html);
            }
        }

                
        $('#following_search').on('keyup',function(){
                $type="{{request()->type}}"
                $user_id="{{request()->user_id}}"
                $value=$(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type : 'post',
                    url : baseurl+'/following-list',
                    data:{
                        'search_keyword':$value,
                        'user_id':$user_id,
                    },
                    success:function(data){
                        $('#following_users').html(data.following_list_html);
                    }
               });
               
           })

                $('#follower_search').on('keyup',function(){
                        $type="{{request()->type}}"
                        $user_id="{{request()->user_id}}"
                        $value=$(this).val();
                        $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                }
                        });
                        $.ajax({
                            type : 'post',
                            url : baseurl+'/follower-list',
                            data:{
                                'search_keyword':$value,
                                'user_id':$user_id,
                            },
                        success:function(data){
                            $('#followers_users').html(data.followers_list_html);
                           
                            console.log(data.followers_list_html) 
                            }
                        });

                })

$('.search_follower_following').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 
    e.preventDefault();
    return false;
  }
});

</script>

@endsection