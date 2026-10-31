		

<div class="container agora-video-container hide   position-relative"  id="broadcast_video">
	<div class="row">
			<div class="col-md-9  ">
				<div class="position-relative">
					<div class="agora-video-title">
						<div class="agora-video-username">
							<!-- <div class="commenterImage-name">
								<div class="commenterImage">
									<img src="http://placekitten.com/50/50" />
								</div>
								<p class="username"><a href="#" id="agora_username_broadacaster"></a></p> 
							</div> -->
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
				<div id="stats-container" class="col-2 float-left text-left mt-2 stats-btn-container">
						<button id="stream-stats-btn" type="button" class="btn btn-lg stream-stat-btn" data-toggle="popover" data-placement="bottom" data-html="true" title="Stream stats" data-content="loading stats...">
							<i id="connection-quality-icon" class="fas fa-signal" ></i>
						</button>
						<button id="downlink-quality-btn" type="button" class="btn btn-lg stream-stat-btn" data-toggle="popover" data-placement="bottom" data-html="true" title="Downlink Quality" data-content="loading stats...">
							<i id="downlink-quality-icon" title="Downlink Quality" class="fas fa-download" ></i>
						</button>
						<button id="uplink-quality-btn" type="button" class="btn btn-lg stream-stat-btn" data-toggle="popover" data-placement="bottom" data-html="true" title="Uplink Quality" data-content="loading stats...">
							<i id="uplink-quality-icon" title="Uplink Quality" class="fas fa-upload" ></i>
						</button>
						<button id="network-stats-btn" type="button" class="btn btn-lg stream-stat-btn" data-toggle="popover" data-placement="bottom" data-html="true" title="Network stats" data-content="loading stats...">
							<i id="network-stats-icon" class="fas fa-network-wired" ></i>
						</button>
						<button id="session-stats-btn" type="button" class="btn btn-lg stream-stat-btn" data-toggle="popover" data-placement="bottom" data-html="true" title="Session stats" data-content="loading stats...">
							<i id="session-stats-icon" class="fas fa-info-circle" ></i>
						</button>
					</div>
					<div id="main-video-stats-btn-container" class="col-2 float-right text-right mt-2 stats-btn-container">
						<button id="main-stream-stats-btn" type="button" class="btn btn-lg p-0" data-toggle="popover" data-placement="left" data-html="true" title="Video stats" data-content="loading stats...">
							<i id="main-stream-stats-icon" class="fas fa-signal" style="color:#fff" ></i>
						</button>
						<button id="main-video-stats-btn" type="button" class="btn btn-lg p-0" data-toggle="popover" data-placement="bottom" data-html="true" title="Video stats" data-content="loading stats...">
							<i id="main-video-stats-icon" class="fas fa-file-video" style="color:#fff" ></i>
						</button>
						<button id="main-audio-stats-btn" type="button" class="btn btn-lg p-0" data-toggle="popover" data-placement="bottom" data-html="true" title="Audio stats" data-content="loading stats...">
							<i id="main-audio-stats-icon" class="fas fa-file-audio" style="color:#fff" ></i>
						</button>
					</div>
					<div id="remote-control-buttons-container" class="row justify-content-center mt-3">
						<div class="col-md-2 text-center">
							<button id="remote-mic-btn" type="button" class="btn btn-block btn-dark btn-lg">
								<i id="remote-mic-icon" class="fas fa-microphone"></i>
							</button>
						</div>
						<div class="col-md-2 text-center">
							<button id="remote-exit-btn"  type="button" class="btn btn-block btn-danger btn-lg">
								<i id="remote-exit-icon" class="fas fa-phone-slash"></i>
							</button>
						</div>
					</div>
				
					<div id="full-screen-video"></div>


					<div id="lower-video-bar" class="row mb-1">

					<!--comment by shubham-->
						<!-- <div id="remote-streams-container" class="container col-9 ml-1">
							<div id="remote-streams" class="row"> -->
								<!-- insert remote streams dynamically -->
							<!-- </div>
						</div> -->
						<!--comment by shubham-->
						<div id="local-stream-container" class="local-stream-section col p-0">
							<div id="buttons-container" class="w-100 callControlls d-flex justify-content-center ms-1">
								<div id="audio-controls" class="text-center btn-group">
									<button id="mic-btn" type="button" class="mic-btn btn btn-block btn-dark btn-sm">
										<i id="mic-icon" class="fas fa-microphone"></i>
										<!-- <svg id="mic-icon"  height="18px" width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M192 352c53.03 0 96-42.97 96-96v-160c0-53.03-42.97-96-96-96s-96 42.97-96 96v160C96 309 138.1 352 192 352zM344 192C330.7 192 320 202.7 320 215.1V256c0 73.33-61.97 132.4-136.3 127.7c-66.08-4.169-119.7-66.59-119.7-132.8L64 215.1C64 202.7 53.25 192 40 192S16 202.7 16 215.1v32.15c0 89.66 63.97 169.6 152 181.7V464H128c-18.19 0-32.84 15.18-31.96 33.57C96.43 505.8 103.8 512 112 512h160c8.222 0 15.57-6.216 15.96-14.43C288.8 479.2 274.2 464 256 464h-40v-33.77C301.7 418.5 368 344.9 368 256V215.1C368 202.7 357.3 192 344 192z"/></svg> -->
									</button>
									<!-- <button id="mic-dropdown" type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span class="sr-only">Toggle Dropdown</span>
									</button> -->
									<div id="mic-list" class="dropdown-menu dropdown-menu-right">
									</div>
								</div>
								<div id="video-controls" class="text-center btn-group">
									<button id="video-btn"  type="button" class="video-btn btn btn-block btn-dark btn-sm">
										<i id="video-icon" class="fas fa-video"></i>
										<!-- <svg  id="video-icon"  xmlns="http://www.w3.org/2000/svg" height="18px" width="18px" viewBox="0 0 576 512"><path d="M384 112v288c0 26.51-21.49 48-48 48h-288c-26.51 0-48-21.49-48-48v-288c0-26.51 21.49-48 48-48h288C362.5 64 384 85.49 384 112zM576 127.5v256.9c0 25.5-29.17 40.39-50.39 25.79L416 334.7V177.3l109.6-75.56C546.9 87.13 576 102.1 576 127.5z"/></svg> -->
									</button>
									<!-- <button id="cam-dropdown" type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span class="sr-only">Toggle Dropdown</span>
									</button> -->
									<div id="camera-list" class="dropdown-menu dropdown-menu-right">
									</div>
								</div>
								<div class="text-center">
									<button id="exit-btn"  type="button" class="call-close-btn btn btn-block btn-danger btn-sm">
										<i id="exit-icon" class="fas fa-phone-alt"></i>
										<!-- <svg d="exit-icon" class="fa-microphone fa-microphone-slash"  xmlns="http://www.w3.org/2000/svg" height="18px" width="18px" viewBox="0 0 512 512">
											<path d="M511.2 387l-23.25 100.8c-3.266 14.25-15.79 24.22-30.46 24.22C205.2 512 0 306.8 0 54.5c0-14.66 9.969-27.2 24.22-30.45l100.8-23.25C139.7-2.602 154.7 5.018 160.8 18.92l46.52 108.5c5.438 12.78 1.77 27.67-8.98 36.45L144.5 207.1c33.98 69.22 90.26 125.5 159.5 159.5l44.08-53.8c8.688-10.78 23.69-14.51 36.47-8.975l108.5 46.51C506.1 357.2 514.6 372.4 511.2 387z"/>
										</svg> -->
									</button>
								</div>
							</div>
							<div id="local-stats-container" class="col-2 float-left text-left p-0 m-0 stats-btn-container">
								<button id="audio-stats-btn" type="button" class="btn btn-lg"data-toggle="popover" data-placement="top" data-html="true" title="Audio stats" data-content="loading stats...">
									<i id="audio-stats-icon" style="color:#fff" class="fas fa-file-audio" ></i>
								</button>
								<button id="video-stats-btn" type="button" class="btn btn-lg" data-toggle="popover" data-placement="left" data-html="true" title="Video stats" data-content="loading stats...">
									<i id="video-stats-icon" style="color:#fff" class="fas fa-file-video" ></i>
								</button>
							</div>
							<!-- <div id="mute-overlay" class="col">
								<i id="mic-icon" class="fas fa-microphone-slash"></i>
							</div> -->
							<div id="no-local-video" class="col text-center">
							<!-- Broadcaster has turned off the video. -->
								<!-- <i id="user-icon" class="fas fa-user"></i> -->
								<i  id="user-icon" class="fas fa-video-slash"></i>
							</div>
							<div id="local-video" class="col p-0"></div>
							<div id="screen-share-btn-container" class="col-3 float-right text-right mr-2 hide">
								<button id="screen-share-btn" type="button" class="btn btn-lg">
									<i id="screen-share-icon" class="fas fa-desktop"></i>
								</button>
								<button id="toggle-stats-btn" type="button" class="btn btn-lg">
									<i id="toggle-stats-icon" style="color:rgb(33, 37, 41)" class="fas fa-file-medical-alt"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 agora-video-right" id="">
						<div class="agora-video-comment">
							<ul class="commentList scrollbar-outer"  id="commentongolive">
											
							</ul>
							<form action="#"  autocomplete="off" class="comment-form hide" role="form" id="comment_form_brodacast" onsubmit="commentOnpost('{{$post_id}}','{{Auth::user()->id}}')">
                                            <div class="commenterinput">   
                                            <textarea  form="comment_form_brodacast"  maxlength="1500"  onkeyup="enableBtncmt(this.value)" maxlength="1500" class="form-control w-100  textarea_comment" type="text" id="comment_value_{{$post_id}}" placeholder="Type your comment here"  ></textarea>
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
            // this.submit();
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
});
</script>