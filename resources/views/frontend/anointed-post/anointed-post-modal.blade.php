   <!-- Create Anointed Post Modal -->
    @php
        $categories = postCategories();
        $countries = postCountries();
    @endphp
    <div class="modal fade" id="CreateAnointedPost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="CreateAnointedPostlLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CreateAnointedPostlLabel">Create Anointed Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('anointed-post-save') }}" id="anointedPost-form" enctype="multipart/form-data">
                        @csrf
                        <div class="create-post-form">
                            <input type="hidden" name="post_type" value="2">
                            <input type="hidden" class="form-control" id="hidden_id_anointed" name="hidden_id" placeholder="Heading">

                            <input type="hidden" name="draft_status_anointed" value="0" id="draft_status_anointed">
                            <div class="form-floating  category-arrow mb-3">
                                <select name="category" class="form-select category" id="category" aria-label="Floating label select example">
                                    <option disabled selected>Please select one</option>
                                    @foreach($categories as $cate)
                                        <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <label for="floatingSelect">Category</label>
                            </div>
                            <div class="form-floating category-arrow mb-3">
                                <select name="country" class="form-select country" id="country" aria-label="Floating label select example">
                                <option disabled selected>Please select one</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <label for="floatingSelect">Country</label>
                                @error('country')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input name="city_name" type="text" class="form-control @error('city_name') is-invalid @enderror" id="city_name" placeholder="City">
                                <label for="floatingInput">City</label>
                                @error('city_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input name="zip_code" type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" placeholder="Zipcode">
                                <label for="floatingInput">Zipcode</label>
                                @error('zip_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating">
                                <textarea name="description" class="form-control" placeholder="Leave a anointed Details here" id="anointedDetails" style="resize: none"></textarea>
                                <label for="anointedDetails">Anointed Details</label>
                            </div>
                            <!-- <div class="image-video-upload-part">
                                <h4>Photo/Video</h4>
                                <input class="upload__inputfile"  class="" id="anointed-file-input" type="hidden" name="file[]" >
                                <input  class="videoExist" type="hidden" value="0" >
                                <div class="image-video-upload-preview">
                                        <div class="needsclick dropzone" id="document_dropzone">
                                        </div>
                                </div>
                            </div> -->
                            <div class="image-video-upload-part">
                                <h4>Photo/Video</h4>
                                <input class="upload__inputfile"  class="" id="anointed-file-input" type="hidden" name="file[]" >
                                <input  class="videoExist" type="hidden" value="0" >

                                             
                                <div class="image-video-upload-preview" id="dropzone_post">
                                        <div class="needsclick dropzone" id="document_dropzone">
                                        </div>
                                </div>
                                
                                <div class="image-video-upload-preview" >
                                    <ul class="upload_post">
                                    </ul>
                                </div>
                            </div>                    
                        </div>            
                        <div class="row">
                        <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                  
                            <div class="col-6"><button class="btn btn-primary savebtnanointed" id="savebtn">Post</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  
