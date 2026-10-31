
<div class="modal fade" id="golivemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="agoraTitleLable" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content">
       <div class="modal-header golivemodal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           <div class="golivemodal-modal-img"><img src="{{ asset('front/images/reply-commetn-logo-lg.svg') }} "></div>
           <div class="golivemodal-modal-img"><h5 class="modal-title text-center" id="agoraTitleLable">Enter title for Go live</h5></div>
           
       </div>
       <div class="modal-body">    
            <form  action="#" id="agora-live-form" autocomplete="false">
                        @csrf
                    
                        <div class="create-post-form">
                            <div class="form-floating mb-3">
                                <input name="go_live_title" type="text" class="form-control" id="go_live_title" placeholder="Title">
                                <label for="floatingInput">Title</label>
                            </div>      
                            <label class="form-check-label font-12">Choose broadcasting method:</label>
                            <div class="form-floating mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="broadcast_method" id="camera" checked>
                                        <label class="form-check-label font-12" for="camera">
                                            Camera
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" name="broadcast_method" id="obs">
                                        <label class="form-check-label font-12" for="obs">
                                            OBS (Open Broadcaster Software)
                                        </label>
                                    </div>
                            </div>    
                            <div class="form-floating mb-3 ">  
                                   <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="0"  name="is_recorded" id="is_recorded">
                                        <label class="form-check-label font-12" for="flexCheckDefault">
                                            Are you want to sure to record this go live.
                                        </label>
                                    </div>
                            </div>     
                            
                                  
                        </div>            
                        <div class="row">
                        <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                  
                            <div class="col-6"><button type="submit" class="btn btn-primary" id="go_live_btn">Start</button></div>
                        </div>
                        
                </form>
       </div>
   </div>
   </div>
</div>