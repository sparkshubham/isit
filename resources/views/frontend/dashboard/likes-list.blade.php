<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="totalLikesListlLabel">Likes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">  
                
            @if(!empty($users))
            <div class="following-users scrollbar-outer liked_user_list_scroll_class" id="liked_user_list_scroll">
                @foreach($users as $usersValue) 
                @if($usersValue->getUser)
               
                    <div class="follow-single-user">
                        <div class="follow-username-img">
                            <div class="followuser-img">
                                @if(isset($usersValue->getUser->profile_picture) && !empty($usersValue->getUser->profile_picture))
                                                <img src="{{$path.$usersValue->getUser->profile_picture}}" alt="">
                                @else
                                @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                <img src="{{$profile_url}}" alt="Lock">
                                @endif
                                <!-- <img src="{{$path.$usersValue->getUser->profile_picture}}" alt=""> -->
                            </div>
                            <div class="followuser-name">
                                <a href="{{route('user-profile', ['user_id' => $usersValue->getUser->id])}}"> {{$usersValue->getUser->first_name.' '.$usersValue->getUser->last_name}}  </a>
                            </div>
                        </div>
                        
                        <span class="likes_time">{{  time_elapsed_string(convertIntTodate($usersValue->created_at))}}</span>
        
                    </div>
             
                @endif
                @endforeach
                </div>
            @endif
            </div>
    </div>
    
<script>
   
    
    // $(".liked_user_list_scroll_class").scroll(function () {
    
    // var notifi = document.getElementById("liked_user_list_scroll");
    // if (notifi.scrollHeight - notifi.scrollTop == notifi.clientHeight) {
    //     page_noti++;
      
    //     alert('shubham')
    // }
    // });
    
    
    $(document).ready(function(){
      $(".liked_user_list_scroll_class").scroll(function(){
        // alert('shubhm')
      });
    });
         
    </script>