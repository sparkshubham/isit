 <!-- Create Chat Room Modal -->

 <div class="modal fade" id="CreateChatRoom" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="CreateChatRoomlLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" id="modal-content-chat">
             <div class="modal-header">
                 <h5 class="modal-title" id="CreateChatRoomlLabel">Chat Room</h5>

                 <div class="modal-editdelet-icon d-flex align-items-center">
                     <button type="button" class="btn btn-primary delete hide" id="delete_exit_btn_group">Delete
                         Group</button>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
             </div>
             <div class="modal-body">
                 <form action="#" autocomplete="false" enctype="multipart/form-data" id="CreateChatRoom-form">
                     <div class="create-post-form">
                         <div class="form-floating mb-3">
                             <input maxlength="50" type="text" class="form-control" id="ChatRoomName" name="name"
                                 placeholder="Chat Room Name">
                                 <input type='hidden' id="group_uniqueName" name="group_uniqueName" />
                           
                             <label for="ChatRoomName">Chat Room Name</label>
                         </div>
                         <div class="add-group-profile">
                             <h5>Profile Picture</h5>
                             <div class="avatar-upload mb-3">
                                 <div class="avatar-edit">
                                     <input type='file' id="imageUpload" name="group_pic" accept=".png, .jpg, .jpeg" />
                                     <label for="imageUpload">
                                         <!--<img src="images/pencil.svg" alt="">--></label>
                                 </div>
                                 <div class="avatar-preview">
                                     <div id="imagePreview"
                                         style="background-image: url(images/create-group-icon.svg);">
                                     </div>
                                 </div>
                             </div>
                             <input type='hidden' id="group_Profile_pic" name="group_Profile_pic" />

                             <h3 id="participant_count" class="hide">5 Participants</h3>
                             <div id="hide_particpant_admin">
                                    <h3>Add Friends</h3>
                                    <div class="search-users">
                                        <input class="search-icon" id="user_search_group_chat" type="search"
                                            placeholder="Search">
                                    </div>
                             </div>
                             
                             <select class="hide" id="appendBlankparticpant" name="appendBlankparticpant"
                                 multiple="multiple">
                             </select>
                            
                           
                             <div class="hide" id="groupUserlist_always_hide">
                             </div>
                           
                             <div class="following-users scrollbar-outer group_user_no_data" id="groupUserlist">
                             </div>
                             <div class="createdby-section">
                                 <h3 id="group_created_by_h" class="hide">Created By: <span class="ps-2"
                                         id="group_created_by"></span></h3>
                                 <h3 id="group_created_date_h" class="hide">Created Date: <span class="ps-2"
                                         id="group_created_date"></span></h3>

                             </div>

                             <input type='hidden' id="is_update" name="false" />
                         </div>
                         <!-- <div class="col-6"><button type="submit" class="btn btn-primary hide" id="update_group">Update Group</button></div> -->
                         <br>
                     </div>
                     <div class="row">
                         <div class="col-6"><button type="button" data-bs-dismiss="modal"
                                 class="btn btn-secondary">Cancel</button></div>
                         <div class="col-6" id="group_create_btn"><button type="submit"
                                 class="btn btn-primary">Create</button></div>
                         <div class="col-6 hide" id="update_group"><button type="submit" class="btn btn-primary ">Update
                                 Group</button></div>
                         <div class="col-6"><button type="submit" class="btn btn-primary hide"
                                 id="exit_btn_group">Delete Group</button></div>

                     </div>

                     <div>
                     </div>

                 </form>
             </div>
             <div class="loading hide" id="file_uploader"></div>
         </div>
     </div>
     
 </div>
