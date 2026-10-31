@extends('frontend.layouts.app')
@section('content')



<section class="main-wrapper">
    <div class="container-card">
        <div class="card pending-requests-card">
            <div class="card-header bg-white">
                Pending Requests
            </div>
            
            <div class="card-body">
                        <div class="following-users scrollbar-outer" id="pending_request">
            @if(!empty($users))
                @foreach($users as $usersValue)
                            <div class="follow-single-user pending-user-count" id="accept_reject_hide{{$usersValue['id']}}">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        @if($usersValue['profile_picture'])
                                        <img src="{{$path.$usersValue['profile_picture']}}" alt="">
                                        @else
                                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                        <img src="{{$profile_url}}" alt="Lock">
                                        @endif
                                        <!-- <img src="{{$path.$usersValue['profile_picture']}}" alt=""> -->
                                    </div>
                                    <div class="followuser-name">
                                    <a href="{{route('user-profile', ['user_id' => $usersValue['id']])}}">{{$usersValue['first_name'].' '.$usersValue['last_name']}}</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button onclick="acceptRequest({{$usersValue['id']}})" class="btn btn-primary">Accept</button>
                                    <button onclick="declineRequest({{$usersValue['id']}})" class="btn btn-secondary">Decline</button>
                                </div>
                            </div>
               @endforeach
               
               </div>
                    </div>
               @else  
                   @include('frontend.common.no-data')
              @endif
     </div>
    </div>
    </section>
   
@endsection


@section('page_wise_scripts')
<script>
        function acceptRequest(id)
        {
            var data ={
                user_id:id
            };
            var url = `{{ url('accept-following-request') }}`;
            var methodType='Post';
            callajax(url,methodType,data)
            // $('#accept_reject_hide'+id).addClass('hide')
            var length=$('.pending-user-count').length;
            var pendLn= length-1
            if(pendLn==0)
            {
                    var path=baseurl+'/front/images/nodata.svg';
                    html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No user found</p></div>';
                    $('#pending_request').html(html);
            }
            
        $('#accept_reject_hide'+id).remove();
          
        }

        function declineRequest(id)
        {
            var data ={
                user_id:id
            };
            var url = `{{ url('decline-following-request') }}`;
            var methodType='Post';
            callajax(url,methodType,data)
            // $('#accept_reject_hide'+id).addClass('hide')
            var length=$('.pending-user-count').length;
            var pendLn= length-1
            if(pendLn==0)
            {
                    var path=baseurl+'/front/images/nodata.svg';
                    html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No user found</p></div>';
                    $('#pending_request').html(html);
            }
            $('#accept_reject_hide'+id).remove();
         
        }


</script>

@endsection
