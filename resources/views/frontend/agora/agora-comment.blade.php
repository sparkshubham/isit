
     
            @if(!empty($comments))
                   @foreach($comments as $commentsValue) 
                   <li>
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
                                            <p class="username"><a href="#">{{$commentsValue ['getUser']['first_name'].' '.$commentsValue ['getUser']['last_name']}}</a>
                                            <span class="postdate">{{time_elapsed_string(convertIntTodate($commentsValue['createdAt']))}}</span></p> 
                                            @endif
                                        </div>
                                        <div class="commentText">
                                            <p>{{$commentsValue['comment']}}</p>
                                            
                                                    
                                        </div>
                                </div>
                        </li>
                        @endforeach
                    @else
                
                        @php $data_arr=['error_name'=>'No Comments found']; @endphp
                        @include('frontend.common.no-data',$data_arr)
                    @endif   
                  