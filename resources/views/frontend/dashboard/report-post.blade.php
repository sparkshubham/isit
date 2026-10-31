
<div class="modal fade" id="reportPost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reportPostLable" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
   <div class="modal-content">
       <div class="modal-header">
           <h5 class="modal-title" id="reportPostLable">Report Post</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div> 
        @php
        $categories = postCategoriesReport();
        @endphp    
       
       <div class="modal-body"> 
              <form  id="report-post-form"  method="POST"  action="{{ route('report-post-user') }}"autocomplete="false">
              @csrf            
                    <div class="form-floating category-arrow mb-3">
                            <input type="hidden" name="report_post_id" id="report_post_id"></input>
                            <select  class="form-select category" id="report_category"  name="category" aria-label="Floating label select example">
                                            <option disabled selected>Please select one</option>
                                                @foreach($categories as $cate)
                                                    <option value="{{ $cate->name }}" data-name="{{ $cate->name }}">{{ $cate->name }}</option>
                                                @endforeach
                                        </select>
                            <label for="floatingSelect">Category</label>
                    </div>                            
                    <div class="form-floating hide" id="report_description_hide">
                        <textarea style="resize: none" class="form-control"  name="description" placeholder="Leave a description here" id="help_description_report"></textarea>
                        <label for="help_description">Description</label>
                    </div>
                    <div class="row mt-4">
                        <div class="col-6"><button type="button"  data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button></div>
                        <div class="col-6"><button type="submit" class="btn btn-primary">Submit</button></div>
                    </div>
               </form>
       
       </div>
   </div>
   </div>
</div>