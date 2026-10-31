@if(count($users)>0)
@if(!empty($users))
        @foreach($users as $userValue)

                <div class="follow-single-user block-user-count" id="hide_show_block{{$userValue['id']}}">
                    <div class="follow-username-img">
                        <!-- <div class="followuser-img">
                            <img src="{{$path['user_media'].$userValue['profile_picture']}}" alt="">
                        </div> -->
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                        <div class="followuser-img">
                            @if($userValue['profile_picture'])
                            @php $profile_url=$path['user_media'].$userValue['profile_picture']@endphp
                               <img src="{{$path['user_media'].$userValue['profile_picture']}}" alt="">
                            @else
                               
                                <input type="hidden" id="broadcaster_img" value="{{$profile_url}}"> 
                            @endif
                      
                            
                        </div>
                        <div class="followuser-name">
                            <a href="#">{{$userValue['first_name'].' '.$userValue['last_name']}}</a>
                        </div>
                    </div>
                    <div class="follow-btn">
                        <button flag="1"  broadcaster_img ="{{$profile_url}}" broadcaster_name="{{$userValue['first_name'].' '.$userValue['last_name']}}"type ="button" class="btn btn-secondary user_join_live" other-user-id="{{$userValue['id']}}">Watch</button>
                    </div>
                </div>

              
        @endforeach
@endif
@else
    @php $data_arr=['error_name'=>'No Data found']; @endphp
    @include('frontend.common.no-data',$data_arr)
@endif