
<div class="modal fade" id="sharePost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sharePostlLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content">
       <div class="modal-header">
           <h5 class="modal-title" id="sharePostlLabel">Share</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body"> 
           <div class="shareable-link-section">
               <div class="sharelinkicon">
                    <i class="uil uil-link-alt"></i>
               </div>
               <div id="inviteCode" class="invite-page">
                    <!-- <a id="link_share"  href='#' class="share_link_post copy_text" target="_blank" ></a> -->
                    <input id="link"  class="share_link_post" >
                    <div id="copy">
                        <i class="uil uil-copy copy_text" aria-hidden="true"  data-copytarget="#link"></i>
                        <!-- <i class="fa fa-clipboard" aria-hidden="true" data-copytarget="#link"></i> -->
                    </div>
              </div>
              <div class="clicktocopy-shareable-link">
                click to copy shareable link
              </div>
           </div>       
           <!-- <div class="search-users-for-share">
                <input class="search" type="search" placeholder="Search" id="user_search_share_list">
           </div>     -->
           <!-- <div class="following-users scrollbar-outer" id="chat_user_list_share">  
              
           </div>   -->
           <div class="modal-footer">
               <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
                <!-- <button type="button" class="btn btn-primary">Send</button> -->
            </div>
       </div>
   </div>
   </div>
</div>