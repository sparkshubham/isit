@if($user['is_public_visibility']==1|| Auth::user()->id==$user['id'])
                                @if(count($saved_post['posts'])>0)
                                    @foreach($saved_post['posts'] as $my_post_value)
                                    <div class="single-card card shadow-sm" id="post_hide_show_{{$my_post_value->id}}">
                                        <div class="card-header bg-transparent border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="mr-2">
                                                    @if($user['profile_picture'])
                                                    <img  class="rounded-circle" height="45px" width="45px"   src="{{$saved_post['path']['user_media'].$my_post_value->getUser->profile_picture}}" alt="Lock">
                                                    @else
                                                    @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                    <img  class="rounded-circle" height="45px" width="45px"  src="{{$profile_url}}" alt="Lock">
                                                    @endif
                                                    </div>
                                                    <div class="ms-2">
                                                        <div class="h5 m-0">
                                                            {{$my_post_value->getUser->first_name.' '.$my_post_value->getUser->last_name}}
                                                        </div>
                                                        <div class="h7">{{ time_elapsed_string(convertIntTodate($my_post_value->created_at))}}</div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="dropdown">
                                                        <button class="btn dropdown-toggle p-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="uil uil-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="icon-img dropdown-menu dropdown-menu-end border-0 shadow-sm p-0" aria-labelledby="dropdownMenuButton1">
                                                                @if(Auth::user()->id==$my_post_value->getUser->id)
                                                                   <li><a class="dropdown-item" onclick="deletePost({{$my_post_value->id}})"> <img src="{{asset('front/images/trash.svg') }}" alt="">Delete Post</a></li>
                                                                @else
                                                                <li class="message_hide"><a class="dropdown-item" href="#"><img src="{{asset('front/images/mail.svg') }}" alt="">Message</a></li>
                                                                <li><a class="dropdown-item" href="#"> <img src="{{asset('front/images/flag.svg') }}" alt="">Report Post</a></li>
                                                                        @if($my_post_value->is_saved==1)
                                                                            <input type="hidden" id="saved_post_{{$my_post_value->id}}" value="1">
                                                                            <li><a class="dropdown-item unsaved_icon"  onclick="saveUnsavepost({{$my_post_value->id}},{{Auth::user()->id}})">  <img id="saved_post_img{{$my_post_value->id}}" src="{{asset('front/images/un-save.svg') }}" alt=""><span id="saved_post_text{{$my_post_value->id}}">Unsave Post</span></a></li>
                                                                        @else 
                                                                        
                                                                            <input type="hidden" id="saved_post_{{$my_post_value->id}}" value="0">
                                                                            <li><a class="dropdown-item unsaved_icon"  onclick="saveUnsavepost({{$my_post_value->id}},{{Auth::user()->id}})">  <img id="saved_post_img{{$my_post_value->id}}" src="{{asset('front/images/save.svg') }}" alt=""><span id="saved_post_text{{$my_post_value->id}}">Save Post</span></a></li>
                                                                        @endif 
                                                                        <input type="hidden" id="blocked_user_id_{{$my_post_value->getUser->id}}" value="1">
                                                       
                                                                      <li><a class="dropdown-item"  onclick="blockUnblockUser({{$my_post_value->getUser->id}},{{Auth::user()->id}})"><img src="{{asset('front/images/slash.svg') }}" alt="">Block User</a></li>
                                                                 
                                                                   
                                                                @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0"> 
                                            <div class="image-wrap">
                                                @if(!empty($my_post_value->getMedia['0']))
                                                    <div class="owl-carousel owl-theme"> 
                                                        @php $media=$my_post_value->getMedia['0'] @endphp
                                                            @if($media->media_type==2)
                                                                <div class="item-video active">
                                                                    <!-- Tag User List Start -->
                                                                    <!-- Tag User List End -->
                                                                    <a href="{{ route('explore-view',['post_id'=>$media->post_id]) }}">
                                                                        <video   class="video-js" controls="">
                                                                            <source src="{{$my_post['path']['post_media'].$media->file_name}}" type="video/mp4">
                                                                            <source src="{{$my_post['path']['post_media'].$media->file_name}}" type="video/ogg">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                    <a>
                                                                    <script src="//players.brightcove.net/1752604059001/217b1cd8-54d5-4ac7-b213-268a81d5081f_default/index.min.js"></script>
                                                                </div>
                                                            @else

                                                            <div class="item-video" data-merge="{{$media->id}}">
                                                            <a   href="{{ route('explore-view',['post_id'=>$media->post_id]) }}" href="#"><img src="{{$my_post['path']['post_media'].$media->file_name}}" /></a>
                                                            </div>
                                                            @endif
                                                    </div>
                                                @endif 
                                            </div>
                                            <div class="card-text">
                                                <h4>{{$my_post_value->heading}}</h4>
                                                <p>{{$my_post_value->description}}</p>
                                            </div>       
                                            <div class="card-footer bg-transparent" id="comments">
                                                <div class="like-comment-share">
                                                    <a class="{{ $my_post_value->is_liked==1 ?'liked_icon':''}}" id="like_unlike_css{{$my_post_value->id}}" onclick="likeUnlikePost({{$my_post_value->id}},{{Auth::id();}})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="17.003" height="17" viewBox="0 0 17.003 17">
                                                                <path id="Icon_feather-thumbs-up" data-name="Icon feather-thumbs-up" d="M11.421,7.267v-3.2a2.42,2.42,0,0,0-2.439-2.4l-3.252,7.2v8.8H14.9a1.618,1.618,0,0,0,1.626-1.36l1.122-7.2a1.583,1.583,0,0,0-.381-1.29,1.637,1.637,0,0,0-1.245-.55Zm-5.69,10.4H3.292a1.613,1.613,0,0,1-1.626-1.6v-5.6a1.613,1.613,0,0,1,1.626-1.6H5.731" transform="translate(-1.167 -1.167)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                                            </svg>
                                                    @if($my_post_value->is_liked==1)
                                                        <input type="hidden" id="liked_value_{{$my_post_value->id}}" value="1">
                                                        <span id="like_unlike_{{$my_post_value->id}}"> Liked</span>
                                                    @else
                                                        <input type="hidden" id="liked_value_{{$my_post_value->id}}" value="0">
                                                        <span id="like_unlike_{{$my_post_value->id}}"> Like</span>
                                                    @endif
                                                    </a>
                                                    

                                                    <a  onclick="getCommentListBypost({{$my_post_value->id}})" class="collapsed" data-bs-toggle="collapse" data-bs-target="#commentscollapse_{{$my_post_value->id}}" aria-expanded="false" aria-controls="commentscollapse">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14">
                                                            <path id="Icon_feather-message-square" data-name="Icon feather-message-square" d="M15.5,11.167a1.444,1.444,0,0,1-1.444,1.444H5.389L2.5,15.5V3.944A1.444,1.444,0,0,1,3.944,2.5H14.056A1.444,1.444,0,0,1,15.5,3.944Z" transform="translate(-2 -2)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                                        </svg> Comment</a>
                                                
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#sharePost">
                                                        <svg id="Group_3600" data-name="Group 3600" xmlns="http://www.w3.org/2000/svg" width="15.112" height="15.094" viewBox="0 0 15.112 15.094">
                                                            <path id="Path_5" data-name="Path 5" d="M-25.4,219.425c.981,0,1.962.007,2.943,0a4.018,4.018,0,0,0,.773-.1,1.377,1.377,0,0,0,1.255-1.326c.084-.671.1-1.351.118-2.028.018-.563,0-1.127,0-1.69a.583.583,0,0,1,.583-.628.583.583,0,0,1,.579.633,26.141,26.141,0,0,1-.146,3.41,6.851,6.851,0,0,1-.2.94,2.258,2.258,0,0,1-1.806,1.7,10.085,10.085,0,0,1-1.867.247c-1.671.034-3.343.028-5.015,0a8.7,8.7,0,0,1-1.546-.2,2.4,2.4,0,0,1-2-2.059,12.349,12.349,0,0,1-.207-2.458c0-1.411,0-2.823.034-4.234a9.73,9.73,0,0,1,.178-1.512,2.356,2.356,0,0,1,2.074-2.062,20.29,20.29,0,0,1,2.166-.209c.628-.035,1.26-.016,1.89-.017a.582.582,0,0,1,.627.584.588.588,0,0,1-.634.578c-.884.011-1.769.011-2.653.042a8.362,8.362,0,0,0-1.206.144,1.287,1.287,0,0,0-1.134,1.14,15.818,15.818,0,0,0-.174,2.041c-.017,1.52,0,3.041.027,4.561a8.045,8.045,0,0,0,.129,1.135,1.4,1.4,0,0,0,1.327,1.254c.654.077,1.314.117,1.973.142.635.023,1.272.005,1.908.005Z" transform="translate(31.935 -205.503)" fill="#001020"/>
                                                            <path id="Path_6" data-name="Path 6" d="M55.33,176.99c-1.1,0-2.153,0-3.206,0a.583.583,0,1,1,.012-1.164q1.708,0,3.416,0c.4,0,.8,0,1.2,0a.561.561,0,0,1,.573.573q0,2.317,0,4.633a.582.582,0,1,1-1.162-.006c0-1,0-2,0-3v-.208c-.073.07-.123.115-.17.162q-3.456,3.456-6.911,6.913a.613.613,0,0,1-.634.213.577.577,0,0,1-.29-.931c.042-.051.092-.1.139-.143l6.873-6.874C55.214,177.115,55.258,177.067,55.33,176.99Z" transform="translate(-42.213 -175.826)" fill="#001020"/>
                                                        </svg> Share</a>

                                                </div>
                                                <div class="total-likes-coments">
                                                    @php $total_likes=$my_post_value->total_likes.' Likes';@endphp
                                                    @if($my_post_value->total_likes==1)
                                                    @php  $total_likes= $my_post_value->total_likes.' Like';@endphp
                                                    @endif
                                                    <input type="hidden" value="{{$my_post_value->total_likes}}" id="data-total-like_{{$my_post_value->id}}">
                                                    <input type="hidden" value="{{$my_post_value->total_comments}}" id="data-total-comment_{{$my_post_value->id}}">
                                                    
                                                    <a id="like_unlike_css{{$my_post_value->id}}" onclick="likelistmodal({{$my_post_value->id}})" ><span id="total_count_like_{{$my_post_value->id}}">{{$total_likes}}</span></a>
                                                    <a onclick="getCommentListBypost({{$my_post_value->id}})" class="collapsed" data-bs-toggle="collapse" data-bs-target="#commentscollapse_{{$my_post_value->id}}" aria-expanded="false" aria-controls="commentscollapse">{{$my_post_value->total_comments}} Comments</a>
                                                </div>
                                            </div>
                                        
                                            <div id="commentscollapse_{{$my_post_value->id}}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#comments">
                                                <ul class="commentList scrollbar-outer" id="comment_list_{{$my_post_value->id}}">
                                                
                                                </ul>
                                                <form class="comment-form" role="form" id="comment_form" onsubmit="commentOnpost({{$my_post_value->id}},{{Auth::user()->id}})">
                                                    <div class="commenterinput">
                                                        <input class="form-control w-100" type="text" id="comment_value_{{$my_post_value->id}}" placeholder="Your comments" />
                                                        <button type="submit" onclick="commentOnpost({{$my_post_value->id}},{{$my_post_value->getUser->id}})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18.667" height="16" viewBox="0 0 18.667 16">
                                                                <path id="send" d="M2.009,19l18.658-8L2.009,3,2,9.222,15.333,11,2,12.778Z" transform="translate(-2 -3)" fill="#e85922"/>
                                                            </svg>                                      
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <!-- Post /////-->
                                    </div>
                                    @endforeach
                                @else  
                                        @include('frontend.common.no-data')
                                @endif
                            @else
                            @php $data_arr=['error_name'=>'Private User']; @endphp
                               @include('frontend.common.no-data')
                            @endif