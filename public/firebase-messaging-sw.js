importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');




// 
var payloadFn;
var firebaseConfig = {
    apiKey: "{{env('FIREBASE_APIKEY')}}",
                authDomain: "{{env('FIREBASE_AUTHDOMAIN')}}",
                projectId: "{{env('FIREBASE_PROJECTID')}}",
                storageBucket: "{{env('FIREBASE_STORAGEBUCKET')}}",
                messagingSenderId: "{{env('FIREBASE_MESSAGINGSENDERID')}}",
                appId: "{{env('FIREBASE_APPID')}}",
                measurementId: "{{env('FIREBASE_MEASUREMENTID')}}"
  };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        // firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
       
// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// [START background_handler]
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: './firebase-logo.png'
  };
  return self.registration.showNotification(notificationTitle,
    notificationOptions);
});

// messaging.onBackgroundMessage((payload) => {
//        payloadFn=payload;
//     //    console.log(self.client,'shubham heree')
//        self.document.getElementById('id_video_audio_call').value='ss';
//     //    alert("akgorund")

//     });


    messaging.onBackgroundMessage(function(payload) {
        console.log('Payload is there firbase background' ,payload)
       
        // const obj_json = JSON.parse(payload['data']['payload']);
        // var loggedIncaller=chatenv+'_'+loggedInId;
            const notificationTitle = payload.data.title;
            const notificationOptions = {
                body: payload.data.message,
                icon:'PATH TO ICON IF ANY',
                data: { url:payload.data.onClick }, //the url which we gonna use later
            };
            return self.registration.showNotification(notificationTitle,notificationOptions);
        });
        //Code for adding event on click of notification
        self.addEventListener('notificationclick', function(event) {
        let url = event.notification.data.url;
        event.notification.close(); 
        event.waitUntil(
            clients.matchAll({type: 'window'}).then( windowClients => {
            // Check if there is already a window/tab open with the target URL
            for (var i = 0; i < windowClients.length; i++) {
            var client = windowClients[i];
            // If so, just focus it.
            if (client.url === url && 'focus' in client) {
                return client.focus();
            }
        }
        // If not, then open the target URL in a new window/tab.
        if (clients.openWindow) {
             return clients.openWindow(url);
        }
        })
        );
        });

                    