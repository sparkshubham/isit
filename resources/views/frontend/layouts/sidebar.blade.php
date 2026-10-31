@php 
$segment2 = request()->segment(2);
$menu_items = config('menu_config.front_sidebar');
$setting_items = config('menu_config.setting_sidebar');
$user = auth()->user();
@endphp

@if(Auth::check())

<header class="section-header ">


        <nav class="navbar navbar-expand-lg fixed-top navbar-light shadow-sm my-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{url('/post-board')}}">
                    <img class="d-lg-block mobile-none" src="{{ asset('front/images/logo.png') }}" alt=""> 
                    <img class="d-block d-lg-none"  src="{{ asset('front/images/logo-mobile.png') }}" alt="">
                </a>
             <div id="main_nav" class="collapse navbar-collapse">
                @php
        $link = request()->url();
        $link_array = explode('/',$link);
        $valueUrl= end($link_array);
        @endphp
           @if($valueUrl=='explore' || $valueUrl=='anointed')
            <ul class="nav searchbar searchusers ms-auto mb-0">
                <li>
                <input class="search-bar" id="search_users_explore" autocomplete="off"  name="search_keyword" type="text" placeholder="Search">
                <div class="following-users scrollbar-outer" id="search_users_append">
                
                    </div>
                </li>
            </ul>
            @elseif($valueUrl=='anointed shuhba')
            <ul class="nav searchbar ms-auto mb-0">
                <li>
                <form method="get" action="{{request()->url()}}" id="common-search" >
                    <!-- @csrf -->
                    <input class="search-bar"  name="search_keyword" type="text" placeholder="Search" >
                </form>
                    </li>
            </ul>
            @elseif($valueUrl=='event-list-other' || $valueUrl=='event-list-my')
            <ul class="nav searchbar ms-auto mb-0">
                <li>
                    
                    <form method="get" action="{{request()->url()}}" id="common-search" >
                        <input type="hidden" name="type"  class="event_type_search"  value="{{request()->type=='my'?'my':'saved'}}" id="event_type_search">
                        <input value="{{request()->search_keyword}}"   class="search-bar"  name="search_keyword" type="text" placeholder="Search" >
                    </form>
               </li>
            </ul>
            @elseif($valueUrl=='store-other')
            <ul class="nav searchbar ms-auto mb-0">
                <li>
                    <form method="get" action="{{request()->url()}}" id="common-search" >
                        <input type="hidden" name="product_type" value="{{request()->product_type}}" class="product_type_search" id="product_type_search">
                        <input class="search-bar" value="{{request()->search_keyword}}"   name="search_keyword" type="text" placeholder="Search" >
                    </form>
                </li>
            </ul>
            @endif

                <ul class="nav me-auto mb-0 float-start leftpannel-mobile">
                   <li class="nav-item leftpannel-open hide"><a class="nav-link" href="#" data-bs-toggle="modal"><i class="uil uil-bars"></i></a></li>
                </ul>
                <ul class="nav nav-pills ms-auto d-lg-none ">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><img  src="{{ asset('front/images/create-posts-icon.svg') }}" alt=""></a>
                        <ul class="dropdown-menu dropdown-menu-end fade-up">
                            <li><a class="dropdown-item" href="#" onclick="CreatePostGeneral()"> General Post</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#CreateAnointedPost"> Anointed Post </a></li>
                          </ul>
                    </li>

                    <li class="nav-item message_hide"><a class="nav-link" href="{{url('/message')}}"><img class="inactive"   src="{{ asset('front/images/messages-icon.svg') }}" alt=""><img class="active" src="{{ asset('front/images/messages-icon-active.svg') }}" alt=""></a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#"><img class="inactive" src="images/notifications-icon.svg" alt=""><img class="active" src="images/notifications-icon-active.svg" alt=""></a></li> -->
                    <!-- <li class="nav-item mobile-only"><a class="nav-link" href="#"><img src="images/search.svg" alt=""></a></li> -->
                    <!-- <li class="nav-item">

                        <div class="search-wrapper">
                            <div class="input-holder">
                                <input type="text" class="search-input" placeholder="Type to search" />
                                <button class="search-icon"><img  src="{{ asset('front/images/search.svg') }}" alt=""></button>
                            </div>
                            <span class="close"></span>
                        </div>

                    </li> -->
                    <li class="nav-item mobile-only"><a class="nav-link" href="#" data-trigger="navbar_main" type="button"><img   src="{{ asset('front/images/more-icon.svg') }}"alt=""></a></li>
                </ul>

                <ul class="nav nav-pills ms-auto  mobile-none">
                    @foreach ($menu_items as $key => $menu)
                        @if($menu['flag']==1)
                            <li class="nav-item {{in_array($segment2,$menu['active_segments']) ? 'active' : ''}}">
                                <a title="{{$menu['title']}}" class="nav-link" href="{{url($menu['url'])}}">
                                    <div class="icon" >
                                            <img class="inactive" src="{{ asset($menu['inactive_path']) }}" >      
                                            <img class="active" src="{{ asset($menu['path']) }}" >
                                    </div> {{$menu['label']}}
                                    <span class="badge hide" id="dynamic_count_{{$menu['title']}}"><span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown desktop-only">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <div class="icon">
                                            <img  src="{{ asset('front/images/create-posts-icon.svg') }}"  alt="">
                                        </div> Create New Post
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end fade-up">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="CreatePostGeneral()"> General Post
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#CreateAnointedPost">Anointed Post
                                            </a>
                                        </li>
                                    </ul>
                            </li>
                        @endif
                        @endforeach    
                    <li class="nav-item dropdown profile-menu">
                        <a class="nav-link dropdown-toggle" href="#"  data-bs-toggle="dropdown"><div class="icon"><img src="{{ asset('front/images/profile-icon.svg') }}" alt=""></div> Profile</a>
                        <ul class="dropdown-menu dropdown-menu-end fade-up">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <div class="menu-and-icon"><span class="menu-icon"><img src="{{ asset('front/images/eye-icon.svg') }}" alt=""></span> Public Visibility</div>
                                    <div class="visiblity-toggle">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" {{Auth::user()->is_public_visibility==1?'checked':''}} type="checkbox" id="is_public_visiblity"  onclick="publicVisblityChange(this.value)" value="{{Auth::user()->is_public_visibility}}";>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @foreach ($setting_items as $key => $items)
                                <li>
                                    <a class="dropdown-item" href="{{url($items['url'])}}">
                                        <div class="menu-and-icon"><span class="menu-icon"><img src="{{ asset($items['path']) }}"  alt=""></span>{{$items['label']}} </div>                           
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
             </div> 
            </div> <!-- container-fluid.// -->
           </nav>
</header> <!-- section-header.// -->



<!-- ============= COMPONENT ============== -->
<nav id="navbar_main" class="mobile-offcanvas navbar navbar-expand-lg fixed-top navbar-light shadow-sm my-navbar">
 <div id="main_nav" class="container-fluid">
    <div class="offcanvas-header">  
		<button class="btn-close float-end"></button>
	</div>
    <!-- <a class="navbar-brand" href="#"><img class="d-lg-block " src="images/logo.png" alt=""> <img class="d-block d-lg-none" src="images/logo-mobile.png" alt=""></a> -->
    <a class="navbar-brand" href="{{url('/post-board')}}"><img src="{{ asset('front/images/logo.png') }}"   alt=""></a>

       
      

	<ul class="nav nav-pills ms-auto">
        @foreach ($menu_items as $key => $menu)
            @if($menu['flag']==1)
                <li class="nav-item {{in_array($segment2,$menu['active_segments']) ? 'active' : ''}}">
                    <a title="{{$menu['title']}}" class="nav-link" href="{{url($menu['url'])}}">
                        <div class="icon" >
                                <img class="inactive" src="{{ asset($menu['inactive_path']) }}" >      
                                <img class="active" src="{{ asset($menu['path']) }}" >
                        </div> {{$menu['label']}}
                        <span class="badge hide" id="dynamic_count_{{$menu['title']}}"><span>
                    </a>
                </li>
            @else
                <li class="nav-item dropdown desktop-only">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <div class="icon">
                                <img  src="{{ asset('front/images/create-posts-icon.svg') }}"  alt="">
                            </div> Create New Post
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end fade-up">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#CreatePostGeneral"> General Post
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#CreateAnointedPost">Anointed Post
                                </a>
                            </li>
                        </ul>
                </li>
            @endif
            @endforeach    
        <li class="nav-item dropdown profile-menu">
            <a class="nav-link dropdown-toggle" href="#"  data-bs-toggle="dropdown"><div class="icon"><img src="{{ asset('front/images/profile-icon.svg') }}" alt=""></div> Profile</a>
            <ul class="dropdown-menu dropdown-menu-end fade-up">
                <li>
                    <a class="dropdown-item" href="#">
                        <div class="menu-and-icon"><span class="menu-icon"><img src="{{ asset('front/images/eye-icon.svg') }}" alt=""></span> Public Visibility</div>
                        <div class="visiblity-toggle">
                            <div class="form-check form-switch">
                                <input class="form-check-input" {{Auth::user()->is_public_visibility==1?'checked':''}} type="checkbox" id="is_public_visiblity"  onclick="publicVisblityChange(this.value)" value="{{Auth::user()->is_public_visibility}}";>
                            </div>
                        </div>
                    </a>
                </li>
                @foreach ($setting_items as $key => $items)
                    <li>
                        <a class="dropdown-item" href="{{url($items['url'])}}">
                            <div class="menu-and-icon"><span class="menu-icon"><img src="{{ asset($items['path']) }}"  alt=""></span>{{$items['label']}} </div>                           
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
 </div> 
</nav>

@endif

@include('frontend.common.confirm-modal')

@include('frontend.anointed-post.general-post-modal')
@include('frontend.anointed-post.anointed-post-modal')



@include('frontend.dashboard.report-post')

@section('page_wise_scripts')

{!! JsValidator::formRequest('App\Http\Requests\Frontend\FrontChangeprofile','#profile-form') !!}
{!! JsValidator::formRequest('App\Http\Requests\Frontend\FrontChangePasswordRequest','#change-password-form') !!}


@if(auth()->user())

<?php
$twillio =Session::get('twillio');
$accessToken='';
if(isset($twillio['accessToken'])){

$accessToken=$twillio['accessToken'];
}
?>
<input type="hidden" id="token_token" name="token_token" value="{{$accessToken}}">
<input type="hidden" id="twillio_identity" name="twillio_identity" value="{{ isset($twillio['identity']) ? $twillio['identity'] : '' }}">
<input type="hidden" id="env" name="env" value="{{ isset($twillio['env']) ? $twillio['env'] : '' }}">

<input type="hidden" name="video_call_room_name"  id="video_call_room_name">
<input type="hidden" name="voice_call_room_name"  id="voice_call_room_name">
<input type="hidden" name="room_name_final"  id="room_name_final">
<input type="hidden" name="id_video_audio_call"  id="id_video_audio_call">



                                    



  
@endif


<script>

    
    $(document).ready(function() {
        
        $('.messages-part .sidebar-group .sidebar .list-group-item').on("click", function () {
            $('.messages-part .chat').addClass("active");
            $('.messages-part .sidebar-group').addClass("d-none");
            // setTimeout(RemoveClass, 1000);
        });
        $('.messages-part .chat-header-user .back-btn').on("click", function () {
            $('.messages-part .chat.active').removeClass("active");
            $('.messages-part .sidebar-group.d-none').removeClass("d-none");
            //   setTimeout(RemoveClass, 1000);
        });
                        
            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                    //  $('#imagePreview').attr('src',e.target.result)
                        $('#imagePreview').css('background-image', 'url("")');
                        var img = $('<img>').attr('src', e.target.result);
                        $('#imagePreview').html(img);

                        // $('.profile-pic').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }


            $("#profile_picture").on('change', function(){
                readURL(this);
            });

            $("#profile_picture").on('click', function() {
            $("#profile_picture").click();
            });
    });
    //Dropzone code start here
    var uploadedDocumentMap = {}
    Dropzone.options.documentDropzone = {
        url: '{{ route('upload-post-media2') }}',
        // maxFilesize: 20, // M
        // maxFilesize: 500,
        maxFilesize: 209715200,
        addRemoveLinks: true,
        uploadMultiple: false,
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4,.webm,.ogg",
        dictDefaultMessage: "+",
        maxFiles: 5,
        // timeout: 1800000,
        timeout: 180000,
        headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        init: function() {
            var that = this;
            this.on("maxfilesexceeded", function(file){
                alert("You can select only 4 Image and 1 video. or only 5 image without video");
                that.removeFile(file);
            });
    
            that.on('sending', function(file) {
                $('.savebtnanointed').prop('disabled', true);
                $('.savebtngenral').prop('disabled', true);
                $('#product_submit_btn').prop('disabled', true);

                $('#file-input').val(1)
                $('#anointed-file-input').val(1)
                $('#product-file-input').val(1)

                var videoFlag=  $('.videoExist').val();
                
                if(file.type == "video/mp4" ||file.type=="video/webm"  ||file.type=="video/ogg") {
                    if(videoFlag>=1)
                    {
                        alert("You can select only 1 video at a time.");
                        that.removeFile(file);
                    }
                    $('.videoExist').val(1);
                }
            
                var legthFile=$('#legthFile').val();
                if(legthFile>=5)
                {
                    alert("You can select only 4 Image and 1 video. or only 5 image without video");
                    that.removeFile(file);
                }
                else{
                    legthFile++;
                    $('#legthFile').val(legthFile);
                }

            });

        },
        success: function (file, response) {
                $('#file-input').val(1)
                $('#anointed-file-input').val(1)
                $('#product-file-input').val(1)
                $('.savebtnanointed').prop('disabled', false);
                $('.savebtngenral').prop('disabled', false);
                $('#product_submit_btn').prop('disabled', false);

                
                $('form').append('<input type="hidden"  name="file_media[]" value="' + response.name + '" data-val="1"><input type="hidden" name="thumbFileName" data-id="'+response.name+'" value="'+response.thumbFileName+'">')
                uploadedDocumentMap[file.name] = response.name
        },
        removedfile: function (file) {
       
        if(file.status=='error')
        {
                file.previewElement.remove();
                $('.savebtnanointed').prop('disabled', false);
                $('.savebtngenral').prop('disabled', false);
                $('#product_submit_btn').prop('disabled', false);

        }
        else
        {
                file.previewElement.remove()
                var legthFile=$('#legthFile').val();
                legthFile-- 
                $('#legthFile').val(legthFile);
                if($('#legthFile').val()==0)
                {
                   $('#product-file-input').val('');
                }

                var name = ''
                if(file.type == "video/mp4" ||file.type=="video/webm"  ||file.type=="video/ogg") {
                    $('.videoExist').val(0);
                }
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                }
                else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find('input[name="file_media[]"][value="' + name + '"]').remove()
                $('form').find('input[name="thumbFileName"][data-id="' + name + '"]').remove()
                var lengthFile = $('input[name="file_media[]"]').length;
                if(lengthFile==0)
                {
                    $('#file-input').val('')
                    $('#anointed-file-input').val('')
                    $('#product-file-input').val('')

                
                }
        }
        }
    }

//Dropzone code end  here//


        //generalpost validation code start  here//
            $('#generalpost-form').validate({
                debug: false,
                ignore : [],
                errorClass: 'help-block',
                errorElement: "span",
                
                rules: {
                    heading: {
                        maxlength:50,
                    },
                    description : {
                        required: function(element) {
                            if($("#file-input").val() == '')
                            return true;
                        else if($("#file-input").val() && $('#heading').val())
                            return false;
                        else
                            return false;
                        }, 
                        minlength: 10,
                        maxlength: 1000,
                    },
                    file:{
                        required: function(element) {
                        if($("#description").val() == '')
                            return true;
                        else
                            return false;
                        },    
                    },
                },
                messages: {
                    heading: {
                        required: "The heading field is required.",
                    },
                    description: {
                        required: "The description field is required.",
                    },
                    file: {
                        required: "The file field is required.",
                    },
                },

                errorPlacement: function(error,element){
                    if($(element).prop('nodeName') == 'SELECT'){
                    error.insertAfter($(element).next('span'));
                    }
                    else if($(element).prop('type') == 'file'){
                    error.insertAfter($(element).parent().parent());
                    }
                    else{
                    error.insertAfter($(element));
                    }
                },
                highlight: function (element, errorClass, validClass)
                        {
                            $(element).parents('.col-field-wrapper').addClass('has-error');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).parents('.col-field-wrapper').removeClass('has-error');
                        },
                        invalidHandler: function(form, validator)
                        {
                            if (!validator.numberOfInvalids())
                                return;

                            $('html, body').animate({
                                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                            }, 500);
                        }
                });

                

        //generalpost validation code end  here//


        //anointedPost validation code start  here//
            $('#anointedPost-form').validate({
                debug: false,
                ignore : [],
                errorClass: 'help-block',
                errorElement: "span",
                rules: {
                    category: {
                        required:true
                    },
                    country: {
                        required:true
                    },
                    zip_code: {
                        minlength: 5,
                        maxlength:9,
                        required:true
                    },
                    city_name: {
                        minlength: 2,
                        maxlength:50,
                        required:true
                    },
                    description : {
                        required: function(element) {
                        if($("#anointed-file-input").val()=='')
                            return true;
                        else
                            return false;
                        }, 
                        minlength: 10,
                        maxlength: 1000,
                    },
                },
                messages: {
                    country: {
                        required: "The country field is required.",
                    },
                    category: {
                        required: "The category field is required.",
                    },
                    city_name: {
                        required: "The city name field is required.",
                    },
                    zip_code: {
                        required: "The zip code field is required.",
                    },
                    description: {
                        required: "The anointed details field is required.",
                    },
                },

                errorPlacement: function(error,element){
                    // if($(element).prop('nodeName') == 'SELECT'){
                    //   error.insertAfter($(element).next('span'));
                    // }
                    if($(element).prop('type') == 'file'){
                    error.insertAfter($(element).parent().parent());
                    }
                    else{
                    error.insertAfter($(element));
                    }
                },
                highlight: function (element, errorClass, validClass)
                        {
                            $(element).parents('.col-field-wrapper').addClass('has-error');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).parents('.col-field-wrapper').removeClass('has-error');
                        },
                        invalidHandler: function(form, validator)
                        {
                            if (!validator.numberOfInvalids())
                                return;

                            $('html, body').animate({
                                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                            }, 500);
                        }
                });

        //anointedPost validation code end  here//

       
        // explore data start here
            $('#search_users_explore').on('keyup', function(){
                var text = $('#search_users_explore').val();
                $('#search_users_append').html('')   
                var url = baseurl+'/search-user';
                var replaced =text.replace(/%20/g, " ");
                $.ajax({
                    type:"GET",
                    url: url,
                    data: {search_keyword: replaced},
                    success: function(data) {
                            var userPath=data.data.path.user_media;
                            var users=data.data.users;
                            var search_html=data.data.search_html;
                            var html='';
                            $('#search_users_append').html(search_html)
                            $('.following-users').addClass('add_display_block');

                    }
                });
            });


            $('#search_users_explore').on('click', function(){
                
                $('#search_users_append').html('')  
                var text = $('#search_users_explore').val();
                var url = baseurl+'/search-user'; 
                var replaced =text.replace(/%20/g, " ");
                $.ajax({
                    type:"GET",
                    url: url,
                    data: {search_keyword: replaced},
                    success: function(data) {
                            var userPath=data.data.path.user_media;
                            var users=data.data.users;
                            var search_html=data.data.search_html;
                            var html='';
                            $('#search_users_append').html(search_html)   
                            $('.following-users').addClass('add_display_block');
                    }
                });
            });

        //explore data end here

        jQuery(document).ready(function(){
            jQuery('.scrollbar-outer').scrollbar();
        });

        $(document).mouseup(function(e) {
            var container = $(".following-users");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                // container.hide();
                $('.following-users').removeClass('add_display_block');
            }
        });
   var parmas='';
        /// load data using  ajax start here
        function loadMoreDataDashboard(page,parmas)
        {
            $.ajax({
                url:'?page=' + page + parmas,
                type:'get',
                beforeSend: function()
                {
                    $(".ajax-load").show();
                }
            })
            .done(function(data){
                    if(data.html == "" || data.save_html==""|| data.my_post_html==""){
                        $('.ajax-load').html("");
                        return;
                    }
                    $('.ajax-load').hide();

                    $("#post_ano_exp").append(data.html);
                    $("#post_board_data").append(data.html);
                    $("#event_list").append(data.html);
                    $("#store_list").append(data.html);
                
                    $('#event_list').append(data.html);
                    $('#saved_event_list').append(data.html);
                    $('.owl-carousel').addClass('owl-loaded')
                    $('.owl-carousel').addClass('owl-drag')
                    if(data.my_post_html)
                    {
                        $('#saved-posts').append(data.save_html);
                        $('#my-posts').append(data.my_post_html);
                    
                    }
                })
                // Call back function
                .fail(function(jqXHR,ajaxOptions,thrownError){
                    console.log("Server not responding.....");
                });
        
        }

            var page =1;
            var s = window.location.pathname
            var final = s.substr(s.lastIndexOf('/') + 1);
            // if(final=='my-profile'){// alert(final);
            // }
            if(final=='post-board'){
                $('.leftpannel-open').removeClass('hide')
            }else{
                $('.leftpannel-open').addClass('hide')
            }

            $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() >= $(document).height()-150 && page!=0){     
                    if(is_scroll){
                            if(final=='other'||final=='my')
                            {
                                var search='{{request()->search_keyword}}';
                                var value='{{request()->product_type}}';
                                parmas='&product_type='+value+'&search_keyword='+search;
                                page++;
                                loadMoreDataDashboard(page,parmas);

                            }
                            // if(final=='event-list-other' || final=='event-list-my')
                            // {
                            //     var search='{{request()->search_keyword}}';
                            //     var value='{{request()->type}}';
                            //     parmas='&type='+value+'&search_keyword='+search;
                            //     $('#event_type_search').val(value);
                            //     page++;
                            //     loadMoreDataDashboard(page,parmas);
                            // }
                            if(final=='my-profile')
                            {
                                var post_type=$('#my_save_post_flag').val();
                                parmas='&post_type='+post_type;
                                page++;
                                loadMoreDataDashboard(page,parmas);
                            }
                            
                            if(final=='post-board'||final=='explore'||final=='anointed') {
                                page++;
                                loadMoreDataDashboard(page,parmas);
                            }
                          
                           
                        }
                    }
            });


            $(".notifications-list").scroll(function () {                
                    var notifi = document.getElementById("scroll_notifications");
                    if ((notifi.scrollHeight - notifi.scrollTop)== notifi.clientHeight) {
                        page++;
                        // loadMoreDataNotification(page);
                    }
                });

                  
            function loadMoreDataNotification(page)
            {
                // $("#store_list").html('')
                $.ajax({
                    url:'?page=' + page ,
                    type:'get',
                    beforeSend: function(){
                        $(".ajax-load").show();
                    }
                })
                .done(function(data){
                    if(data.html == ""){
                        console.log('Hey blank here');
                        $('.ajax-load').html("");
                        return;
                    }
                    $('.ajax-load').hide();
                    $("#notification_list").append(data.html);
                    $('.owl-carousel').addClass('owl-loaded')
                    $('.owl-carousel').addClass('owl-drag')

                })
                // Call back function
                .fail(function(jqXHR,ajaxOptions,thrownError){
                    console.log("Server not responding.....");
                    // alert("Server not responding.....");
                });
            }
           


            $('.price_type').on('click', function(){
                var value=$('input[name="price_type"]:checked').val();
                if(value==1){
                    $('#price_box_store').addClass('hide');
                }
                else{
                    $('#price_box_store').removeClass('hide');
                }
            });

            
           
        var page =1;
        var parmas="";
        var s = window.location.pathname
        var final = s.substr(s.lastIndexOf('/') + 1);
          $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() >= $(document).height()-150 && page!=0){     
                        if(final=='store-other'||final=='store-my') {
                                page++;
                                var search='{{request()->search_keyword}}';
                                var product_type= $('#product_type_search').val()
                                parmas='&product_type='+product_type+'&search_keyword='+search;
                                dataLoadStore(page,parmas);
                            }
                        }
            });

            $('#filter_store').on('change', function() {
                var value= this.value;
                page=1;
                var search='{{request()->search_keyword}}';
                parmas='&product_type='+value+'&search_keyword='+search;
                $('#product_type_search').val(value);
                $("#store_list").html('')
                dataLoadStore(page,parmas);
            });
            
            
            function dataLoadStore(page,parmas)
            {
                // $("#store_list").html('')
                $.ajax({
                    url:'?page=' + page + parmas,
                    type:'get',
                    beforeSend: function(){
                        $(".ajax-load").show();
                    }
                })
                .done(function(data){
                 
                    $('.ajax-load').hide();
                    $("#store_list").append(data.html);
                    $('.owl-carousel').addClass('owl-loaded')
                    $('.owl-carousel').addClass('owl-drag')

                    if(data.html ==""){
                        $('.ajax-load').html("");
                        return;
                    }
                })
                // Call back function
                .fail(function(jqXHR,ajaxOptions,thrownError){
                    console.log("Server not responding.....");
                    // alert("Server not responding.....");
                });
            }
      
            

            
        var page =1;
        var s = window.location.pathname
        var final = s.substr(s.lastIndexOf('/') + 1);
          $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() >= $(document).height()-150 && page!=0){     
                            if(final=='event-list-other' || final=='event-list-my')
                            {
                                var search='{{request()->search_keyword}}';
                                var value='{{request()->type}}';
                                parmas='&type='+value+'&search_keyword='+search;
                                $('#event_type_search').val(value);
                                page++;
                                loadMoreDataEventList(page,parmas);
                            }
                        }
            });

            
            
        var parmas='';
        // $('#event_type_search').val('my')
        $('.save_unsave_event_filter').click(function() {
            var event_type=$(this).attr('data-type');
            $('#event_type_search').val(event_type)
            page=1;
            $("#event_list").html('');
            $('#saved_event_list').html('');
            loadMoreDataEventList(page,parmas);

        });

        /// load data using  ajax start here
        function loadMoreDataEventList(page,parmas){
            $.ajax({
                url:'?page=' + page + parmas,
                type:'get',
                beforeSend: function()
                {
                    $(".ajax-load").show();
                }
            })
            .done(function(data){
                
                  
                    $("#event_list").append(data.my_event_html);
                    $('#saved_event_list').append(data.saved_event_html);
                  
                    $('.ajax-load').hide();
                    $('.owl-carousel').addClass('owl-loaded')
                    $('.owl-carousel').addClass('owl-drag')
                    if(data.my_event_html =="" ||data.saved_event_html==""){
                        $('.ajax-load').html("");
                        return;
                    }
                })
                // Call back function
                .fail(function(jqXHR,ajaxOptions,thrownError){
                    console.log("Server not responding.....");
                });
        
        }


    


          



            $('#help_category').on('change', function() {
                var optVal= $("#help_category option:selected").attr('data-name');
                if(optVal=='Other'){
                    $('#help_description_hide').removeClass('hide')
                }else{
                    $('#help_description_hide').addClass('hide')
                    $('#help_description').val('')
                }
            });
            $('#report_category').on('change', function() {
                var optVal= $("#report_category option:selected").attr('data-name');
                if(optVal=='Other'){
                  
                    $('#report_description_hide').removeClass('hide')
                }else{

                    $('#report_description_hide').addClass('hide')
                    $('#help_description_report').val('')

                  
                }
            });
        /// load data using  ajax end here

        //store validation code start  here//
        $('#create-store-form').validate({
            debug: false,
            ignore : [],
            errorClass: 'help-block',
            errorElement: "span",
            rules: {
                product_type: {
                    required:true
                },
                category_id: {
                    required:true
                },
                country_id: {
                    required:true
                },
                name: {
                    required:true,
                    minlength: 3,
                    maxlength: 50,
                },
                city: {
                    minlength: 2,
                    maxlength:50,
                    required:true
                },
                zip_code: {
                    minlength: 5,
                    maxlength:9,
                    required:true
                },
                description : {
                    // required: true,
                    minlength: 10,
                    maxlength: 1000,
                },
                file: {
                    required: true
                },
                price: {
                    required: function(element) {
                            var price_type=$('input[name="price_type"]:checked').val();
                            var product_price=$('#product_price').val();
                            if(price_type==2)
                                return true;
                            else
                                return false;
                    },  
                    min: function(element){
                        var price_type=$('input[name="price_type"]:checked').val();
                        var product_price=$('#product_price').val();
                        if(price_type==2){
                            return 1;
                        }
                        else{
                            return '';
                        }
                }

                 
                },
            },
            messages: {
                product_type: {
                    required: "The product type field is required.",
                },
                name: {
                    required: "The product name field is required.",
                },
                country_id: {
                    required: "The country field is required.",
                },
                category_id: {
                    required: "The category field is required.",
                },
                city: {
                    required: "The city name field is required.",
                },
                zip_code: {
                    required: "The zip code field is required.",
                },
                // description: {
                //     required: "The product description field is required.",
                // },
                file: {
                    required: "Please select atleast 1 image/video of the product.",
                },
                price:{
                    required: "Please enter a price more then zero or mark it as free.",
                }
            },

            errorPlacement: function(error,element){
                if($(element).prop('type') == 'file'){
                error.insertAfter($(element).parent().parent());
                }
                else{
                error.insertAfter($(element));
                }
            },
            highlight: function (element, errorClass, validClass)
                    {
                        $(element).parents('.col-field-wrapper').addClass('has-error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.col-field-wrapper').removeClass('has-error');
                    },
                    invalidHandler: function(form, validator)
                    {
                        if (!validator.numberOfInvalids())
                            return;

                        $('html, body').animate({
                            scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                        }, 500);
                    }
            });


           
        
          //store validation code end  here//

          //event validation code start  here//
          $('#create-event-form').validate({
            debug: false,
            ignore : [],
            errorClass: 'help-block',
            errorElement: "span",
            rules: {
                date: {
                    required:true,
                    date: true
                },
                start_time: {
                    required:true,
                },
                name: {
                    minlength: 3,
                    maxlength: 50,
                    required:true
                },
                end_time: {
                    required:true,
                },
                event_picture:{
                    required:true,
                    required: function(element) {
                        var optVal= $("#event_hidden_id").val();
                        if(optVal)
                            return false;
                        else
                            return true;
                        },  
                }
            },
            messages: {
                date: {
                    required: "The date field is required.",
                },
                name: {
                    required: "The event name field is required.",
                },
                start_time: {
                    required: "The start time field is required.",
                },
                end_time: {
                    required: "The end time field is required.",
                },
                event_picture: {
                    required: "The event picture field is required.",
                },
                
            },

            errorPlacement: function(error,element){
                if($(element).prop('type') == 'file'){
                error.insertAfter($(element).parent().parent());
                }
                else{
                error.insertAfter($(element));
                }
            },
            highlight: function (element, errorClass, validClass)
                    {
                        $(element).parents('.col-field-wrapper').addClass('has-error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.col-field-wrapper').removeClass('has-error');
                    },
                    invalidHandler: function(form, validator)
                    {
                        if (!validator.numberOfInvalids())
                            return;

                        $('html, body').animate({
                            scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                        }, 500);
                    }
            });

$.validator.addMethod("maxDate", function(value, element) {
	var curDate = new Date();
    var inputDate = new Date(value);
    if (inputDate < curDate)
		return true;
	return false;
}, "Invalid Date!");








            $('#report-post-form').validate({
            debug: false,
            ignore : [],
            errorClass: 'help-block',
            errorElement: "span",
            rules: {
                category: {
                    required:true
                },
                description: {
                    required: function(element) {
                        var optVal= $("#report_category option:selected").attr('data-name');
                        if(optVal=='Other')
                              return true;
                        else
                            return false;
                        },    
                },
            },
            messages: {
                category: {
                    required: "The category field is required.",
                },
                description: {
                    required: "The description field is required.",
                },
            },

            errorPlacement: function(error,element){
                if($(element).prop('type') == 'file'){
                error.insertAfter($(element).parent().parent());
                }
                else{
                error.insertAfter($(element));
                }
            },
            highlight: function (element, errorClass, validClass)
                    {
                        $(element).parents('.col-field-wrapper').addClass('has-error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.col-field-wrapper').removeClass('has-error');
                    },
                    invalidHandler: function(form, validator)
                    {
                        if (!validator.numberOfInvalids())
                            return;

                        $('html, body').animate({
                            scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                        }, 500);
                    }
            });


            $('#help-form').validate({
            debug: false,
            ignore : [],
            errorClass: 'help-block',
            errorElement: "span",
            rules: {
                category: {
                    required:true
                },
                description: {
                    required: function(element) {
                        var category= $("#help_category option:selected").attr('data-name');
                        // var category=$('input[name="category"]:selected').val();

                        if(category=='Other')
                              return true;
                        else
                            return false;
                        }, 
                    
                },
            },
            messages: {
                category: {
                    required: "The category field is required.",
                },
                description: {
                    required: "The description field is required.",
                },
            },

            errorPlacement: function(error,element){
                if($(element).prop('type') == 'file'){
                error.insertAfter($(element).parent().parent());
                }
                else{
                error.insertAfter($(element));
                }
            },
            highlight: function (element, errorClass, validClass)
                    {
                        $(element).parents('.col-field-wrapper').addClass('has-error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.col-field-wrapper').removeClass('has-error');
                    },
                    invalidHandler: function(form, validator)
                    {
                        if (!validator.numberOfInvalids())
                            return;

                        $('html, body').animate({
                            scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                        }, 500);
                    }
            });

            
            //anointedPost validation code start  here//
            $('#CreateChatRoom-form').validate({
                debug: false,
                ignore : [],
                errorClass: 'help-block',
                errorElement: "span",
                rules: {
                    name: {
                        minlength: 3,
                        maxlength: 50,
                        required:true
                        
                    },
                    group_Profile_pic: {
                        required:true
                    },
                    appendBlankparticpant: {
                            required: true, 
                            minlength: 1,
                            maxlength: 4,  
                    },
                   
                },
                messages: {
                    group_Profile_pic: {
                        required: "The Group profile field is required.",
                    },
                    name: {
                        required: "The Group name field is required.",
                    },
                    appendBlankparticpant: {
                        required: "Please select atleast one user.",
                        minlength:"Please select atleast one user.",
                        maxlength:"You can only add 5 participants to a chat room."

                    },
                    
                },

                errorPlacement: function(error,element){
                    // if($(element).prop('nodeName') == 'SELECT'){
                    //   error.insertAfter($(element).next('span'));
                    // }
                    if($(element).prop('type') == 'file'){
                    error.insertAfter($(element).parent().parent());
                    }
                    else{
                    error.insertAfter($(element));
                    }
                },
                highlight: function (element, errorClass, validClass)
                        {
                            $(element).parents('.col-field-wrapper').addClass('has-error');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).parents('.col-field-wrapper').removeClass('has-error');
                        },
                        invalidHandler: function(form, validator)
                        {
                            if (!validator.numberOfInvalids())
                                return;

                            $('html, body').animate({
                                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                            }, 500);
                        }
                });

                $(".comment-form").submit(function(e) {
                    e.preventDefault();
                });


    // $("#agora-live-form").submit(function(e) {
    //     e.preventDefault();
    // });


            $('#agora-live-form').validate({
            debug: false,
            ignore : [],
            errorClass: 'help-block',
            errorElement: "span",
            rules: {
                go_live_title: {
                    required:true,
                    minlength: 3,
                    maxlength: 20,
                },
            },
            messages: {
                go_live_title: {
                    required: "The title field is required.",
                },
              
            },

            errorPlacement: function(error,element){
                if($(element).prop('type') == 'file'){
                error.insertAfter($(element).parent().parent());
                }
                else{
                error.insertAfter($(element));
                }
            },
            highlight: function (element, errorClass, validClass)
                    {
                        $(element).parents('.col-field-wrapper').addClass('has-error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.col-field-wrapper').removeClass('has-error');
                    },
                    invalidHandler: function(form, validator)
                    {
                        if (!validator.numberOfInvalids())
                            return;

                        $('html, body').animate({
                            scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                        }, 500);
                    }
            });

         



   
</script>
@endsection




