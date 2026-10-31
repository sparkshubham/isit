

var timerHtml='<span id="time">00:00</span>'

var cData;
var roomFn;
var partcipantList=[]
var url = baseurl + '/ring/new_phone_ring.mp3';
var audio = new Audio(url); 
// audio["ring"] = new Audio(); 
// audio["ring"].src = url
// audio["ring"].loop = true;
function getUserData(convo_id){
  conversation = clientFn.getConversationBySid(convo_id);
  conversation.then(function(value){console.log(getParticipantGroup(value));},function(error) {console.log(error);});
}
var promisesCh = [];
function getParticipantGroup(value) {
  promisesCh = [];
return new Promise((resolve, reject) => {
  value.getParticipants().then(participant => {
   
      $.each(participant,function (index, participantUser){
        participantUser.getUser().then(
          user => {
            identity=user.identity;
            fullName=user.friendlyName;
            profile_pic =getProfileImage(user.attributes.USER_PROFILE_PIC);   
            promisesCh[identity]= {fullName,profile_pic};
          }
        );
      
      })
    },
    error => {
      consolelog(error);
      reject(e);
    }

  )

});

}

function getProfileImage(value) {
  var imgUrl=baseurl+"/front/images/placeholder-user.png";
  if(value){
      return value;
  }else{
      return imgUrl;
  }
  
}


function getParticipantSingleCall() {
  promisesCh = [];
  var user_name = $('#chat_user_name').html();
  var url = getProfileImage($('#chat_user_profile_pic').attr('src'));
  promisesCh['identity']= {'fullName':user_name,'profile_pic':url};
}

function audioRingpause() {
  audio.pause();
  audio.currentTime = 0;
}


function audioRingplay() {
  audio.loop = true;
  audio.play();
}



function videoCallUserimage()
{
  
var htmlVal = '<img  src="'+promisesCh.identity.profile_pic+'"/><span >'+promisesCh.identity.fullName+'</span>';
// var htmlVal = "<img src='" + url + "'/><span >" + user_name + "</span>"
$('#no-local-video').html(htmlVal)
}

var isVideo = true;

function call_intite(type='V'){
  // permissionAudioVideo()
  $('.modal').modal('hide');
  isCaller=true
  isCallStart=true
  $('#local-media').html('');
  $('.video_Call_btn').addClass('hide');
  var room_name = $('#video_call_room_name').val();
  // var room_name = $('#room_name').val();
  var values = room_name.split('~');
  if(type=='A'){
    room_name = $('#voice_call_room_name').val();
    values = room_name.split('~');

  }
  $('#room_name_final').val(room_name);
  $('#local-media').removeClass('hide')
  if (values[1] == 'A') {
    isVideo = false;
    $('#local-media').addClass('hide')
    isGroupVideo = false;
    $('.local_media_wth_btn').addClass('audio-call')
    $('.local_media_wth_btn').removeClass('video-call-small-screen')
  }

  if (values[0] == 'G') {
     isGroupVideo = true;
     getUserData(values[2]);
  }

  if (values[1] == 'V') {
   isGroupVideo = true;
    $('.local_media_wth_btn').addClass('video-call-small-screen')
 }
    $('.twillio_video_call').addClass('hide');
    $('#videoCallMessage').removeClass('hide')
     
  if (values[0] == 'I') {
    isGroupVideo = false;
    getParticipantSingleCall();
 }
 if(isVideo){
  getParticipantSingleCall();
  videoCallUserimage();

}


// audioRingplay();

  var tw_token = $('#token_token').val();

  Twilio.Video.connect(tw_token, {
    name: room_name,
    //  tracks: localTracks,
    video: isVideo,
    audio: true,
  })
    .then(function (room) {
      roomFn = room;  
      room.participants.forEach(participant => {
        participant.tracks.forEach(publication => {
          console.log('47',publication.track);
          if (publication.track) {
            $('#remote-media-div-2').addClass('hide')
            if (!isVideo) {
                 remoteMediaDivHide()
            }
           
            dynamicAppendVideoTagDiv(publication.track,publication)
            toggleVisibility("#no-local-video2", false);
            toggleVisibility("#no-local-video", false);

          

          }
          publication.on('unsubscribed', (track) => {
            console.log('unsubscribed',track);
        //    alert('unsubscribed')
            /* Hide the associated <video> element and show an avatar image. */
            
          });
          publication.on('subscribed', (track) => {
            
            callTimer();
            console.log('subscribed',track.kind);
            track.on('enabled', (dat) => {
              if (track.kind == "video") {
                videoHandler(track.sid,true);
                
              
              }
              else{
                audioHandler();
              }
            });
            track.on('disabled', (dat) => {
              if (track.kind == "video") {
                videoHandler(track.sid,false);
              }
              else{
                audioHandler();
              }
            });
            /* Hide the avatar image and show the associated <video> element. */
          });

        });
        participant.on('trackSubscribed', track => {
          $('#remote-media-div-2').addClass('hide')
          partcipantList.push(track)
          if (!isVideo) {
            remoteMediaDivHide()
          }

          console.log('3', track.kind)
          dynamicAppendVideoTagDiv(track,participant)
          toggleVisibility("#no-local-video2", false);
          toggleVisibility("#no-local-video", false);
        });
      });

      // Attach the Participant's Media to a <div> element.


      ///whene room partcipant connected add start
      room.on('participantConnected', participant => {
        // audioRingpause();
        callTimer();
        participant.tracks.forEach(publication => {
          console.log(`Participant "${participant.identity}" has connected to the Room`);
          if (publication.isSubscribed) {
            const track = publication.track;
            $('#remote-media-div-2').addClass('hide')
            if (!isVideo) {
              remoteMediaDivHide()
            }
            dynamicAppendVideoTagDiv(track,publication)
            publication.track.on('disabled', () => {
              track.disable()
            });

            toggleVisibility("#no-local-video2", false);
            toggleVisibility("#no-local-video", false);
           
          }
        });

      
        participant.on('trackUnsubscribed', trackUnsubscribed);
        // inside the track or subscribe track
        participant.on('trackSubscribed', track => {
          // audioRingpause();
          partcipantList.push(track)
          dynamicAppendVideoTagDiv(track,participant)
          $('#remote-media-div-2').addClass('hide')
          if (!isVideo) {
            remoteMediaDivHide()
          }

          toggleVisibility("#no-local-video", false);
          track.on('enabled', () => {
            if (track.kind == "video") {
              if (track.isEnabled) {
                // track.detach();
                videoHandler(track.sid,true);
              } else {
                // track.attach();
                videoHandler(track.sid,false);
              }
                
            }
            else{
              audioHandler();
            }
          
          });
          track.on('disabled', () => {
            if (track.kind == "video") {
              if (track.isEnabled) {
                // track.detach();
                videoHandler(track.sid,true);
              } else {
                // track.attach();
                videoHandler(track.sid,false);
                
              }
            } else if (track.kind == "audio") {
              audioHandler();
              if (track.isEnabled) {
                track.detach();
              } else {
                track.attach();
              }
            }

          });
        });

      });


      room.on('participantDisconnected',participantDisconnected);

     

      ///whene room partcipant connected add end


      $('#remote-mic-btn').on('click', () => {
        room.localParticipant.audioTracks.forEach(track => {
          track = track.track;
          if (track.kind == "audio") {
            if (track.isEnabled) {
              track.disable();
              $("#remote-mic-icon").removeClass('fa-microphone').addClass('fa-microphone-slash');
            } else {
              track.enable();
              $("#remote-mic-icon").removeClass('fa-microphone-slash').addClass('fa-microphone');

            }
          }
        });
      });

      // user remote screen is there
      $('#remote-video-btn').on('click', () => {
        room.localParticipant.videoTracks.forEach(track => {
          track = track.track;
          if (track.kind == "video") {
            if (track.isEnabled) {
              track.disable();
              $("#remote-video-icon").removeClass('fa-video').addClass('fa-video-slash');
              $('#no-local-video2').attr("style", "display:flex");
              $('#no-local-video2').html('Turned off camera.');
            } else {
              track.enable();
              $('#no-local-video2').attr("style", "display:none");
              $("#remote-video-icon").removeClass('fa-video-slash').addClass('fa-video');

            }
          }
        });
      });

      $('#remote-exit-btn').on('click', () => {
        swal({
          title: 'Are you sure you want to disconnect call.',
          // text: result,
          icon: "warning",
          buttons: [
            'No, cancel it!',
            'Yes, I am sure!'
          ],
          dangerMode: true,
        }).then(function (isConfirm) {
          if (isConfirm) {
            isCaller=false;
            
                   
            roomFn.localParticipant.videoTracks.forEach((trackPub) => {
              trackPub.track.stop();
              const attachedElements = trackPub.track.detach();
              attachedElements.forEach(element => element.remove());              
              trackPub.unpublish();
             
            });
            roomFn.localParticipant.tracks.forEach((trackPub) => {
              trackPub.track.stop();
              const attachedElements = trackPub.track.detach();
              attachedElements.forEach(element => element.remove());              
              trackPub.unpublish();
             
            });



            room.disconnect();
            // $('.video-cols-section').html('');
            $('#messages-part-windows').removeClass('hide');
            $('.twillio_video_call').removeClass('hide');
            $('#videoCallMessage').addClass('hide')
            var data = {
              callId: room_name,
              userId: loggedInId
            };
            // sendBusyNotification(data)
          //  if(!isGroupVideo)
          //  {
            location.reload();
          // }

          }
        });
      });
      function callDisconnect()
      {
        room.disconnect();
      }


      // To disconnect from a Room

      room.on('disconnected', room => {
        console.log('Participant disconnet:' + room.identity);
        // Detach the local media element
        // audioRingpause();
          room.localParticipant.tracks.forEach(publication => {
            const attachedElements = publication.track.detach();
            attachedElements.forEach(element => element.remove());
        });


        
          
      });


      window.addEventListener('beforeunload', () => {
        room.disconnect();
      });
      


    }).catch(error=>{
      let eStr = JSON.stringify(error);
      console.log('shubham',eStr)
    });


    function handleTrackDisabled(track) {
      console.log('track',track);
      track.on('disabled', () => {
        /* Hide the associated <video> element and show an avatar image. */
      });
    }

    function handleTrackEnabled(track) {
      track.on('enabled', () => {
        /* Hide the avatar image and show the associated <video> element. */
      });
    }
    


  if (isVideo) {

    Twilio.Video.createLocalVideoTrack().then(track => {
      $('.video_Call_btn').removeClass('hide');
      $('#local-media').html('')
      const localMediaContainer = document.getElementById('local-media');
      localMediaContainer.appendChild(track.attach());
      $('#local-media').append(timerHtml)
    }).catch(error=>{
      let eStr = JSON.stringify(error);
      console.log('localVideo',eStr)
    });;
    var user_name = $('#chat_user_name').html();
    var url = $('#chat_user_profile_pic').attr('src');
    var htmlVal = '<img id="remote_user_profile_pic" src="' + url + '"/><span >Calling to ' + user_name + '</span>';
    $('#remote-media-div-2').html(htmlVal)
    $('.local_media_wth_btn').removeClass('audio-call')
    $('.local_media_wth_btn').addClass('video-call-small-screen')
  } else {
    $('.local_media_wth_btn').addClass('audio-call')
    $('.local_media_wth_btn').removeClass('video-call-small-screen')
    Twilio.Video.createLocalAudioTrack().then(track => {
      $('.video_Call_btn').addClass('hide');
      const localMediaContainer = document.getElementById('local-media');
      localMediaContainer.appendChild(track.attach());
    }).catch(error=>{
      let eStr = JSON.stringify(error);
      console.log('localAudo',eStr)
    });
    var user_name = $('#chat_user_name').html();
    var url = $('#chat_user_profile_pic').attr('src');
    var htmlVal = "<img src='" + url + "'/><span >Calling to " + user_name + "</span>"
    $('#no-local-video').html(htmlVal)
    remoteMediaDivHide()
    $('#remote-media-div-2').addClass('hide')
  }



 function videoHandler(sid,isHide){
  if(isHide){
    $('#track_video_'+sid).find('video').removeClass('hide');
    $('#track_video_'+sid).find('.remote-media-div-2').addClass('hide');
  }
  else{
    $('#track_video_'+sid).find('video').addClass('hide');
    $('#track_video_'+sid).find('.remote-media-div-2').removeClass('hide');  
  }
 }

 function audioHandler(){
  var htmlVal = '<img  src="'+promisesCh.identity.profile_pic+'"/><span >'+promisesCh.identity.fullName+'</span>';
  // var htmlVal = "<img src='" + url + "'/><span >" + user_name + "</span>"
  $('#no-local-video').html(timerHtml+htmlVal)
 }

 function participantDisconnected(participant) {
  isCaller=false;
  isCallStart=false;

  console.log('participantDisconnected participant',participant);

  console.log('roomFn.participants',roomFn.participants);

;
// audioRingpause();

  if(!isGroupVideo){
    console.log('participant333',participant);
     
    try {

      $('#track_video2_'+participant.identity).parents('.text-center').remove();
      participant.tracks.forEach((publication) => {
        console.log('publication',publication);
        const remotePublication = publication;
        // if (remotePublication.isSubscribed) {
          const { track } = remotePublication;
          if (track) trackUnsubscribed(track);
     //   }
      });


     roomFn.disconnect();
      
    } catch (error) {
      console.log(error);
    }
    roomFn.localParticipant.tracks.forEach((trackPub) => {
      trackPub.unpublish();
      trackPub.track.stop();
    });
 ;
   // document.getElementById('track_video_'+roomFn.sid).remove();
    // $('.video-cols-section').html('');
    $('.twillio_video_call').removeClass('hide');
    $('#messages-part-windows').removeClass('hide');
    $('#videoCallMessage').addClass('hide');
    location.reload();
  }
  else{
    console.log('participant222 local vide',participant);
    $('#track_video2_'+participant.identity).parents('.text-center').remove();
    participant.tracks.forEach((publication) => {
      console.log('publication',publication);
      const remotePublication = publication;
      // if (remotePublication.isSubscribed) {
        const { track } = remotePublication;
        if (track) trackUnsubscribed(track);
   //   }
    });
    
    // alert("helo");
    // if(roomFn.participants.size == 0){

    //   roomFn.disconnect();
    //   roomFn.localParticipant.tracks.forEach((trackPub) => {
    //     trackPub.unpublish();
    //     trackPub.track.stop();
    //   });
  
    // // document.getElementById('track_video_'+roomFn.sid).remove();
    //   // $('.video-cols-section').html('');
    //   $('.twillio_video_call').removeClass('hide');
    //   $('#messages-part-windows').removeClass('hide');
    //   $('#videoCallMessage').addClass('hide');
    //   location.reload();
    // }

    // roomFn.participants.forEach(user =>{
    //     if()

    // })
    
  }

}

 function trackUnsubscribed(track) {
  console.log('trackUnsubscribed',track);
  partcipantList.pop(track)
 
  track.detach().forEach(element => element.remove());
  // Alternative if Safari crashes when detaching tracks
  // track._attachments!.forEach((detachedElement) => detachedElement.remove());
}


  function toggleVisibility(elementID, visible) {
    if (visible) {
      $(elementID).attr("style", "display:block");
    } else {
      $(elementID).attr("style", "display:none");
    }
  }

  function dynamicAppendVideoTagDiv(track,participant) {
    $('.local_media_wth_btn').removeClass('video-call-small-screen')
    var iDiv = document.createElement('div');
    iDiv.className = 'col-sm-6 text-center';
    iDiv.id = 'track_video_' + track.sid;
    var iDiv2 = document.createElement('div');
    iDiv2.id = 'track_video2_' + participant.identity;
    iDiv2.className = 'remote-media-div-2 hide';
    if(!isGroupVideo){
      var htmlVal = '<img id="remote_user_profile_pic" src="'+promisesCh.identity.profile_pic+'"/><span >'+promisesCh.identity.fullName+'</span>';
      var user_name= '';
    }
    else{
      var userObhj = promisesCh[participant.identity];
      console.log(userObhj,'heloo');
      var htmlVal = '<img id="remote_user_profile_pic" src="'+userObhj.profile_pic+'"/><span >'+userObhj.fullName+'</span>';
      var user_name= '<span class="user_name_group">'+userObhj.fullName+'</span>'
    }
    iDiv2.innerHTML = htmlVal;
    iDiv.innerHTML = user_name;

    if (track.kind == "audio") {
      document.getElementById('remote-media-div' + loggedInId).appendChild(track.attach())
    } else {
      if (isVideo) {
        document.getElementById('remote-media-div' + loggedInId).appendChild(iDiv).appendChild(track.attach());
        document.getElementById('remote-media-div' + loggedInId).appendChild(iDiv).appendChild(iDiv2);
      }

    }
  }

  

  function blankRemote() {
    $('$remote-media-div').html('')
  }

  
  function remoteMediaDivHide()
  {
    $('#remote-media-div' + loggedInId).addClass('hide')
    audioHandler();
  }


  
function startTimer(duration, display) {
  var timer = duration, minutes, seconds;
  setInterval(function () {
      minutes = parseInt(timer / 60, 10);
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      display.textContent = minutes + ":" + seconds;

      if (++timer < 0) {
          timer = duration;
      }
  }, 1000);
}

var isTimer=true;
function callTimer(){
  if(isTimer)
  {
    isTimer=false
    var fiveMinutes = 60 * 0,
    display =  document.querySelector('#time');
    startTimer(fiveMinutes, display);
  }
};



};




