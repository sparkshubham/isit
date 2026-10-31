  <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="totalLikesListlLabel">Likes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">  
                
            @if(!empty($users))
            <div class="following-users scrollbar-outer">
                @foreach($users as $usersValue)
                
                @if($usersValue)
                    <div class="follow-single-user">
                        <div class="follow-username-img">
                            <div class="followuser-img">
                                @if(isset($usersValue['profile_picture']) && !empty($usersValue['profile_picture']))
                                                <img src="{{$path.$usersValue['profile_picture']}}" alt="">
                                @else
                                @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                <img src="{{$profile_url}}" alt="Lock">
                                @endif
                            </div>
                            <div class="followuser-name">
                                <a href="{{route('user-profile', ['user_id' => $usersValue['id']])}}"> {{$usersValue['first_name'].' '.$usersValue['last_name']}}  </a>
                            </div>
                        </div>
                        
                        <span class="likes_time">{{  time_elapsed_string(convertIntTodate($usersValue['createdAt']))}}</span>
        
                    </div>
             
                @endif
                @endforeach
                </div>
               @else
                <div class="no_data_found">
                    <img src="{{ asset('front/images/nodata.svg') }}" alt="">
                    <p>No Likes found</p>

                </div>
                @endif 
            </div>
    </div>