@extends('frontend.layouts.app')
@section('content')

<section class="main-wrapper">
    <div class="" id="video_stream_go_live" >
                
    </div>    
    <div class="container-card video_time_hide">
        <div class="card blockedusers-card">
            <div class="card-header bg-white">
                Live Users
            </div>
            <div class="card-body">
                @if(count($users) > 0)
                <div class="following-users scrollbar-outer" id="live_users_list">
                                        @php 
                                            $data_arr['users']=$users;
                                        @endphp
                                        @include('frontend.agora.list',$data_arr)
                                    </div>
                                    @else
                                    @php 
                                        $data_arr=['error_name'=>'No live users found'];
                                    @endphp
                                    @include('frontend.common.no-data',$data_arr)
                </div>
                
                @endif
                   
            </div>
        </div>
    </div>
    </section>

@endsection
