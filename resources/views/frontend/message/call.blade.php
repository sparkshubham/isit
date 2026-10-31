 <!-- Create Chat Room Modal -->

 <div class="modal fade" id="call_modal_popup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="call_modal_popup_lable" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="call_modal_popup_lable">Call</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="#"  autocomplete="false" enctype="multipart/form-data" id="CreateCallRoom-form">
                <div class="create-post-form">
                        <div class="form-floating mb-3">
                           <label for="ChatRoomName"></label>
                        </div>
                        <div class="add-group-profile">
                            <div class="avatar-upload mb-3">
                                <div class="avatar-preview">
                                    <div id="imagePreview_call" style="background-image: url(images/create-group-icon.svg);">
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>            
                <div class="row">
                    <div class="col-6"><button type="button"  data-bs-dismiss="modal" data-type ="false" class="btn btn-secondary call_accept_decliend">Declined</button></div>
                    <div class="col-6"><button type="buttn"  data-type ="true" class="btn btn-primary call_accept_decliend">Accept</button></div>
                </div>
                
                
                </form>
            </div>
        </div>
    </div>
</div>