
  const Video = Twilio.Video;

function call_intite() {

  var tw_token = $('#token_token').val();

  var room_name = $('#room_name').val();

 Video.connect(tw_token, {
    name: room_name,
    //  tracks: localTracks,
    video: isVideo,
    audio: true,
  }).then(room => {

    console.log(`Connected to Room: ${room.name}`);
    // Log your Client's LocalParticipant in the Room
    const localParticipant = room.localParticipant;
    console.log(`Connected to the Room as LocalParticipant "${localParticipant.identity}"`);

    // Log any Participants already connected to the Room
    room.participants.forEach(participant => {
      console.log(`Participant "${participant.identity}" is connected to the Room`);
      document.getElementById('remote-media-div').appendChild(participant.attach());
    });

    // Log new Participants as they connect to the Room
    room.once('participantConnected', participant => {
      console.log(`Participant "${participant.identity}" has connected to the Room`);
      
      document.getElementById('remote-media-div').appendChild(participant.attach());
    });

    // Log Participants as they disconnect from the Room
    room.once('participantDisconnected', participant => {
      console.log(`Participant "${participant.identity}" has disconnected from the Room`);
      
      document.getElementById('remote-media-div').appendChild(participant.attach());
    });


    // Attach the Participant's Media to a <div> element.
    room.on('participantConnected', participant => {
      console.log(`Participant "${participant.identity}" connected`);

      participant.tracks.forEach(publication => {
        if (publication.isSubscribed) {
          const track = publication.track;
          document.getElementById('remote-media-div').appendChild(track.attach());
        }
      });

      participant.on('trackSubscribed', track => {
        document.getElementById('remote-media-div').appendChild(track.attach());
      });
    });


    room.localParticipant.audioTracks.forEach(publication => {
      publication.track.disable();
    });

    room.localParticipant.videoTracks.forEach(publication => {
      publication.track.disable();
    });

    room.localParticipant.videoTracks.forEach(publication => {
      publication.track.stop();
      publication.unpublish();
    });
    

    room.participants.forEach(participant => {
      participant.tracks.forEach(publication => {
         publication.on('unsubscribed', () => {
           /* Hide the associated <video> element and show an avatar image. */
         });
      });
    });
    

    room.localParticipant.audioTracks.forEach(publication => {
      publication.track.enable();
    });
    
    room.localParticipant.videoTracks.forEach(publication => {
      publication.track.enable();
    });

    room.on('disconnected', room => {
      // Detach the local media elements
      room.localParticipant.tracks.forEach(publication => {
        const attachedElements = publication.track.detach();
        attachedElements.forEach(element => element.remove());
      });
    });
    
    // To disconnect from a Room
    // room.disconnect();
    
    
// room.participants.forEach(participant => {
//   participant.tracks.forEach(publication => {
//     if (publication.isSubscribed) {
//       handleTrackDisabled(publication.track);
//     }
//     publication.on('subscribed', handleTrackDisabled);
//   });
// });


// room.participants.forEach(participant => {
//   participant.tracks.forEach(publication => {
//     if (publication.isSubscribed) {
//       handleTrackEnabled(publication.track);
//     }
//     publication.on('subscribed', handleTrackEnabled);
//   });
// });



    
Video.createLocalVideoTrack().then(localVideoTrack => {
  return room.localParticipant.publishTrack(localVideoTrack);
}).then(publication => {
  console.log('Successfully unmuted your video:', publication);
});




  });


  



}

function handleTrackDisabled(track) {
  track.on('disabled', () => {
    /* Hide the associated <video> element and show an avatar image. */
  });
}


function handleTrackEnabled(track) {
  track.on('enabled', () => {
    /* Hide the avatar image and show the associated <video> element. */
  });
}





Video.createLocalVideoTrack().then(track => {
  const localMediaContainer = document.getElementById('local-media');
  localMediaContainer.appendChild(track.attach());
});
