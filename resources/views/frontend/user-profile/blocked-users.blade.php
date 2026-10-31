@extends('frontend.layouts.app')
@section('content')

<section class="main-wrapper">
    <div class="container-card">
        <div class="card blockedusers-card">
            <div class="card-header bg-white">
                Blocked Users
            </div>
            <div class="card-body">
                <div class="following-users scrollbar-outer" id="blocked_userss">
                    @if(!empty($users))
                            @foreach($users as $userValue)
                                    <div class="follow-single-user block-user-count" id="hide_show_block{{$userValue['id']}}">
                                        <div class="follow-username-img">
                                            <div class="followuser-img">
                                                <img src="{{$path.$userValue['profile_picture']}}" alt="">
                                            </div>
                                            <div class="followuser-name">
                                                <a href="#">{{$userValue['first_name'].' '.$userValue['last_name']}}</a>
                                                <div class="user-post-followers">
                                                        <span class="total-posts">{{$userValue['following_count']}} Following</span>
                                                        <span class="total-followers">{{$userValue['follower_count']}} followers</span>
                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="follow-btn">
                                            <button onclick ="unblockUser({{$userValue['id']}})"class="btn btn-secondary">Unblock</button>
                                        </div>
                                    </div>
                            @endforeach
                    @else  
                    @include('frontend.common.no-data')
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
    </section>

@endsection
