<li>
    
@if(!empty($comments))
    @foreach($comments as $commentsValue) 
        <div class="single-comment">
            <div class="commenterImage-name">
                @if($commentsValue['getUser'])
                <div class="commenterImage">
                               @if(isset($commentsValue['getUser']['profile_picture']) && !empty($commentsValue['getUser']['profile_picture']))
                                    <img src="{{$path.$commentsValue['getUser']['profile_picture']}}" />
                                @else
                                @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                                                <img src="{{$profile_url}}" alt="Lock">
                                @endif
                  
                </div>
                <p class="username"><a href="{{route('user-profile', ['user_id' =>$commentsValue['getUser']['id']])}}">{{$commentsValue ['getUser']['first_name'].' '.$commentsValue ['getUser']['last_name']}}</a>
                <span class="postdate">{{time_elapsed_string(convertIntTodate($commentsValue['createdAt']))}}</span></p> 
                @endif
            </div>
            <div class="commentText">
                <p>{{$commentsValue['comment']}}</p>
                @php $post_id =getPostIdComment($commentsValue['comment_id']);@endphp
                    <a class="cursor-pointer" onclick="getReplyCommentList({{$commentsValue['comment_id']}},{{$post_id}})">{{$commentsValue['total_replied']==0?'':$commentsValue['total_replied']}} Reply</a>  
                    
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
        </div>


    <div class="comments-reply-modal modal fade" id="CommentReply{{$commentsValue['comment_id']}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="CommentReplylLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CommentReplylLabel">Replies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ps-2 pe-2">
                <div class="comment-reply-section scrollbar-outer">
                    <ul class="commentList subComment"  id="comment_list_reply{{$commentsValue['comment_id']}}">

                    
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <form class="comment-form" role="form" id="comment_form"  onsubmit="return false;">
                    <div class="commenterinput">
                      
                        <textarea maxlength="1500" auth-id="{{Auth::user()->id}}" parent-coment-id="{{$commentsValue['comment_id']}}" post-id="{{$post_id}}" class="form-control w-100 textarea_comment"  type="text"id="comment_value_reply_{{$commentsValue['comment_id']}}" placeholder="Type your reply here" onkeyup="enterKeyPressed(event)"></textarea>
                        <button  class="comments_val_class"  type="submit" onclick="return replycommentOnpost({{$post_id}},{{$commentsValue['comment_id']}},{{Auth::user()->id}})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18.667" height="16" viewBox="0 0 18.667 16">
                                <path id="send" d="M2.009,19l18.658-8L2.009,3,2,9.222,15.333,11,2,12.778Z" transform="translate(-2 -3)" fill="#e85922"/>
                            </svg>                                      
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
        </div>
    </div>

     
                

    @endforeach
    @endif
    
</li>