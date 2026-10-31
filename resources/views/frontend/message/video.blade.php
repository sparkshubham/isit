<div class="hide" id="videoCallMessage">

                <div class="container position-relative">
                   
                    <div id="remote-media-div{{Auth::user()->id}}" class="row  video-cols-section justify-content-center">
                  
                    </div>
                </div>

                <div class="local_media_wth_btn video-call-small-screen ">
                     <div id="user_name_caller"></div>
                    <div id="local-media" class="timer">
                    </div>
                    <div id="buttons-container" class="w-100 callControlls d-flex justify-content-center ms-1">
                                <!-- <div id="audio-controls" class="text-center btn-group">
                                        <button id="remote-speaker-btn" type="button" class="btn btn-block btn-dark ">
                                                <i id="remote-speaker-icon" class="fa fa-volume-up"></i>
                                        </button>      
                                </div> -->


                                <div id="audio-controls" class="text-center btn-group">
                                        <button id="remote-mic-btn" type="button" class="btn btn-block btn-dark ">
                                                <i id="remote-mic-icon" class="fas fa-microphone"></i>
                                        </button>     
                                </div>
                                <div id="video-controls" class="text-center btn-group video_Call_btn hide">
                                        <button id="remote-video-btn"  type="button" class="btn btn-block btn-dark">
                                            <i id="remote-video-icon" class="fas fa-video"></i>
                                        </button>
                                </div>
                                

                                <div class="text-center">
                                        <button id="remote-exit-btn"  type="button" class=" call-close-btn btn btn-block btn-danger ">
                                                <i id="remote-exit-icon" class="fas fa-phone-alt"></i>
                                        </button>
                                </div>

                        </div>

                        <div id="no-local-video" class="col text-center nolonger-video-call">                        
                        </div>
                        <div id="no-local-video2" class="col text-center nolonger-video-call" style="display:none;">                        
                        </div>
                    </div>
                    

            </div>