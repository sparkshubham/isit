
	
	<div class="container  agora-video-container hide position-relative local-stream-section" id="audience_video">
		
	<div class="row">
					<div class="col-md-9  ">
						<div class="position-relative">
								<div class="agora-video-title">
									<div class="agora-video-username">
										<div class="commenterImage-name">
											<div class="commenterImage">
												<img src="#" id="agora_user_pic"/>
											</div>
											<p class="username"><a href="#" id="agora_username_broadacaster"></a></p> 
										</div>
										<p class="agora-video-text-title"></p>
									</div>
									<div class="agora-video-participant"><span>
										<svg xmlns="http://www.w3.org/2000/svg" width="18" height="13.5" viewBox="0 0 18 13.5">
										<g id="Icon_feather-eye" data-name="Icon feather-eye" transform="translate(-0.083 -2.583)">
											<path id="Path_12071" data-name="Path 12071" d="M.833,9.333s3-6,8.25-6,8.25,6,8.25,6-3,6-8.25,6-8.25-6-8.25-6Z" transform="translate(0)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
											<path id="Path_12072" data-name="Path 12072" d="M12,9.75A2.25,2.25,0,1,1,9.75,7.5,2.25,2.25,0,0,1,12,9.75Z" transform="translate(-0.667 -0.417)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
										</g>
										</svg></span> <span id="live_user_count_particpant">0</span></div>
									
								</div>

						
							<div id="full-screen-video"></div>

							
							<div id="buttons-container" class="w-100 callControlls d-flex justify-content-center ms-1">
								<div class="text-center">
									<button id="exit-btn" type="button" class="call-close-btn btn btn-block btn-danger btn-sm">
										<i id="exit-icon" class="fas fa-phone-alt"></i>
									</button>
								</div>
							</div>
							<div id="no-local-video" class="col text-center nolonger-brodcaster">
							</div>
							<div id="watch-live-overlay">
								<div id="overlay-container">
								</div>
							</div>
						</div>
		            </div>
					<div class="col-md-3 agora-video-right">
								<div class="agora-video-comment">
									<ul class="commentList scrollbar-outer" id="commentongolive"  >
													
									</ul>
										<form  action="#" class="comment-form hide" role="form" id="comment_form_audience" onsubmit="commentOnpost('{{$post_id}}','{{Auth::user()->id}}')">
                                            <div class="commenterinput">
                                            <textarea  form="comment_form_audience"  maxlength="1500" onkeyup="enableBtncmt(this.value)"  class="form-control w-100  textarea_comment" type="text" id="comment_value_{{$post_id}}" placeholder="Type your comment here" ></textarea>
                                                <button type="button" class="comments_val_class" onclick="commentOnpost('{{$post_id}}','{{Auth::user()->id}}')">
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
	
<script>
	
	$(document).ready(function(){
		
	    $('.comment-form').removeClass('hide')
		$('.comment-form').on('submit', function(e){
			e.preventDefault();
			
		e.stopPropagation();
		});
	});
	
$('textarea').keyup(function (event) {
        if (event.keyCode == 13 && event.shiftKey) {
            var content = this.value;
            var caret = getCaret(this);
            this.value = content.substring(0,caret)+"\n"+content.substring(carent,content.length-1);
            event.stopPropagation();
            
      }else if(event.keyCode == 13)
      {
         $('.comment-form').submit();
      }
  });

	
</script>
	