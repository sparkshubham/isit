<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} | {{ !empty($title)?$title:'' }}</title>
      <!-- Favicons -->
    <link rel="shortcut icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">
    <!-- Fornt ccs -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    {{ Html::style('front/css/bootstrap.min.css') }}
    {{ Html::style('front/css/owl.theme.default.css') }}
    {{ Html::style('front/css/owl.carousel.css') }}
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">    
    <!-- Fornt Js -->
    <!-- <script src="{{ asset('public/front/js/jquery.validate.min.js') }}"></script> -->     
    
    {{ Html::style('front/css/style.css') }}
    {{ Html::style('front/css/responsive.css') }}   

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/6.0.0-beta.2/dropzone.min.css" integrity="sha512-qkeymXyips4Xo5rbFhX+IDuWMDEmSn7Qo7KpPMmZ1BmuIA95IPVYsVZNn8n4NH/N30EY7PUZS3gTeTPoAGo1mA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css"  />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.css" integrity="sha512-BB0bszal4NXOgRP9MYCyVA0NNK2k1Rhr+8klY17rj4OhwTmqdPUQibKUDeHesYtXl7Ma2+tqC6c7FzYuHhw94g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
	
    
    {{ Html::style('front/css/agora-style.css') }}
    {{ Html::style('front/css/twillio-video.css') }}

    
    
    @yield('page_wise_style') 

    
   
</head>
<body class="hold-transition skin-blue sidebar-mini " id="isit_body">
    <div class="wrapper">
        
            @include('frontend.layouts.header')
            @if(Auth::check())
            @else
               @include('frontend.layouts.navbar')
            @endif
            
        <!-- Left side column. contains the sidebar -->
            @include('frontend.layouts.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper twillio_video_call">
           @yield('content')
        </div>
    </div>
   
   
    
@include('frontend.agora.agora-go-live')
@if(Auth::check()) 
@include('frontend.message.video')
@endif
@include('frontend.dashboard.share-modal')


    {{ Html::script('front/js/popper.min.js') }}
    {{ Html::script('adminLTE/bower_components/jquery/dist/jquery.min.js') }}
    {{ Html::script('front/js/jquery-3.6.0.min.js') }}
    {{ Html::script('front/js/bootstrap.min.js') }}
    {{ Html::script('front/js/owl.carousel.min.js') }}
    {{ Html::script('front/js/jquery.scrollbar.js') }}
    {{ Html::script('front/js/custom.js') }}
    {{ Html::script('front/js/toastr.min.js') }}
    {{ Html::script('front/js/jsvalidation/jsvalidation.js') }}
    {{ Html::script('front/js/jquery.validate.min.js') }}
    {{ Html::script('front/js/moment.min.js') }}



    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.js" integrity="sha512-1QoWYDbO//G0JPa2VnQ3WrXtcgOGGCtdpt5y9riMW4NCCRBKQ4bs/XSKJAUSLIIcHmvUdKCXmQGxh37CQ8rtZQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js" integrity="sha512-JZSo0h5TONFYmyLMqp8k4oPhuo6yNk9mHM+FY50aBjpypfofqtEWsAgRDQm94ImLCzSaHeqNvYuD9382CEn2zw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    
    <link data-require="sweet-alert@*" data-semver="0.4.2" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

    @if (Auth::check())
    @php $notifictaion_count=getUserNotificationUnreadCount(Auth::user()->id);@endphp
    <script> 
    //    var audio = {};
    //     audio["ring"] = new Audio();
    //     var url = baseurl + '/ring/new_phone_ring.mp3';
    //     audio["ring"].src = url
    //     audio["ring"].loop = true;

       var baseurl = '{{ url("") }}';
       var BASEURL = '{{ url("") }}';
       var chatenv = '{{env("APP_ENV")}}';
       var loggedInId = '{{Auth::user()->id}}';
       var loggedInUsername= '{{Auth::user()->first_name}} {{Auth::user()->last_name}}';
       var mytwillioSid = '{{Auth::user()->twilio_s_id}}';
       var recordingblob=null;
       var agoraAppId="{{env('AGORA_APP_ID')}}"
       var obj={};
       var is_scroll=true;
       console.log('{{$notifictaion_count}}')
       $('#dynamic_count_Notification').addClass('hide');
       if('{{$notifictaion_count}}'>0)
       {
        $('#dynamic_count_Notification').removeClass('hide');
        $('#dynamic_count_Notification').html('{{$notifictaion_count}}');
       }
       
    </script> 
  
    @endif

    <script>
        $(document).ready(function(){
            $('#isit_body').fadeIn(2000);
        });
        $(document).ready(function() {    
            toastr.options.closeButton = true;
            toastr.options.timeOut = 2500 ;
            toastr.options.extendedTimeOut = 10;
            @if(Session::has('error'))
                toastr.error('{{ Session::get('error') }}');
            @elseif(Session::has('success'))
                toastr.success('{{ Session::get('success') }}');
            @endif
        });     

    </script>

    


    {{ Html::script('front/js/isit-common.js') }}
    {{ Html::script('front/js/post-common.js') }} 
    <!-- {{ Html::script('front/js/agora/AgoraRTCSDK-3.6.11.js') }} 
    {{ Html::script('front/js/agora/rtm/index.min.js"') }}  -->


    <!-- Agora SDKs -->
    <script src="https://cdn.agora.io/sdk/release/AgoraRTCSDK-3.6.11.js"></script>
    
    <!-- /front/js/agora/agora-audience-client.js -->
    <script src="https://cdn.jsdelivr.net/npm/agora-rtm-sdk@1.5.0/index.min.js"></script>
    <!-- {{ Html::script('front/js/agora.js') }} -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>    
	<!-- external css -->
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script type="text/javascript">
        var CurrentFCM;
        var isCaller=false;
        var isCallStart=false
        var is_go_live=false;
		// setup tooltip
		// $("body").popover({
		// 	selector: '[data-toggle="popover"]'
		// });
		// toggle ui
		$("#mic-btn").prop("disabled", true);
		$("#mic-dropdown").prop("disabled", true);
		$("#video-btn").prop("disabled", true);
		$("#cam-dropdown").prop("disabled", true);
		$("#screen-share-btn").prop("disabled", true);
		$("#exit-btn").prop("disabled", true);
		$("#toggle-stats-btn").prop("disabled", true);

		$(document).ready(function(){
			$("#modalFormAgora").modal("show");
		});
        

       
     
        
	</script>
	

    @yield('page_wise_scripts') 
    
    <script src="https://media.twiliocdn.com/sdk/js/conversations/v2.1/twilio-conversations.min.js"></script>
    {{ Html::script('front/js/twiliochat-shubham.js') }}
    {{ Html::script('front/js/agora/ui.js') }}
    {{ Html::script('front/js/twillio/twilio-video.min.js') }}

    


<script src="https://sdk.twilio.com/js/video/releases/2.22.1/twilio-video.min.js"></script>
<!-- <script src="//sdk.twilio.com/js/video/releases/2.17.1/twilio-video.min.js"></script> -->



    
    
@if(Auth::check())
<!-- 
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.11.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.11.0/firebase-analytics.js"; -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
<script>
    

function getUserMedia()
{

  navigator.permissions.query({name: 'microphone'})
  .then((permissionObj) => {
   console.log(permissionObj.state);
   if(permissionObj.state=='denied')
   {
                     
        swal({
          //   title: "Good job!",
            text: "Cannot detect your microphone please check the device connection and permisson settings",
            icon: "warning",
            button: "Okay",
            dangerMode: true,
        });
        
    }
  })
  .catch((error) => {
   console.log('Got error :', error);
  })
 
  navigator.permissions.query({name: 'camera'})
  .then((permissionObj) => {
    if(permissionObj.state=='denied')
    {
                     
        swal({
            //   title: "Good job!",
            text: "Cannot detect your camera please check the device connection and permisson settings",
            icon: "warning",
            button: "Okay",
            dangerMode: true,
        });
        

     
     }
  })
  .catch((error) => {
   console.log('Got error :', error);
  })

  
  navigator.permissions.query({name: 'notifications'})
  .then((permissionObj) => {
    if(permissionObj.state=='denied')
    {
                
        swal({
        //   title: "Good job!",
        text: "Cannot detect your notification permission settings!",
        icon: "warning",
        button: "Okay",
        dangerMode: true,
        });
         
    }
  })
  .catch((error) => {
   console.log('Got error :', error);
  })

  // navigator.permissions.query({ name: 'notifications' })
}

// function permissionAudioVideo(){
// navigator.mediaDevices.getUserMedia( { audio: true, video: true } )
//    .then( ( stream ) => {   
//    },
//    e => {
//     swal({
//         //   title: "Good job!",
//         text: "Cannot detect your microphone/video permission !",
//         icon: "warning",
//         button: "Okay",
//         dangerMode: true,
//         });
         

//    } );
// }


getUserMedia()
</script>

@php 
    $currentuser = Auth::user();
    $fcm_token = $currentuser->device_id;
@endphp

<input value="{{$fcm_token}}" type="hidden" id="fcm_token"></input>
@endif

<script type="module">
            
            $("#CreateCallRoom-form").submit(function(e) {
               e.preventDefault();
            });

           firebase.initializeApp({
                apiKey: "{{env('FIREBASE_APIKEY')}}",
                authDomain: "{{env('FIREBASE_AUTHDOMAIN')}}",
                projectId: "{{env('FIREBASE_PROJECTID')}}",
                storageBucket: "{{env('FIREBASE_STORAGEBUCKET')}}",
                messagingSenderId: "{{env('FIREBASE_MESSAGINGSENDERID')}}",
                appId: "{{env('FIREBASE_APPID')}}",
                measurementId: "{{env('FIREBASE_MEASUREMENTID')}}"
           });
           const messaging = firebase.messaging();
           function startFCM() {
                   messaging
                       .requestPermission()
                       .then(function () {
                           return messaging.getToken()
                       })
                       .then(function (response) {
                       console.log('FCM token',response);
                       CurrentFCM = response;
                       tokensave(response)
                      // clientFn.setPushRegistrationId('fcm', CurrentFCM);
   
                       }).catch(function (error) {
                        swal({
                        //   title: "Good job!",
                        text: "Cannot detect your notification permission settings!",
                        icon: "warning",
                        button: "Okay",
                        dangerMode: true,
                        });
                           // alert(error);
                           console.log(error);
                       });
               }
           startFCM();
   

           function tokensave(response){
               var url = baseurl+'/store-token-web';
               var data={
                   device_id :response,
                   device_type :3
               }
               var methodType='POST';
               $.ajaxSetup({
                   headers: {
                   'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                   }
               });
               // e.preventDefault();
               var formData = data;
               var type = methodType;
               var ajaxurl = url;
               $.ajax({
                   type: type,
                   url: ajaxurl,
                   data: formData,
                   dataType: 'json',
                   success: function (data) {
                       console.log('sucess',data);
                   },
                   error: function (data) {
   
                   }
               });
           }
   

           var isIncomingCall;
           messaging.onMessage(function (payload) {
            // I~V~local_10_local_11
            // I~A~local_10_local_11

            // 11->10
            // 10->11 case mai
               const title = payload.notification.title;
               const options = {
                   body: payload.notification.body,
                   icon: payload.notification.icon,
               };
               new Notification(title, options);
              
             
            //    callPopupOpen(payload)
           });


function callPopupOpen(payload)
{
        var EVENT_CALL_DISCONNECTED = "disconnected";
        var EVENT_CALL_CONNECTED = "connected";
        var EVENT_ROOM_CREATED = "room-created";
        var EVENT_DECLINED = "call_declined";
        const obj_json = JSON.parse(payload['data']['payload']);
        var loggedIncaller=chatenv+'_'+loggedInId;
        var callId= obj_json.callId
        var isIncomingCall=true;


       

        if((obj_json.callBackEvent==EVENT_ROOM_CREATED || obj_json.callBackEvent==EVENT_CALL_CONNECTED) && obj_json.type==1
        && obj_json.callId && obj_json.caller_userId != loggedIncaller)
        {


        if (obj_json.callId != $('#room_name_final').val() && $('#room_name_final').val().length > 0) {
        console.log('inside busy api room name')
        var data={
        callId:$('#room_name_final').val(),
        userId:loggedInId
        };

        // sendBusyNotification(data);
        return false
        }

        $('#video_call_room_name').val(obj_json.callId)
       
        
        $('#chat_user_name').html(obj_json.callerName);
        $('#chat_user_profile_pic').attr('src',obj_json.callerPic)
        var callerName="";
        if(obj_json.callerName){
        callerName=obj_json.callerName
        }
        var callerpic=baseurl+'/front/images/placeholder-user.png';
        if(obj_json.callerPic)
        {
            callerpic=obj_json.callerPic
        }

        if(!isCaller)
        {
            // audioRingplay();
        swal({
        title:'Call from '+callerName,
        icon :callerpic,
        className:"call-modal",
        buttons: [
        'Decline',
        'Accept'
        ],
        // customClass: ".video-call",
        dangerMode: true,
        }).then(function(isConfirm) {
        if (isConfirm) {

        console.log('triger')
        $('#messages-part-windows').addClass('hide');
        $('#videoCallMessage').removeClass('hide')
        // videoCallFunction();
        call_intite();
        // audioRingpause()
        //$(".audio_video_call_intiate").trigger("click");

        }else{
        var data={
        callId:obj_json.callId,
        userId:loggedInId
        };
        // audioRingpause()
        // sendBusyNotification(data);
        // location.reload();
        }

        });
        }

    }

    // { callId: "I~V~qa_98~qa_99", reason: "", user_id: 99, callBackEvent: "call_declined" }
    if(obj_json.type==2 && obj_json.callBackEvent==EVENT_CALL_DISCONNECTED && obj_json.callBackEvent==EVENT_DECLINED)
    {
    if(obj_json.user_id!=loggedInId)
    {
    $('#video_call_room_name').val(obj_json.callId)
    swal({
    title:'Call disconnect',
    icon:'warning',
    buttons: [
    'Cancel',
    'Call back'
    ],
    // customClass: ".video-call",
    dangerMode: true,
    }).then(function(isConfirm) {
    if (isConfirm) {

    $('#messages-part-windows').addClass('hide');
    $('#videoCallMessage').removeClass('hide')
    // videoCallFunction();
    call_intite();
    // $(".audio_video_call_intiate").trigger("click");

    }else{
    var data={
    userId:loggedInId,
    callId:obj_json.callId
    }
    callDisconnect()
    // sendBusyNotification(data);
    // location.reload();

    }
    })
    }

    }
}


        
   
           function sendBusyNotification(data)
            {
                console.log('Busy api call');
                // var url = baseurl+'/send-busy-notification';
                // var methodType='POST';
                // $.ajaxSetup({
                //     headers: {
                //     'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                //     }
                // });
                // // e.preventDefault();
                // var formData = data;
                // var type = methodType;
                // var ajaxurl = url;
                // $.ajax({
                //     type: type,
                //     url: ajaxurl,
                //     data: formData,
                //     dataType: 'json',
                //     success: function (data) {
                //         // location.reload();
             
                //     },
                //     error: function (data) {
    
    
                //     }
                // });
            }
        
        
            // window.onbeforeunload = function() {
            //     if (isCallStart)
            //     {
            //         var message = "Call in processing are you sure you want to disconnect call?";
            //         if (confirm(message)) return true;
            //         else return false;
            //     }
            // }
          
   
   </script>

    {{ Html::script('front/js/twillio/twillio-video.js') }} 
    





    

 
</head>
    <div class="ajax-load text-center" style="display:none">
        <p><img src=" {{ asset('front/images/loader-infinty.svg') }}"></p>
    </div>
    
@include('frontend.dashboard.likes-modal')
@include('frontend.message.call')

<div  class="loader-cover hide" id="agora_loader" > 
           <div class="loading hide " id=""></div>
</div>
@include('frontend.common.delete-modal')
@include('frontend.layouts.footer')


<script>
    
$('.btn-block').click(function(){
    //Some code
    $('.clockpicker-popover').css('display', 'none!important');
});

    //   $(document).ready(function(){
	// 		$("#call_modal_popup").modal("show");
	// 	});
    
    
</script>
  

</html>
