 <!-- Create Chat Room Modal -->

 <div class="modal fade" id="EditChatRoom" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="EditChatRoomlLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditChatRoomlLabel">Group Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="#"  autocomplete="false" enctype="multipart/form-data" id="EditChatRoom-form">
                <div class="create-post-form">
                
                  
                  
                        <div class="form-floating mb-3">
                            <input  maxlength ="50" type="text" class="form-control" id="ChatRoomName_edit" name="name" placeholder="Chat Room Name">
                            <label for="ChatRoomName">Chat Room Name</label>
                        </div>
                        <div class="add-group-profile">
                            <h5>Profile Picture</h5>
                            <div class="avatar-upload mb-3">
                                <div class="avatar-edit">
                                    <input type='file' id="imageUpload_edit" name="group_pic" accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload_edit"><!--<img src="images/pencil.svg" alt="">--></label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview" style="background-image: url(images/create-group-icon.svg);">
                                    </div>
                                </div>
                            </div>

                            <h3 id="participant_count">5 Participants</h3>
                            <div class="search-users">
                                    <input class="search-icon"  id="user_search_group_chat_edit "type="search" placeholder="Search">
                            </div>

                            
                            <input type='hidden' id="selected_user_edit" name="users_hidden" />
                            <div class="groupcreated-participents following-users" id="group_particpants">
                              

                            </div>
                            <br>
                            <h3>Created By: <span class="ps-2" id="group_created_by"></span></h3> 
                            <h3>Created Date: <span class="ps-2" id="group_created_date"></span></h3>

                            <div class="col-6">
                                <button type="submit" class="btn btn-primary hide" id="update_group">Update Group</button></div>


                                    </div>
                            </div>     
                                
                            <div class="row">
                                <div class="col-6"><button type="button"  data-bs-dismiss="modal"class="btn btn-secondary" >Cancel</button></div>
                                <div class="col-6"><button type="submit" class="btn btn-primary" id="delete_exit_btn_group">Delete Group</button></div>

                            </div>
                
                
                </form>
            </div>
        </div>
    </div>
</div>