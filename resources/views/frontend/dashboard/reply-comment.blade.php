               @if(!empty($comments))
                    @foreach($comments as $commentsValue) 

                    
                        <li>
                            <div class="single-comment">
                                <div class="commenterImage-name">
                                    <div class="commenterImage">
                                        @if(isset($commentsValue['get_user']['profile_picture']) && !empty($commentsValue['get_user']['profile_picture']))
                                            <img src="{{$path.$commentsValue['get_user']['profile_picture']}}" />
                                        @else
                                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                        <img src="{{$profile_url}}" alt="Lock">
                                        @endif
                                    </div>
                                    <p class="username">
                                        <a href="{{route('user-profile', ['user_id' => $commentsValue['get_user']['id']])}}">{{$commentsValue['get_user']['first_name']}} {{$commentsValue['get_user']['last_name']}}</a>
                                        <span class="postdate">{{time_elapsed_string(convertIntTodate($commentsValue['createdAt']))}}</span>
                                    </p> 
                                </div>
                                <div class="commentText">
                                    <p>{{$commentsValue['comment']}}</p>      
                                </div>
                                           
                                <div class="like_dislike_comment">
                                 @php $total_likes=$commentsValue['total_likes'].' Likes';@endphp
                                                            @if($commentsValue['total_likes']==1)
                                                            @php  $total_likes= $commentsValue['total_likes'].' Like';@endphp
                                                            @endif
                                <a  onclick="commentlikelistmodal({{$commentsValue['comment_id']}})" ><span id="total_like_comment_{{$commentsValue['comment_id']}}">{{$total_likes}}</span></a>
                                    
                                <a class="{{ $commentsValue['is_liked']==1 ?'liked_icon':'dislike_icon'}}" id="like_unlike_css{{$commentsValue['comment_id']}}" onclick="likeUnlikeComment({{$commentsValue['comment_id']}})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17.003" height="17" viewBox="0 0 17.003 17">
                                                <path id="Icon_feather-thumbs-up" data-name="Icon feather-thumbs-up" d="M11.421,7.267v-3.2a2.42,2.42,0,0,0-2.439-2.4l-3.252,7.2v8.8H14.9a1.618,1.618,0,0,0,1.626-1.36l1.122-7.2a1.583,1.583,0,0,0-.381-1.29,1.637,1.637,0,0,0-1.245-.55Zm-5.69,10.4H3.292a1.613,1.613,0,0,1-1.626-1.6v-5.6a1.613,1.613,0,0,1,1.626-1.6H5.731" transform="translate(-1.167 -1.167)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                                            </svg>
                                    @if($commentsValue['is_liked']==1)
                                        <input type="hidden" id="liked_value_{{$commentsValue['comment_id']}}" value="1">
                                        <span id="like_unlike_{{$commentsValue['comment_id']}}"> Liked</span>
                                    @else
                                        <input type="hidden" id="liked_value_{{$commentsValue['comment_id']}}" value="0">
                                        <span id="like_unlike_{{$commentsValue['comment_id']}}"> Like</span>
                                    @endif
                                    </a>
                                 
                                                            
                                        
                                    <input type="hidden" value="{{$commentsValue['total_likes']}}" id="data-total-comment-like_{{$commentsValue['comment_id']}}">
                
                            </div>
                            </div>   
                        </li>
                    @endforeach
                @else
                <div class="no_data_found">
                    <img src="{{ asset('front/images/nodata.svg') }}" alt="">
                    <p>No replies found</p>

                </div>
                @endif 
               