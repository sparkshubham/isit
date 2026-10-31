

    @php
        $categories = postCategoriesAdd();
        $countries = postCountries();
      $currency_arr=  config('currency.currency')
    @endphp
<div class="modal fade" id="add_modal_store" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add_modalLabel_product" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_modalLabel_product">Create Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('create-product') }}" accept-charset="UTF-8"   id="create-store-form" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                    <div class="create-post-form">
                            <input type="hidden" id="product_id" name="product_id">
                            <ul class="sell-and-buy">
                                <li>
                                    <input type="radio" id="buy" name="product_type" value="1" >
                                    <label for="buy">I want to Buy</label>
                                </li>
                                <li>
                                    <input type="radio"  id="sell"  value="2" name="product_type">
                                    <label for="sell">I want to Sell</label>
                                </li>
                            </ul>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Product Name">
                                <label for="floatingInput">Product Name</label>
                            </div>
                            <div class="form-floating category-arrow mb-3">
                                <select class="form-select category_id" id="product_category" name="category_id" aria-label="Floating label select example">
                                    <option disabled selected>Please select one</option>
                                        @foreach($categories as $cate)
                                            <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                        @endforeach
                                </select>
                                <label for="floatingSelectCat">Category</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea name="description" class="form-control"  id="product_description"  placeholder="Description"  style="resize: none"></textarea>         
                                <label for="floatingInput">Description</label>
                            </div>
                            <div class="form-floating category-arrow mb-3">
                                <select  class="form-select country_id"  id="product_country"   name="country_id" aria-label="Floating label select example">
                                        <option disabled selected>Please select one</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                </select>
                                <label for="floatingSelectCount">Country</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control"  id="product_city"   name="city"  placeholder="City">
                                <label for="floatingInput">City</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control"   id="product_zipcode"   name="zip_code" placeholder="Zipcode">
                                <label for="floatingInput">Zipcode</label>
                            </div>
                            <ul class="sell-and-buy">
                                <li>
                                    <input type="radio" class="price_type" id="for-money" value="2" name="price_type">
                                    <label for="for-money">For Money</label>
                                </li>
                                <li>
                                    <input type="radio" class="price_type" id="for-free" value="1"  name="price_type">
                                    <label for="for-free">For Free</label>
                                </li>
                            </ul>
                            <!-- <div class="form-floating mb-3" id="price_box_store"> 

                                <input type="number" class="form-control"   id="product_price" name="price"  placeholder="Price ($)">
                                <label for="floatingInput">Price ($)</label>
                            </div> -->
                            <div class="contactnumber w-100 mb-3" id="price_box_store">    
                                <div class="form-floating select-arrow" for="currency">
                                    <select class="form-control valid" id="currency" name="currency">
                                       <!-- <option disabled selected>Select</option> -->
                                       @foreach($currency_arr as $currency)
                                            <option value="{{ $currency['cc'] }}-{{ $currency['symbol'] }}">{{ $currency['cc'] }}-{{ $currency['symbol'] }}  </option>
                                        @endforeach
                                    </select>
                                    <label for="currency">Currency</label>
                                </div>
                                <div class="form-floating">
                                    <input type="number" class="form-control"   id="product_price" name="price"  placeholder="Price ($)">
                                    <!-- <input type="number" class="form-control w-100" id="phone_number" name="phone_number" placeholder="Phone Number" value="9166621459"> -->
                                    <label for="product_price">Price</label>
                                </div>
                            </div>
                            <div class="image-video-upload-part">
                                <h4>Photo/Video</h4>
                                <input class="upload__inputfile"  class="" id="product-file-input" type="hidden" name="file" >
                                <input  class="videoExist" type="hidden" value="0" >
                                <div class="image-video-upload-preview" id="dropzone_prdouct">
                                        <div class="needsclick dropzone" id="document_dropzone">
                                        </div>
                                </div>
                                <div class="image-video-upload-preview" >
                                    <ul id="upload_products">
                                    </ul>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">  
                            </div>
                    
                    </div>            
                    <div class="row">
                        <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                        <div class="col-6"><button type="submit" id="product_submit_btn"  class="btn btn-primary">Publish</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>