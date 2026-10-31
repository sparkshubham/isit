
<!-- Create Event Modal -->

<div class="modal fade" id="CreateEvent" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="CreateEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="bottom:45px!important">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="CreateEventLabel">Create Event</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <form method="POST" action="{{ route('create-event') }}" accept-charset="UTF-8"   id="create-event-form" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="create-post-form" id="edit_event_data">
                            <input type="hidden" class="form-control" id="event_hidden_id"  name="event_hidden_id"   placeholder="Event Name">
                            <input type="hidden" class="form-control" id="timeoffset"  name="timeoffset"   placeholder="Event Name">
                            <input type="hidden" class="form-control" id="timezone"  name="timezone"   placeholder="Event Name">
                            <input type="hidden" class="form-control" id="gmt_time"  name="gmt_time" >

                            <div class="add-group-profile">
                             <h5>Event picture.</h5>
                             <div class="avatar-upload mb-3">
                                 <div class="avatar-edit">
                                     <input type='file' id="event_image_upload"  name="event_picture" accept=".png, .jpg, .jpeg" />
                                     <label for="event_image_upload">
                                         <!--<img src="images/pencil.svg" alt="">--></label>
                                 </div>
                                 <div class="avatar-preview">
                                     <div id="imagePreview" class="event_picture"
                                         style="background-image: url(images/create-group-icon.svg);">
                                     </div>
                                 </div>
                             </div>
                         </div>

                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="event_name"  name="name"   placeholder="Event Name">
                                <label for="event_name">Event Name</label>
                            </div>

                            
                            <div class="form-floating mb-3">
                                
                                <input type="date" class="form-control calendar-icon" name="date" id="date_event" placeholder="Date">
                                <label for="date_event">Date</label>
                            </div>
                            <div class="form-floating mb-3"> 
                                <input type="text" class="form-control clock-icon" name="start_time" id="start_time_event" placeholder = "Start Time">
                                <label for="start_time">Start Time</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control clock-icon" name="end_time"  id="end_time_event" placeholder = "End Time">
                                <label for="end_time">End Time</label>
                            </div>
                          
                            
                    </div>            
                    <div class="row">
                        <div class="col-6"><button  type="button" id="event_submit" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                        <div class="col-6"><button id="create_event_btn" type="submit" class="btn btn-primary">Create</button></div>
                    </div>
            </form>
        </div>
    </div>
    </div>
</div>


