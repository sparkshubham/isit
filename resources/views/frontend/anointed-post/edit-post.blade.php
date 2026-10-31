<div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CreatePostGeneralLabel">Edit General Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('general-post-save') }}" accept-charset="UTF-8"   id="generalpost-form" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="create-post-form">
                            
                            <input type="hidden" value="1" class="form-control" id="post_type" name="post_type" placeholder="Heading">
                            <input type="hidden" value="0" class="form-control" id="draft_status" name="draft_status" placeholder="Heading">
                                <div class="form-floating mb-3"> 
                                <input type="text" class="form-control" id="heading" name="heading" placeholder="Heading">
                                <label for="heading">Heading</label>
                                @error('heading')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" id="description" name="description" placeholder="Leave a description here" id="floatingTextarea" style="resize:none;" ></textarea>
                                <label for="floatingTextarea">Description</label>
                            </div>
                            <div class="image-video-upload-part">
                                <h4>Photo/Video</h4>
                                <input class="upload__inputfile"  class="" id="file-input" type="hidden" name="file[]" >
                                <input  class="videoExist" type="hidden" value="0" >

                                             
                                <div class="image-video-upload-preview">
                                        <div class="needsclick dropzone" id="document_dropzone">
                                        </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">  
                            </div>
                        </div>            
                        <div class="row">
                        <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                  
                            <!-- <div class="col-6"><button type="button"   data-dismiss="modal" class="btn btn-secondary">Cancel</button></div> -->
                            <div class="col-6"><button id="savebtn" class="btn btn-primary savebtngenral">Post</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>