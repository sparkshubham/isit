
@foreach($posts as $posts_val)
                @if(!empty($posts_val->getMedia))
                        @php $media =$posts_val->getMedia[0];@endphp
                        @php
                          $postFolderName = config('constants.post_media.upload_path');
                          $path['post_media'] = getS3Url($postFolderName);
                        @endphp
                        @if($posts_val->getMedia[0]->media_type==2)
                        <a href="{{ route('explore-view', ['post_id' => $media->post_id] ) }}" class="single-image">
                            <img src="{{$path['post_media'].$media->thumb_image_name}}" alt=''>
                            <span class="video-icon"><img src="{{ asset('front/images/play-icon.svg') }}" alt=""></span>
                        </a>
                        @else
                            <a href="{{ route('explore-view', ['post_id' => $media->post_id] ) }}" class="single-image">
                                <img src="{{$path['post_media'].$media->file_name}}" alt=''>
                              </a>    
                        @endif
                 @endif
@endforeach