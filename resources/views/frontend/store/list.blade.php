@if(count($products)>0)
                @foreach($products as $product)
                    @php
                        $postFolderName = config('constants.product_media.upload_path');
                        $path['product_media'] = getS3Url($postFolderName);
                    @endphp
                <div class="col-lg-4 col-sm-6 mb-3 store-count-product" id="hide_show_product_{{$product->id}}">
                    <div class="card single-store h-100">
                        <div class="store-img image-wrap">
                        @if($product->user_id==Auth::user()->id )
                            <div class="stre-status sold cursor-pointer" onclick="deleteProduct({{$product->id}})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>          
                            </div>
                            <div  class="stre-status active cursor-pointer" onclick="editProduct({{$product->id}})" >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>       
                            </div>
                            @endif
                            <a href="{{ route('product-view',['product_id'=>$product->id]) }}"> 
                                <div class="owl-carousel owl-theme"> 
                                    @php
                                        $media= $product->getMedia;
                                        $file_name='';                                         
                                    @endphp      
                                    @if(isset($media[0]))
                                           @php $file_name=$path['product_media'].$media[0]->file_name;@endphp
                                        @else
                                           @php $file_name =url('/front/images/placeholder-store.png');@endphp
                                        @endif
                                        @php $file_type=getFileTypeUsingName($file_name);@endphp
                                        @if($file_type==2)
                                               @php  $file_name=$path['product_media'].$media[0]->thumb_image_name;@endphp
                                        @endif

                                     <div class="item" data-merge="{{$product->id}}">
                                            <img src="{{$file_name}}" alt="#">
                                    </div>
                                   
                                </div>      
                            </a>
                        </div>
                      
                        <div class="store-content">
                            <div class="store-price">{{($product->price_type==1)?'Free':$product->currency.' '.$product->price;}}</div>
                               <a href="#">{{$product->name}}</a>
                            <ul>
                                <li><i class="uil uil-map-marker"></i> {{getCountryNameById($product->country_id )}}</li>
                            </ul>
                        </div>
                     
                    </div>
                </div>
                @endforeach
    
                @endif