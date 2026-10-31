
                @if(count($notifications)>0)
                    @foreach($notifications as $notificationVal)
                        <div class="follow-single-user">
                            <div class="follow-username-img">
                                <div class="followuser-img">
                                  
                                @if($notificationVal['userData']['profile_picture'])
                                <img src="{{$path['user_media'].$notificationVal['userData']['profile_picture']}}" alt="#">
                                @else
                                @php  $profile_url = url('/front/images/placeholder-user.png'); @endphp
                                <img src="{{$profile_url}}" alt="#">

                                @endif
                                   
                                </div>
                                <div class="followuser-name" style="cursor:pointer;">
                                    <div class="notification-username">
                                        <?php
                                         $url_dynamic="#";
                                        
                                        if(!empty($notificationVal['other_id'])){
                                          
                                           $url_dynamic=route('user-profile', ['user_id' => $notificationVal['other_id']]);
                                        }
                                        else if(!empty($notificationVal['post_id'])){
                                            $url_dynamic=route('explore-view', ['post_id' => $notificationVal['post_id']]);
                                        }
                                        else if(empty($notificationVal['other_id']) && empty($notificationVal['post_id']))
                                        {
                                           $url_dynamic=route('pending-request');
                                        }
                                        ?>
                                        <a class="d-block" href="{{$url_dynamic}}">
                                        <span>{{$notificationVal['message'];}}</span></a>
                                    </div>
                                    <div class="user-post-followers">
                                        <div class="date"><i class="uil uil-calender"></i> {{ time_elapsed_string(convertIntTodate($notificationVal['created_at']))}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-img " >
                                <?php
                            if(!empty($notificationVal['post_id'])){
                                if(!empty($notificationVal['postData']))
                                {
                                ?>
                                <img src="{{$path['post_media'].$notificationVal['postData']['file_name']}}"  alt="#">

                                     <?php
                                }
                            }
                            ?>
                            </div>
                        </div>
                    @endforeach
                @else
                   @php $data_arr=['error_name'=>'No Data found']; @endphp
                   @include('frontend.common.no-data',$data_arr)
                @endif