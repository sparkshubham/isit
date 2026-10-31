@extends('frontend.layouts.app')
@section('content')  
   	<section class="main-wrapper">
        <div class="container">
            <div class="gallery-images">
              @foreach($posts as $posts_val)
                 @if(!empty($posts_val->getMedia))
                    @foreach($posts_val->getMedia as $media)
                        @if($media->media_type==2)
                        <a href="#" class="single-image">
                          <!-- <video controls>
                            <source src="{{$path['post_media'].$media->file_name}}" type="video/*">
                            <span class="video-icon"><img src="{{ asset('front/images/play-icon.svg') }}" alt=""></span>
                          </video> -->
                            <img src="{{$path['post_media'].$media->thumb_image_name}}" alt=''>
                            <span class="video-icon"><img src="{{ asset('front/images/play-icon.svg') }}" alt=""></span>
                            
                        </a>
                        @else
                            <a href="" class="single-image">
                               <img src="{{$path['post_media'].$media->file_name}}" alt=''>
                              </a>    
                        @endif
                    @endforeach
                 @endif
              @endforeach
            </div>
        </div>
    </section>	
@endsection
