
@extends('frontend.layouts.app')
@section('content')


   <section class="main-wrapper">
        <div class="container">                
            <div class="container-card">
                <!--- \\\\\\\Post-->
                <div class="single-card ad-detail card shadow-sm">
                    <div class="card-body p-0"> 
                        <div class="image-wrap">
                            <div class="owl-carousel ad-slider owl-theme">     
                                 @if(!empty($products->getMedia))
                                        @foreach($products->getMedia as $media)
                                            @if($media->media_type==2)
                                            <div class="item-video active">
                                                    <!-- Tag User List Start -->
                                                    <!-- Tag User List End -->
                                                    <video class="video-js" controls disablepictureinpicture controlslist="nodownload noplaybackrate">
                                                    <source src="{{$path['product_media'].$media->file_name}}" type="video/mp4">
                                                    <source src="{{$path['product_media'].$media->file_name}}" type="video/ogg">
                                                    Your browser does not support the video tag.
                                                    </video>
                                                    <script src="//players.brightcove.net/1752604059001/217b1cd8-54d5-4ac7-b213-268a81d5081f_default/index.min.js"></script>
                                            </div>
                                           @else
                                            <div class="item"  data-merge={{$media}}>
                                                <img  src="{{$path['product_media'].$media->file_name}}" >
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif     
                           </div> 
                        </div>
                        
                        <div class="card-text">
                            <h4 class="ad-title">{{$products->name}}</h4>
                            <span class="Add-category">{{ getCategoryNameById($products->category_id )}}</span>
                            <p>{{$products->description}}</p>
                            
                            <ul class="list-address">
                                <li>
                                    <a href="#"><i class="uil uil-map-marker"></i>{{getCountryNameById($products->country_id )}}, {{ isset($products->zip_code) ?$products->zip_code: ''}}</a>
                                </li>
                            </ul>
                            
                            <div class="ad-price">                                
                                <div class="form-floating mb-3">
                                    @php $final_price =$products->currency.' '.$products->price @endphp
                                    <input type="text" class="form-control" disabled value=" {{($products->price_type==1)?'Free':$final_price;}}" id="priceindollar" placeholder="Price">
                                    <label for="priceindollar">Price</label>
                                </div>
                            </div>
                            @if(Auth::user()->id!=$products->getUser->id)
                            
                            @php 
                                $userInfo=getProfileData($products->getUser->id);
                            @endphp
                            @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                            @if($products->getUser->profile_picture)
                                @php  $profile_url=$path['user_media'].$products->getUser->profile_picture; @endphp
                            @endif
                            <div class=" message_hide message-seller"
                            >
                                <button   class="btn btn-primary w-auto px-4 tc-users"
                                    data-user-twillio-id="{{ $userInfo['twilio_s_id']}}"   
                                    data-tc-fullname="{{ $products->getUser->first_name}} {{  $products->getUser->last_name}}"   
                                    data-tc-name="{{$products->getUser->first_name}} {{  $products->getUser->last_name}}" 
                                    data-tc-profile="{{$profile_url}}"
                                    data-tc-user-id="{{$products->getUser->id}}" type="button" >Message Seller</button>
                            </div>
                            @endif
                        </div>   
                    </div>
                <!-- Post /////-->
                </div>
        </div>
    </section>
@endsection
