/*

    uniqueName of conversation will be in pattern
    user_identity1_user_identity2 where uder identity will be sorted in acsending order
    i.e. if users with id 1 and 3 are chatting then 
    uniquename of conversation will be 1_3
*/

var clientFn;
token = $('#token_token').val();
identity = $('#twillio_identity').val();
friendlyName = $('#friendlyName').val();
env=$('#env').val();
current_conversation_id = '';
current_conversation = '';
new_conversation_created = 0;
start_new_conversation_with_user = '';
current_conversation_list_count = 0;
converstaionType=1;
unread_messages = 0;
var obj={};

var participant_count;


new Twilio.Conversations.Client.create(token).then(chatClient => {
    const client  = chatClient;
    clientFn=chatClient;
    
$(document).ready(function(){
    listOfConversations();
});

// clientFn.setPushRegistrationId('fcm', CurrentFCM);
function openSideChatOnPagerefresh(id)
{
    
    
li_obj =$('.unique_'+id)

current_conversation_id = li_obj.find('.name').attr('data-sid');
var chatwindowname = li_obj.find('.name').attr('data-attributes-name');
var imgeUrl = li_obj.find('.name').attr('data-attributes-img');
var groupName = li_obj.find('.name').attr('data-group-info');

$('#chat_user_name').html(chatwindowname)
$('#chat_user_profile_pic').attr('src',imgeUrl)
$('#home').addClass('show');
$('#home').addClass('active');
$('#welcome').addClass('hide');
$('#welcome').removeClass('active');



 
// $(chat_user_name


new_conversation_created = 0;
start_new_conversation_with_user = '';
openChatDetail(current_conversation_id);

}

var path = window.location.pathname
var final = path.substr(path.lastIndexOf('/') + 1);
if(final=='message')
{
    var s = window.location.search
    if(s){
        
       var unique_name= s.substr(s.lastIndexOf('?') + 1);
        setTimeout(function() {
            openSideChatOnPagerefresh(unique_name);
        }, 2500);

    }
}

// handle new message event
client.on('tokenExpired',function(tokenExpired){
    console.log('tokenExpired');
    console.log(tokenExpired);
});
// handle new message event
client.on('messageAdded',function(messageAdded){
    console.log('messageAdded');
    updateNewMessage(messageAdded);
});
client.on('pushNotification',function(pushNotification)
{
        // console.log(pushNotification);
    // if new conversation is initiated by someone else
    if(conversationAdded.createdBy !== identity)
    {
        listOfConversations();
    }
});
client.on('conversationRemoved',function(conversationRemoved){
    // console.log(conversationRemoved);
    conversation_id = conversationRemoved.sid;
    if(conversation_id == current_conversation_id)
    {
        $('#chat_body').html('');
        $('#chat_panel').addClass('d-none');
    }
    listOfConversations();
});

$(document).on('click','#start-new-conversation',start_new_conversation);
// $(document).on('click','#delete-conversation',delete_conversation);



$(document).on('click','#delete_exit_btn_group,#exit_btn_group',function(){
    delete_convo_id = $(this).attr('data-convo-id');
    var type = $(this).attr('data-type');
    var identity = $(this).attr('identity');;  
    if(type==0)
    {
        swal({
            title:'Are you sure you want to exit this group?',
            // text: result,
            icon: "warning",
            buttons: [
              'No, cancel it!',
              'Yes, I am sure!'
            ],
            dangerMode: true,
          }).then(function(isConfirm) {
            if (isConfirm) {
                remove_particpants(delete_convo_id,identity,true)
            }else{      
               return false;
            }
          });
       
    }else{
        swal({
            title:'Are you sure you want to delete this?',
            // text: result,
            icon: "warning",
            buttons: [
              'No, cancel it!',
              'Yes, I am sure!'
            ],
            dangerMode: true,
          }).then(function(isConfirm) {
            if (isConfirm) {
                delete_conversation(delete_convo_id);
            }else{      
               return false;
            }
          });
    }

});



$(document).on('click','.remove_person_to_group',function(){
    var id=$(this).attr('data_id');
    convoId=$(this).attr('data-convo-id');
    swal({
        title:'Are you sure you want to remove this user?',
        // text: result,
        icon: "warning",
        buttons: [
          'No, cancel it!',
          'Yes, I am sure!'
        ],
        dangerMode: true,
      }).then(function(isConfirm) {
        if (isConfirm) {
            $('#'+id).remove()
            participant_count =participant_count-1
            $('#participant_count').html(participant_count+' Participants')
            remove_particpants(convoId,id)
        }else{      
           return false;
        }
      });
})


function remove_particpants(conversation_id,identity,flagReload=false)
{
 
    $('#'+identity).remove()
    var result = identity.split('_');
    $("#appendBlankparticpant option[value="+result[1]+"]").remove();
    client.getConversationBySid(conversation_id)
    .then(
            function (particpant) 
            {
                particpant.removeParticipant(identity)
                .then(
                        function(particpant_success)
                        {
                          
                         
                          
                            if(flagReload)
                            {
                                var url = baseurl+'/message';
                                window.location.href=url;
                            }
                            // $('#'+identity).addClass('hide')
                            // /$('#CreateChatRoom').modal('hide')
                            // location.reload();
                        },
                        function (error) 
                        {
                            console.log(error);
                        }
                    );
            },
            function (error) 
            {
                console.log(error);
            }
        );
}

$(document).on('click','#chat_list li',function() 
// $(document).on('click','#chat_list_liCH3ca353013168460da6623f5e127fd1a9',function() 
{
    li_obj = $(this);
   
    current_conversation_id = li_obj.find('.name').attr('data-sid');
    var chatwindowname = li_obj.find('.name').attr('data-attributes-name');
    var imgeUrl = li_obj.find('.name').attr('data-attributes-img');
    var groupName = li_obj.find('.name').attr('data-group-info');
    $('#chat_user_name').html(chatwindowname)
    $('#chat_user_profile_pic').attr('src',imgeUrl)
    // $(chat_user_name
    new_conversation_created = 0;
    start_new_conversation_with_user = '';
    openChatDetail(current_conversation_id);
});



// $(document).on('click','#chat_list li',function() 
// // $(document).on('click','#chat_list_liCH3ca353013168460da6623f5e127fd1a9',function() 
// {
//     li_obj = $(this);
//     current_conversation_id = li_obj.find('.name').attr('data-sid');
//     var chatwindowname = li_obj.find('.name').attr('data-attributes-name');
//     var imgeUrl = li_obj.find('.name').attr('data-attributes-img');
//     var groupName = li_obj.find('.name').attr('data-group-info');
//     $('#chat_user_name').html(chatwindowname)
//     $('#chat_user_profile_pic').attr('src',imgeUrl)
//     // $(chat_user_name
//     new_conversation_created = 0;
//     start_new_conversation_with_user = '';
//     openChatDetail(current_conversation_id);
// });



$(document).on('click','.sharePostbtn',function() {
 

    var sid = $(this).attr('data-sid');
    var new_message =$('#link').val();
    $('#sharePostbtn_'+sid).text('Shared');
    $('#sharePostbtn_'+sid).prop('disabled', true);
    sendMessageSharePost(sid,new_message)
})

var convoId;

function getConversationId(obj){
        var myId = loggedInId;
        var mySid = mytwillioSid;
        var otherSid = obj.other_twillio_id;
        var otherUserId = obj.other_user_id;
        var tempId = '';
        if (myId > otherUserId){
            tempId= otherSid+'_'+mySid; 
        }else{ 
            tempId=  mySid+'_'+otherSid;
        }
        convoId = tempId;
        return convoId
}

$('.tc-users').click(function(){ 

        obj.username=$(this).attr('data-tc-fullname');
        obj.other_twillio_id=$(this).attr('data-user-twillio-id');
        obj.other_user_id=$(this).attr('data-tc-user-id');
        obj.profile_pic=$(this).attr('data-tc-profile');
        convoId= getConversationId(obj)
        obj.chat_user_id=env+'_'+obj.other_user_id;
        converstaionType="1"  // if 1  that means 1 to one and 2 means group
        start_new_conversation(converstaionType);
       // createConversation(obj);
});

$("#CreateChatRoom-form").submit(function(e) {
    converstaionType="2"
    if($("#appendBlankparticpant").val().length > 0 && $('#group_Profile_pic').val() &&  $('#ChatRoomName').val() )
    {
        console.log($("#appendBlankparticpant").val().length)
        if($("#appendBlankparticpant").val().length<=4){
            start_new_conversation(converstaionType);
         } 
    }
})





var no_data_svg=baseurl+'/front/images/nodata.svg';
var notFound='<li  class="list-group-item unread nav-item  notFound_conv"  id="notFound_conv" role="presentation"><div class="no_data_found"><img src="'+no_data_svg+'" alt=""><p>No data found</p></div></li>'

var users=[]
$(document).on('click','.add_remove_group',function(){
    var value=$(this).attr('data_id');
    users= $("#appendBlankparticpant").val();
    if($.inArray(value,users)=='-1'){
        users.push(value);
        $("#appendBlankparticpant").append('<option  id="'+value+'" selected="" value="'+value+'"/>')
        $('#svg_add_'+value).addClass('d-none');
        $('#text_change_add_remove_'+value).addClass('btn-primary')
        $('#text_change_add_remove_'+value).removeClass('btn-secondary')
        $('#text_change_add_remove_'+value).text('Remove')
    }else{
        users.pop(value);
        $("#appendBlankparticpant option[value="+value+"]").remove();
        $('#text_change_add_remove_'+value).text('+ Add')
        $('#text_change_add_remove_'+value).addClass('btn-secondary')
        $('#text_change_add_remove_'+value).removeClass('btn-primary')
    }
    var tlds = $("#appendBlankparticpant").val();

});


$(document).on('click','#send-message-user',sendMessage);
$(document).on('click','#send-message-audio',sendMessage);

$("#sendMessage").submit(function(e) {
    e.preventDefault();
});


$('textarea').keyup(function (event) {
    if (event.keyCode == 13 && event.shiftKey) {
        var content = this.value;
        var caret = getCaret(this);
        this.value = content.substring(0,caret)+"\n"+content.substring(carent,content.length-1);
        event.stopPropagation();
        
  }else if(event.keyCode == 13)
  {
    sendMessage()
  }
});


function start_new_conversation(type) 
{

    // 
    user_identity =identity;
    if(user_identity == undefined)
    {
        alert('Please select user');
        return;
    }
    if(type==1){
        var myId = loggedInId;
        var mySid = mytwillioSid;
        var otherSid = obj.other_twillio_id;
        var otherUserId = obj.other_user_id;
        var tempId = '';
        if (myId > otherUserId){
            tempId= otherSid+'_'+mySid; 
        }else{ 
            tempId=  mySid+'_'+otherSid;
        }
        var uniqueName = 'chat_'+tempId;
    }
    if(type==2)
    {

        var d=new Date();
        var ms=d.getTime();
        var uniqueName= "Group_"+ms;
    }
    if($('#group_uniqueName').val())
    {
        uniqueName=$('#group_uniqueName').val()
    }
    // create_new_conversation(uniqueName);
    client.getConversationByUniqueName(uniqueName)
    .then(
            function(conversation_exits)
            {
                current_conversation_id = conversation_exits.sid;
                current_conversation = conversation_exits;
                    // openChatDetail(current_conversation_id);
                    var groupNameArr=uniqueName.split('_');
                    if(groupNameArr[0]=='Group'){
                        updateConversationByUniqueName(current_conversation)
                    }else{
                        var url = baseurl+'/message?'+uniqueName;
                        window.location.href=url;
                    }
                    // $('#user_list_modal').modal('hide');
            },
            function (conversation_doesnot_exits) 
            {
                // switch the user identities and check again for conversation
                // uniqueName = uniqueName.split('_').reverse().join('_');
                uniqueName=uniqueName;
                client.getConversationByUniqueName(uniqueName)
                .then(
                        function(conversation_exits)
                        {
                            current_conversation_id = conversation_exits.sid;
                            current_conversation = conversation_exits;
                            openChatDetail(current_conversation_id);
                            var url = baseurl+'/message?'+uniqueName;
                            window.location.href=url;
                            // $('#user_list_modal').modal('hide');
                        },
                        function (conversation_doesnot_exits)
                        {
                            /*
                                conversation does not exists with both the combination of user identities
                                Create new converstion
                            */
                            create_new_conversation(uniqueName);
                            console.log('create conversation');
                          

                        }
                    );
            }
        );
}


function create_new_conversation(uniqueName)
{
    user_identity = identity;
    user_name =  obj.username;
    var attributes=""
    if(converstaionType==2)
    { 
        $('#modal-content-chat').addClass('loader-bg')
        $('#file_uploader').removeClass('hide');
      
        if($('#group_Profile_pic').val())
        {
            groupProfilePic=$('#group_Profile_pic').val();
        }
        attributes={ 
            GROUP_PROFILE_PIC:groupProfilePic,
            GROUP_CREATOR_ID :loggedInId,
            GROUP_CREATOR_NAME :loggedInUsername,
        }
        friendlyName=$('#ChatRoomName').val();
    }else{
         attributes={ 
            AUTHOR_NAME:obj.username,
            USER_PROFILE_PIC :obj.profile_pic,
        }
        uniqueName=uniqueName
    }


    client.createConversation({'friendlyName':friendlyName,'uniqueName':uniqueName,'attributes':attributes})
    .then(
            function(conversation)
            {
                conversation.updateUniqueName(uniqueName)
                .then(
                        function(conversation)
                        {
                            conversation.join()
                            .then(
                                    function (conversation) 
                                    {
                                        conversation.advanceLastReadMessageIndex(1)
                                        .then(
                                                function(index)
                                                {
                                                    current_conversation_id = conversation.sid;
                                                    current_conversation = conversation;
                                                    start_new_conversation_with_user = obj.chat_user_id;
                                                    new_conversation_created = 1;
                                                    // openChatDetail(current_conversation_id);
                                                    // $('#user_list_modal').modal('hide');
                                                    if(converstaionType==2){
                                                      
                                                        var userPartipant=$("#appendBlankparticpant").val()
                                                        $.each(userPartipant,function(index,value) 
                                                        {
                                                            start_new_conversation_with_user=env+'_'+value;
                                                            conversation.add(start_new_conversation_with_user)
                                                            .then(
                                                                    function(participantResponse)
                                                                    { 
                                                                       var finalLength= userPartipant.length-1
                                                                        if(finalLength==index)
                                                                        {
                                                                            listOfConversations();
                                                                            $('#CreateChatRoom').modal('hide')
                                                                            var url = baseurl+'/message?'+uniqueName;
                                                                            window.location.href=url;
                                                                            $('#modal-content-chat').removeClass('loader-bg')
                                                                            $('#file_uploader').addClass('hide');

                                                                        }
                                                                    }
                                                                ); 
                                                        });
                                                    }
                                                    else{  
                                                        conversation.add(start_new_conversation_with_user)
                                                        .then(
                                                                function(participantResponse)
                                                                {
                                                                    listOfConversations();
                                                                    var url = baseurl+'/message?'+uniqueName;
                                                                    window.location.href=url;
                                                                }
                                                            ); 
                                                        
                                                    }
                                                   
                                                },
                                                function (error) 
                                                {
                                                    console.log(error);
                                                    // conversation.delete();
                                                }
                                            );
                                    }
                                );
                        }
                    );
                
            },
            function (error) 
            {
                console.log(error);
            }
        );
}



function delete_conversation(conversation_id)
{

    client.getConversationBySid(conversation_id)
    .then(
            function (conversation) 
            {
                conversation.delete()
                .then(
                        function(conversation)
                        {
                           
                            $('#CreateChatRoom').modal('hide')
                            listOfConversations();
                            // location.reload();
                            $('#CreateChatRoom').modal('hide')
                            var url = baseurl+'/message';
                            window.location.href=url;
                            if(conversation_id == current_conversation_id)
                            {      
                                // $('#chat_panel').addClass('d-none');
                            }
                        },
                        function (error) 
                        {
                            console.log(error);
                        }
                    );
            },
            function (error) 
            {
                console.log(error);
            }
        );
}



function listOfConversations()
{
    client.getSubscribedConversations()
    .then(
        function(list_of_conversations) 
        {
    
            items = list_of_conversations.items;
            hasNextPage = list_of_conversations.hasNextPage;
            hasPrevPage = list_of_conversations.hasPrevPage;

            if(!hasPrevPage)
            {
                current_conversation_list_count = items.length; 
            }
            else
            {
                current_conversation_list_count += items.length;
            }
          

            if(items.length > 0)
            {
                var promise_conv=[];
    
                $.each(items,function(index,value) 
                {   
                        user_identity = identity; 
                        // makePromiseRun(value) ;     
                          
                        promise_conv.push(getParticipant(value).then(participantss=>{

                        }));
                });    

                
    
                Promise.all(promise_conv)
                    .then(
                        result => 
                        {
                            console.log(result);                
                        }
                    ).catch(error=>{console.log(error)});
            
            }
            else
            {
           
                html = '<li class="list-group-item unread nav-item"  role="presentation">'+
                            '<div class="about">'+
                                '<div class="name" > No Conversations Yet </div>'+
                            '</div>'+
                        '</li>';
                $('#chat_list').append(notFound);

                    
    var no_data_svg=baseurl+'/front/images/nodata.svg';
    var notFound='<li  class="list-group-item unread nav-item  notFound_conv"  id="notFound_conv" role="presentation"><div class="no_data_found"><img src="'+no_data_svg+'" alt=""><p>No data found</p></div></li>'
    
    $('#chat_user_list_share').append(notFound);
                
            }
            
        },
        function(error) 
        {
            console.log(error);
        }
    );
}



function sortObjectByKeys(o) {
    var order= Object.keys(o).sort().reverse().reduce((r, k) => (r[k] = o[k], r), {});
    return order;
}

var count =1;

function last_message_print(value)
{
    return new Promise((resolve,reject)=>
    {
        value.getMessages(1)
        .then( 
                lastMessageGet=>
                {       
                    if(lastMessageGet.items.length > 0){
                            if(lastMessageGet.items[0].type=="media"){                                                        
                                    var typeGet= getfileType(lastMessageGet.items[0].media.contentType);
                                    value['lastMessageFind']=  typeGet.name;
                                    value['lastMsgdateCreated']=lastMessageGet.items[0].dateCreated;// moment().format('HH:mm a');
                            }else{
                                value['lastMessageFind']= lastMessageGet.items[0].body;
                                value['lastMsgdateCreated']= lastMessageGet.items[0].dateCreated;//.format('HH:mm a');
                            }
                            $('#last_msg_sid_'+value.sid).html(value['lastMessageFind'])
                            $('#last_msg_time'+value.sid).html(timeSinceAgo(value['lastMsgdateCreated']))
                        }else{
                            value['lastMessageFind']="";
                            value['lastMsgdateCreated']="";
                            var groupNameArr=value.uniqueName.split('_');
                            var groupInfo='#';
                            $('#last_msg_sid_'+value.sid).html(value['lastMessageFind'])
                            $('#last_msg_time'+value.sid).html(value['lastMsgdateCreated'])
                            var search_parms=window.location.search;
                            if(groupNameArr[0]!='Group' && final =="message" && search_parms=='')
                            {
                              $('.unique_'+value.uniqueName).remove();
                            }
                            if(groupNameArr[0]!='Group' && final =="message" && search_parms!='')
                            {

                                var uniq_url='?'+value.uniqueName
                                if(uniq_url!=search_parms)
                                {
                                  $('.unique_'+value.uniqueName).remove();
                                }
                            }
                        }

                        resolve(lastMessageGet);
                    
                },
                error=>
                {
                    console.log(error);
                    reject(error);
                }
            )
        });
        
}

var count=0;
function getParticipant(value) 
{
    if(undefined == obj.print_user_arr){
        obj.print_user_arr = [];
    }
    return new Promise((resolve,reject)=>
    {
        value.getParticipants().then(
            participant=>
            {       
                $.each(participant,function(index,participantUser) 
                {  
                    participantUser.getUser().then(
                        user=>{  
                         //   console.log('userprint',user,'index',value.uniqueName)                       
                                if(user_identity!=user.identity){
                                    value['FULL_NAME'] =user.friendlyName;
                                    value['USER_PROFILE_PIC'] =user.attributes.USER_PROFILE_PIC; 
                                    if($.inArray(value.uniqueName,obj.print_user_arr)=='-1'){      
                                        converstionUserprint(value);
                                        count=count+1;
                                        console.log
                                        if(value.uniqueName){
                                             obj.print_user_arr.push(value.uniqueName)
                                        }
                                    }
                                   
    
                                }
                                var groupNameArr=value.uniqueName.split('_');
                                if(groupNameArr[0]=='Group'  && user_identity==user.identity && participant.length==1){
                                    converstionUserprint(value);
                                }
                              
                            resolve(user);
                        }
                    );
                })

            },
            error=>
            {
                console.log(error);
                reject(error);
            }
        )
    });
}





function getProfile(value) {
    var imgUrl=baseurl+"/front/images/placeholder-user.png";
    if(value){
        return value;
    }else{
        return imgUrl;
    }
    
}


function getName(value){
    var name='Unknown User';
    if(value){
        return value;
    }else{
        return name;
    }
}

var exist_group="";
function converstionUserprint(value)
{
    unread_messages = 0;
    var groupNameArr=value.uniqueName.split('_');
    
    var groupInfo='#';
    if(groupNameArr[0]=='Group'){
        // groupInfo='onclick="groupInfoPopup('+value.sid+')"';
        USER_PROFILE_PIC= getProfile(value.channelState.attributes.GROUP_PROFILE_PIC)
        AUTHOR_NAME=getName(value.friendlyName);
    }else{
        USER_PROFILE_PIC= getProfile(value.USER_PROFILE_PIC)
        AUTHOR_NAME= getName(value.FULL_NAME)
    }

    visible_unread_message_count = 'd-none';
    if(value.lastMessage != undefined && value.lastReadMessageIndex != undefined)
    {                                                                    
        conversation_last_message_index = value.lastMessage.index;
        conversation_last_read_message_index = value.lastReadMessageIndex;
        unread_messages = conversation_last_message_index - conversation_last_read_message_index;
        if(unread_messages > 0)
        {
            visible_unread_message_count = '';
        }
    }
    lastMsgdateCreated ="";
    lastMessageFind="";
    if(value.lastMsgdateCreated){
        lastMsgdateCreated=timeSinceAgo(value.lastMsgdateCreated);
    }
    if(value.lastMessageFind){
        lastMessageFind=value.lastMessageFind
    }
  
    html='<li class="list-group-item unread nav-item unique_'+value.uniqueName+'"  id="chat_list_li'+value.sid+'"   role="presentation">'+
    '<button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="false">'+
    '<figure class="avatar avatar-state-success"><img src="'+USER_PROFILE_PIC+'" class="rounded-circle" alt="image"></figure>'+
    '<div  class="users-list-body name" data-attributes-name="'+AUTHOR_NAME+'" data-attributes-img="'+USER_PROFILE_PIC+'" data-sid="'+value.sid+'" data-username="'+AUTHOR_NAME+'" data-group-info="'+groupInfo+'" id="chat_username'+value.sid+'" ><div><h5>'+AUTHOR_NAME+'</h5><p id="last_msg_sid_'+value.sid+'">'+lastMessageFind+'</p></div>'+
    '<div class="users-list-action "><small class="text-muted" id="last_msg_time'+value.sid+'">'+lastMsgdateCreated+'</small><div class="new-message-count unread_count status '+visible_unread_message_count+'" id="unread_count'+value.sid+'" >'+unread_messages+'</div></div></div></button></li>'                
    $('#chat_list').append(html);
    

htmlTEST='<aside><div class="follow-single-user"><div class="follow-username-img"><div class="followuser-img"><img src="'+USER_PROFILE_PIC+'" alt=""></div>'+
'<div class="followuser-name"><a href="#">'+AUTHOR_NAME+'</a></div></div><div class="form-check"><button class="btn btn-primary sharePostbtn hide" id="sharePostbtn_'+value.sid+'"  data-sid= "'+value.sid+'" type="button"  >Share</button></div></div></aside>';

    
    $('#chat_user_list_share').append(htmlTEST);
    last_message_print(value);

}








function updateConversationByUniqueName(conversation) 
{
    var sid=conversation.sid
    var attributes={ 
        GROUP_PROFILE_PIC:$('#group_Profile_pic').val(),
        GROUP_CREATOR_ID :loggedInId,
        GROUP_CREATOR_NAME :loggedInUsername,
    }
   
    var friendlyName=$('#ChatRoomName').val();
    

    conversation.updateFriendlyName(friendlyName)
    .then(
            function(conversation)
            {
                conversation.updateAttributes(attributes)
                .then(
                        function (conversation) {
                            uniqueName=conversation.uniqueName
                            participants = conversation.getParticipants();
                            participants
                            .then(
                                    function(participants_data)
                                    {
                                        identity_array = [];
                                    
                                        if(participants_data.length > 0)
                                        {
                                            var userPartipant=$("#appendBlankparticpant").val()
                                            $.each(participants_data,function(index,participant)
                                            {
                                                identity_array.push(participant.identity);
                                            });

                                            $.each(userPartipant,function(index,value)
                                            {
                                                new_id=env+'_'+value;
                                                if(jQuery.inArray(new_id, identity_array) !== -1){
                                                }
                                                else {
                                                        conversation.add(new_id)
                                                        .then(
                                                                function(participantResponse)
                                                                {
                                                                    console.log(participantResponse)
                                                                
                                                                }
                                                        );   
                                                }
                                                var finalLength= userPartipant.length-1
                                                if(finalLength==index)
                                                {
                                                    $('#CreateChatRoom').modal('hide')
                                                    var url = baseurl+'/message?'+uniqueName;
                                                    window.location.href=url;
                                                }
                                                                
                                            
                                            })
                                        
                                                    
                                        }
                                    

                                        
                                    },
                                    function(error)
                                    {
                                        conversation.delete();
                                        console.log(error);
                                    }
                                );

                        }
                    )
            })

    
}

var last_msg_index;
function openChatDetail(current_conversation_id)
{
    // li_obj = $(this);
    //  current_conversation_id = li_obj.find('.name').attr('data-sid');
    //    MESSAGE_COUNT=;
      $('.list-group-item').removeClass('active');
      $('#chat_list_li'+current_conversation_id).addClass('active');
    //   $('sidebar-group')
    //   $('myTabContent')
    // $('#myTabContent').addClass('active');
    // $('#chats').removeClass('active');
    current_conversation = client.getConversationBySid(current_conversation_id);
   
    current_conversation.
    then(
        function(value)
        {
            messages(value.getMessages(100))
            .then(
                    function (last_msg_number)
                    {
                    
                        // console.log(vv);
                        $('.chat-header-user').removeClass('chat-type-group')
                        value.advanceLastReadMessageIndex(last_msg_number);
                        // user_img = li_obj.find('img').attr('src');
                        $('#myTabContent').find('#user_name').text($('#chat_username'+current_conversation_id).attr('data-username'));
                        
                        // $('#chat_panel').find('#chat_image').attr('src',user_img);
                        $('#myTabContent').removeClass('d-none');
                        $('#chat_list_li'+current_conversation_id).addClass('active');
                        $('#delete-conversation').attr('onClick','delete_conversation("'+current_conversation_id+'")');

                        var groupNameArr=value.uniqueName.split('_');
                        $('#chat_user_name').attr('data_conversion_id_group','');
                        if(groupNameArr[0]=='Group'){
                          $('#chat_user_name').attr('data_conversion_id_group',''+current_conversation_id+'');
                          $('.chat-header-user').addClass('chat-type-group')
                          callId= "G~V~"+value.sid
                          $('#video_call_room_name').val(callId);
                          $('#voice_call_room_name').val('');
                          $('#voice_call_li').addClass('hide');
                          $('#video_call_li').removeClass('hide');
                          
                        }else
                        {
                           $('#voice_call_li').removeClass('hide');
                           $('#video_call_li').removeClass('hide');
                        }
                        $('#unread_count'+current_conversation_id).text(0);
                        $('#unread_count'+current_conversation_id).addClass('d-none');
                        if(final=='message')
                        {
                           scrollLast()
                        }
                        
                        videoCallIniatetwillio(value)
                        
                    }
                );
        },
        function(error) {console.log(error);}
    );

    return true;
}





function videoCallIniatetwillio(value)
{
    var groupNameArr=value.uniqueName.split('_');
    if(groupNameArr[0]=='Group'){
        callId= "G~V~"+value.sid
        $('#video_call_room_name').val(callId);
        // $('#voice_call_room_name').val('');
        // $('#voice_call_li').addClass('hide');

    }else{
        // $('#voice_call_li').removeClass('hide');
        value.getParticipants().then(
            participant=>
            {          
                $.each(participant,function(index,participantUser) 
                {  
                    participantUser.getUser().then(
                        user=>{  
                            // console.log('userprint',user,'index')                       
                                if(identity!=user.identity){
                                   var calleeUserId=user.identity;
                                   returnCallerandCalleeName(calleeUserId)
    
                                }
                        }
                    );
                })
            },
            error=>
            {
                console.log(error);

            }
        )


    }   
    


    function returnCallerandCalleeName(calleeUserId)
    {

        var callerUserId=identity;
        var result = calleeUserId.split('_');
     
        var tempId = '';
        // if (loggedInId > result[1]){
        //     tempId=   calleeUserId +"~"+ callerUserId;
        // }else{ 
            tempId= callerUserId +"~"+ calleeUserId ;
        // }

       // callerUserId logged in callee other user_id

        var vc_name ="I~V~"+tempId ; // I~V~local_4~local_7
        var voice_name ="I~A~"+tempId; // I~A~local_4~local_7

        $('#video_call_room_name').val(vc_name)
        $('#voice_call_room_name').val(voice_name);
        return tempId;
    } 
    
    




    
}


$(document).on('click','audio_video_call_intiate',function(){
    var script_url = baseurl+'/front/js/twillio/twillio-video.js';
    loadScript(script_url) 
    $('#messages-part-windows').addClass('hide');
    $('#videoCallMessage').removeClass('hide')

});


function sendMessageSharePost(sid,message) 
{   
  
    current_conversation = client.getConversationBySid(sid);
   
    current_conversation.
        then(
                function(conversation)
                {
                    if(new_conversation_created == 1)
                    {
                        conversation.add(start_new_conversation_with_user)
                        .then(
                                function(participant) 
                                {
                                    new_conversation_created = 0;
                                    start_new_conversation_with_user = '';
                                    // listOfConversations();
                                    var attributes={ 
                                        AUTHOR_NAME:loggedInUsername,
                                        AUTHOR_PIC :$('#logged_inuser_profie').val(),
                                        CONVERSATION_SID : sid
                                    }
                                    //send message with based on type
                
                                            
                                        conversation.sendMessage(message,attributes).
                                            then(
                                                    last_message_index => 
                                                    {
                                                
                                                        conversation.setAllMessagesUnread();
                                                        // conversation.advanceLastReadMessageIndex(last_message_index);
                                                        $('#text_message_chat').val('');
                                                        $('#file_send_in_chat').removeClass('hide')
                                                        $('#send-message-user').addClass('hide')
                                                        $('#send-message-user').attr('disable',false)
                                                    }
                                                );
    
                                },
                                function(error) {console.log(error);}
                        );
                    }
                    else
                    {
                        //send message with based on type file 0 means text
                        var attributes={ 
                            AUTHOR_NAME:loggedInUsername,
                            AUTHOR_PIC :$('#logged_inuser_profie').val(),
                            CONVERSATION_SID : sid
                        }
                            conversation.sendMessage(message,attributes).
                                then(
                                        last_message_index => 
                                        {
                                            conversation.advanceLastReadMessageIndex(last_message_index);
                                            $('#text_message_chat').val('');
                                            $('#file_send_in_chat').removeClass('hide')
                                            $('#send-message-user').addClass('hide')
                                        }
                                    );
    
                    }
                },
                function(error) {console.log(error);}
            );
}

// $(document).on('click','#chat_user_name',delete_conversation);



$(document).on('click','#chat_user_name',function(){
    convo_id = $(this).attr('data_conversion_id_group');
    if(convo_id){
        groupInfo(convo_id);
    }
})

function groupInfo(convo_id)
{
   $('#CreateChatRoom').modal('show');
   $('#edit_group_convoId').val(convo_id)
   editGroupPopup(convo_id)
}





$(document).on('click','#share_post_modal',function(){
   
   var post_id= $(this).attr('data-post-id')
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });

    var ajaxurl = baseurl+'/branch-io-share';

    var formData={
        post_id:post_id
    }

    var type='POST';
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        dataType: 'json',
        success: function (data) {
            // $('#sharePost').modal('show');
            // // $('#msg').html(data);
            // $('#link').val(data.data.url);
            // $('#link_share').html(data.data.url);
            // $('#link_share').attr('href',data.data.url);

            $('#sharePost').modal('show');
            $('#link').val(data.data.url);
            $('#link').attr('redirect-url',data.data.url);
            $('.sharePostbtn').text('Share');
            $('.sharePostbtn').prop('disabled', false);
            $("#draft_status").val(1);
        },
        error: function (data) {
        }
    });


})


var groupCreated_by='';
function editGroupPopup(convo_id)
{
   
    conversation = client.getConversationBySid(convo_id);
    conversation.
    then(
        function(value)
        {
            // $('#group_particpants').html('');
            $("#appendBlankparticpant option").remove();
            $('#groupUserlist').html('');
            $('#groupUserlist_always_hide').html('');
            var data=getParticipantGroup(value);
            $("#ChatRoomName").val(value.friendlyName)
            $('#imagePreview').css('background-image', 'url('+value.channelState.attributes.GROUP_PROFILE_PIC +')');
            $('#imagePreview').hide();
            $('#imagePreview').addClass('update');
            $('#imagePreview').fadeIn(650);
            $("#group_created_by").html(value.channelState.attributes.GROUP_CREATOR_NAME) 
            dateUpdated= moment(value.dateCreated).format('MMM DD, YYYY');  
            $("#group_created_date").html(dateUpdated)
            groupCreated_by=value.channelState.createdBy
            $('#group_create_btn').addClass('hide')
            $('#delete_exit_btn_group').removeClass('hide')
            $('#group_created_date_h').removeClass('hide')
            $('#group_created_by_h').removeClass('hide')
            $('#update_group').removeClass('hide')
            $('#participant_count').removeClass('hide')
            $('#group_uniqueName').val(value.uniqueName)
            $('#group_Profile_pic').val(value.channelState.attributes.GROUP_PROFILE_PIC);
            $('#is_update').val(0)
            // $('#imageUpload').val(value.channelState.attributes.GROUP_PROFILE_PIC);
            // $('#group_Profile_pic').val()
            // followerList()
            
            

            
        },
        function(error) {console.log(error);}
    );

}


var user_count='';
function getParticipantGroup(value) 
{
    return new Promise((resolve,reject)=>
    {
        value.getParticipants().then(
            participant=>
            {
                var promises=[]
                participant_count= participant.length
                $('#participant_count').html(participant_count+' Participants')

                $.each(participant,function(index,participantUser) 
                {  
                    participantUser.getUser().then(
                        user=>{
                            promises.push(user);
                            groupUserprint(user)
                        }
                    );

                })
                
               
            },
            error=>
            {
                console.log(error);
                reject(error);
            }
        )
    });

}

var currentFvrt={};
$.each(followingusers, function(key,val) {
    currentFvrt[env+'_'+val.id]=val;
});

function groupUserprint(user)
{
    var result = user.identity.split('_');
  
    var value=result[1]
    users=$('#appendBlankparticpant').val();

    if($.inArray(value,users)=='-1'){
        users.push(value);
        $("#appendBlankparticpant").append('<option  id="'+value+'" selected="" value="'+value+'"/>')
        $('#svg_add_'+value).addClass('d-none');
        $('#text_change_add_remove_'+value).addClass('btn-primary')
        $('#text_change_add_remove_'+value).removeClass('btn-secondary')
        $('#text_change_add_remove_'+value).text('Remove')
    }else{
        users.pop(value);
        $("#appendBlankparticpant option[value="+value+"]").remove();
        $('#text_change_add_remove_'+value).text('+ Add')        
        $('#text_change_add_remove_'+value).addClass('btn-secondary')
        $('#text_change_add_remove_'+value).removeClass('btn-primary')
    }
    if(loggedInId==value){
        $("#appendBlankparticpant option[value="+value+"]").remove();
    }

      var  fullName=getName(user.friendlyName);
      var profile_pic =getProfile(user.attributes.USER_PROFILE_PIC); 
      var HtmlBtn='';
      if(user.identity==groupCreated_by){
          HtmlBtn='<button type="button" class="btn  btn-primary bg-transparent " >Admin</button>';
      }
      if(groupCreated_by!=user.identity){
           HtmlBtn='';
          
      }
      if(user_identity==groupCreated_by && groupCreated_by!=user.identity){
          result=user.identity.split('_')
          HtmlBtn='<button  id="text_change_add_remove_'+result[1]+'" type="button" data_id="'+env+'_'+result[1]+'"class="btn  btn-primary remove_person_to_group" data-convo-id="'+convo_id+'"  data_id="'+user.identity+'" >Remove</button></div></div>';
      }
        if(groupCreated_by!=user_identity){
            $('#delete_exit_btn_group').attr('data-convo-id',convo_id)
            $('#delete_exit_btn_group').attr('identity',user.identity)
            $('#delete_exit_btn_group').attr('data-type',0)//if 1 then delete and 0 means exist
            $('#exit_btn_group').text('Exit Group')
            $('#exit_btn_group').attr("type", "button");
            $('#exit_btn_group').attr('data-type',0)//if 1 then delete and 0 means exist
            $('#exit_btn_group').attr('identity',user_identity)


            

            $('#exit_btn_group').attr('data-convo-id',convo_id)
            $('#delete_exit_btn_group').addClass('hide')
            $('#ChatRoomName').prop('disabled', true);
            $('#ChatRoomName').prop('disabled',true)
            $('#update_group').addClass('hide')
            $('#exit_btn_group').removeClass('hide')
            $('#hide_particpant_admin').addClass('hide')
            
            $('#imageUpload').prop('disabled',true);
            
        }else{
            $('#delete_exit_btn_group').attr('data-convo-id',convo_id)
            
            $('#exit_btn_group').attr('data-convo-id',convo_id)
            $('#delete_exit_btn_group').attr('data-type',1)//if 1 then delete and 0 means exist
            $('#delete_exit_btn_group').text('Delete Group')
            $('#update_group').removeClass('hide')
            
            $('#exit_btn_group').attr("type", "submit");
            $('#ChatRoomName').prop('disabled', false);
            $('#ChatRoomName').prop('disabled',false);
            $('#exit_btn_group').addClass('hide')
            $('#hide_particpant_admin').removeClass('hide')
            
           $('#imageUpload').prop('disabled',false);
            
        }


        
        if(currentFvrt[user.identity]!=undefined){
            // console.log('dddd',user.identity)
             currentFvrt[user.identity]=null;
        }
        var identityMade =user.identity
        var nameHtml= '<div class="follow-single-user group_user_popup pe-0" id="'+identityMade+'"><div class="follow-username-img"><div class="followuser-img"><img src="'+profile_pic+'" alt=""></div>'+
        '<div class="followuser-name"><a href="#">'+fullName+'</a></div></div><div class="follow-btn">'+HtmlBtn+'</div></div>';


        console.log(currentFvrt);
        $('#groupUserlist').append(nameHtml);
        $('#groupUserlist_always_hide').append(nameHtml);

       
        var tlds = $("#appendBlankparticpant").val();
   

}





function scrollLast()
{
    setTimeout(function() {
        $('#chat_body').scrollTop($("#chat_body")[0].scrollHeight);
    }, 1200);
    
}



async function messages(list_of_messages) 
{
    $('#chat_html').html('');
    obj.list_of_messages_data=list_of_messages
    let last_message_index = new Promise( function (resolve) 
    {
        list_of_messages.then(
            function(messages)
            {
                items = messages.items;

                hasNextPage = messages.hasNextPage;
                hasPrevPage = messages.hasPrevPage;
                last = 0;
                $('#chat_html').removeClass('no-message'); 
                if(items.length > 0)
                { 
                    var promises = [];
                    $.each(items,function(index,value) 
                    {         
                            if(value.type=="media")
                            { 
                                promises.push(getMediaMessage(value));
                            }  
                            last = value.state.index;
                    });
                
                         
                    Promise.all(promises)
                    .then(
                        result => 
                        {         
                            messagePrint(items)
                        }
                    ).catch(error=>{console.log(error)});
                        
                    resolve(last); 
                }
                else
                {
                    $('#chat_html').html('<span id="hide_msg">No Message found</span>');
                    $('#chat_html').addClass('no-message'); 
                    resolve(0);
                }

            },
            function(error) {console.log(error);}
                              
        );
    })
  
    // console.log(current_conversation);
    return last_message_index;
    // current_conversation.updateLastReadMessageIndex(last_message_index);
}

function getMediaMessage(value) 
{
    return new Promise((resolve,reject)=>
    {
        value.media.getContentTemporaryUrl().then(
            urlValue=>
            {          
                value['dyanmic_media_url']=urlValue;
                resolve(value);
                        
            },
            error=>
            {
                console.log(error);
                reject(error);
            }
        )
    });
}


var lastDateUpdate='';
async function messagePrint(items)
{

    var lastDate="";
    $('#chat_html').html('');
    $('#chat_html').html('');
    $.each(items,function(index,value) 
    {
        dateUpdated = value.dateUpdated;
        var messageDivider="";
        var groupTextName="";
        var lastDateGet =timeSince(value.dateUpdated)
        if(lastDate != lastDateGet){
            lastDate=lastDateGet      
            if(lastDate=="Today"){lastDateUpdate =lastDateGet}else{lastDateUpdate=lastDateGet}   ;
           messageDivider+='<div class="message-item messages-divider" data-label="'+lastDateGet+'"></div>';
        }

        var groupNameArr=value.conversation.channelState.uniqueName.split('_');
        if(groupNameArr[0]=='Group'){
            groupTextName='<div class="message-avatar mb-1"><div><div class="time chat_user_name">'+value.attributes.AUTHOR_NAME+'</div></div></div>';

        }
        
        //  dateUpdated= timeSinceAgo(dateUpdated) 
         dateUpdated= moment(dateUpdated).format('HH:mm a');  
                var divide_date= '';
                if(value.media)
                { 
                        var typeGet= getfileType(value.media.contentType);
                        var img_url=  typeGet.url;
                        if(typeGet.name=="image"){
                            img_url=value.dyanmic_media_url;
                            }
                            if(value.author != identity){
                                html = '<div class="message-item">'+groupTextName+'<div class="message-content"><a target ="_blank"  href="'+value.dyanmic_media_url+'"><img class="chat_image_msg" src="'+img_url+'"></img></a></div>'+
                                '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>'    
                            }
                            else{
                                html = '<div class="message-item outgoing-message">'+
                                '<div class="message-content"><a target ="_blank" href="'+value.dyanmic_media_url+'"><img class="chat_image_msg" src="'+img_url+'"></img></a></div>'+
                                '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>';
                            }
                        $('#chat_html').append(messageDivider+html);

                }else{
                        var valueMsg=isValidURL(value.body);
                        console.log(valueMsg);
                        if(valueMsg){
                            valueMsg='<a href="'+value.body+'">'+value.body+'</a>'
                        }else{
                            valueMsg=value.body;
                        }
                        if(value.author != identity){
                            html = '<div class="message-item">'+groupTextName+'<div class="message-content">'+valueMsg+'</div>'+
                            '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>'    
                        }else{
                            html = '<div class="message-item outgoing-message">'+
                            '<div class="message-content">'+valueMsg+'</div>'+
                            '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>';
                         }
                        $('#chat_html').append(messageDivider+html);
                }
                if(final=='message')
                {
                   scrollLast()
                }
               

    });
}


function isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
}


$("#formInputFile").change(function(){
        var fileValue=$('#formInputFile').val();
        if(fileValue){
        $('#globalFileTypeCheck').val(1)
            $('#file_send_in_chat').addClass('hide')
            $('#send-message-user').removeClass('hide')
        }else{
            $('#globalFileTypeCheck').val(0)
        }
        sendMessage() 
});


$("#text_message_chat").keyup(function(){
        $('#globalFileTypeCheck').val(0)
        var valueFile = $('#text_message_chat').val();
        if(valueFile==""){
            $('#file_send_in_chat').removeClass('hide')
            $('#send-message-user').addClass('hide')
        }else{
            $('#file_send_in_chat').addClass('hide')
            $('#send-message-user').removeClass('hide')
        }
        $('#formInputFile').val('');
})



function sendMessage() 
{   
    $('#send-message-user').attr('disable',true)
    new_message = $('#text_message_chat').val();
    var fileType=$('#globalFileTypeCheck').val();
    if(!new_message && fileType==0)
    {
        alert('Please enter a message first');
        return false;
    }
    current_conversation.
        then(
                function(conversation)
                {
                    if(new_conversation_created == 1)
                    {
                        conversation.add(start_new_conversation_with_user)
                        .then(
                                function(participant) 
                                {
                                    new_conversation_created = 0;
                                    start_new_conversation_with_user = '';
                                    listOfConversations();
                                    var attributes={ 
                                        AUTHOR_NAME:loggedInUsername,
                                        AUTHOR_PIC :$('#logged_inuser_profie').val(),
                                        CONVERSATION_SID : current_conversation_id
                                    }
                                    //send message with based on type
                                    if(fileType==0){
                                            
                                        conversation.sendMessage(new_message,attributes).
                                            then(
                                                    last_message_index => 
                                                    {
                                                
                                                        conversation.setAllMessagesUnread();
                                                        // conversation.advanceLastReadMessageIndex(last_message_index);
                                                        $('#text_message_chat').val('');
                                                        $('#file_send_in_chat').removeClass('hide')
                                                        $('#send-message-user').addClass('hide')
                                                        $('#send-message-user').attr('disable',false)
                                                    }
                                                );
                                    }else{
                                        $('#send_message_loader').removeClass('hide')
                                        $('#myTabContent').addClass('loader-bg')
                                      
                                        
                                        const formData = new FormData();
                                        if(recordingblob){
                                            formData.append('recording',blob);
                                        }else{
                                            formData.append('file', $('#formInputFile')[0].files[0]);
                                        }
                                        conversation.sendMessage(formData,attributes).
                                        then(
                                                last_message_index => 
                                                {
                                                    conversation.advanceLastReadMessageIndex(last_message_index);
                                                    $('#formInputFile').val('');
                                                    $('#file_send_in_chat').removeClass('hide')
                                                    $('#send-message-user').addClass('hide')
                                                    $('#send-message-user').attr('disable',false)
                                                    $('#send_message_loader').addClass('hide')
                                                  
                                                    $('#myTabContent').removeClass('loader-bg')


                                        
                                                }
                                            );
                                     };  
    
                                },
                                function(error) {console.log(error);}
                        );
                    }
                    else
                    {
                        //send message with based on type file 0 means text
                        var attributes={ 
                            AUTHOR_NAME:loggedInUsername,
                            AUTHOR_PIC :$('#logged_inuser_profie').val(),
                            CONVERSATION_SID : current_conversation_id
                        }
                        if(fileType==0){
                            conversation.sendMessage(new_message,attributes).
                                then(
                                        last_message_index => 
                                        {
                                            conversation.advanceLastReadMessageIndex(last_message_index);
                                            $('#text_message_chat').val('');
                                            $('#file_send_in_chat').removeClass('hide')
                                            $('#send-message-user').addClass('hide')
                                        }
                                    );

                        }else{
                            $('#send_message_loader').removeClass('hide')
                            $('#myTabContent').addClass('loader-bg')
                            const formData = new FormData(); 
                            if(recordingblob){
                                formData.append('recording',blob);
                            }else{
                                formData.append('file', $('#formInputFile')[0].files[0]);
                            }
                            conversation.sendMessage(formData,attributes).
                            then(
                                    last_message_index => 
                                    {
                                        conversation.advanceLastReadMessageIndex(last_message_index);
                                        $('#formInputFile').val('');
                                        $('#send_message_loader').addClass('hide')
                                        $('#file_send_in_chat').removeClass('hide')
                                        $('#send-message-user').addClass('hide')
                                        $('#myTabContent').removeClass('loader-bg')
                                    }
                                );
                        }
    
                    }
                },
                function(error) {
                    swal({
                        title:'Conversation no longer exist.',
                        // text: result,
                        icon: "warning",
                        buttons: [
                          'No, cancel it!',
                          'Yes, I am sure!'
                        ],
                        dangerMode: true,
                      }).then(function(isConfirm) {
                        if (isConfirm) {
                        //    location.reload()
                           var url = baseurl+'/message';
                           window.location.href=url;
                        }else{      
                            // location.reload()
                            var url = baseurl+'/message';
                            window.location.href=url;
                        }
                      });
                    console.log(error);
                }
            );
}




function updateNewMessage(new_message) {
    $('#chat_html').removeClass('no-message');
    $('#hide_msg').remove(); 
    new_message_conversation_id = new_message.conversation.sid;
    if(current_conversation_id == new_message_conversation_id){ 
        var groupTextName="";
        var groupNameArr=new_message.conversation.uniqueName.split('_');
        if(groupNameArr[0]=='Group'){
            groupTextName='<div class="message-avatar mb-1"><div><div class="time chat_user_name">'+new_message.attributes.AUTHOR_NAME+'</div></div></div>';
    
        }
      
        dateUpdated= moment(new_message.dateUpdated).format('HH:mm a');  
        var lastDateGet =timeSince(new_message.dateUpdated)
        var messageDivider="";
        // if(new_message.lastMessage){
        //     lastDateUpdate =timeSince(new_message.lastMessage.dateCreated);
        // }

        if(lastDateUpdate!=lastDateGet)
        {
            lastDateUpdate=lastDateGet;
            messageDivider+='<div class="message-item messages-divider" data-label="'+lastDateGet+'"></div>';
        }

        if(new_message.media){  
            new_message.media.getContentTemporaryUrl().then(
                function(urlValue) { /* code if successful */ 
                    var typeGet= getfileType(new_message.media.contentType);
                    var img_url=  typeGet.url;
                    // var img_url=  typeGet.url;
                    if(typeGet.name=="image"){img_url=urlValue;}
             
                    if(new_message.author != identity)
                    {
                        html = '<div class="message-item">'+groupTextName+'<div class="message-content"><a target ="_blank"  href="'+urlValue+'"><img class="chat_image_msg" src="'+img_url+'"></img></a></div>'+
                        '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>'    
                        $('#unread_count'+new_message_conversation_id).text(0);
                        unread_messages=0;
                        var message_index= new_message.index
                        new_message.conversation.advanceLastReadMessageIndex(message_index)
                    }
                    else
                    {
                        html = '<div class="message-item outgoing-message">'+
                        '<div class="message-content"><a target ="_blank" href="'+urlValue+'"><img class="chat_image_msg"  src="'+img_url+'"></img></a></div>'+
                        '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>';
                        var message_index= new_message.index
                        new_message.conversation.advanceLastReadMessageIndex(message_index)
                        
                        unread_messages=0;
                    }
                    $('#chat_html').append(messageDivider+html);
                    $("#last_msg_sid_"+current_conversation_id).html(typeGet.name)
                    $("#last_msg_time"+current_conversation_id).html(timeSinceAgo(new_message.dateUpdated))
                  
                    scrollLast()
                    
                 },

                 function(error) { /* code if some error */
                  console.log(error); }
              );
            
        }else{ 
                if(new_message.author != identity){
                        html = '<div class="message-item">'+groupTextName+'<div class="message-content">'+new_message.body+'</div>'+
                        '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>'    
                        $('#unread_count'+new_message_conversation_id).text(0);
                        var message_index= new_message.index
                        new_message.conversation.advanceLastReadMessageIndex(message_index)
                        
                        unread_messages=0;
                    }
                    else{
                        html = '<div class="message-item outgoing-message">'+
                        '<div class="message-content">'+new_message.body+'</div>'+
                        '<div class="message-avatar"><div><div class="time">'+dateUpdated+'</div></div></div></div>';
                        var message_index= new_message.index
                        new_message.conversation.advanceLastReadMessageIndex(message_index)
                        
                        unread_messages=0;
                    }
                    $('#chat_html').append(messageDivider+html);
                    $("#last_msg_sid_"+current_conversation_id).html(new_message.body)
                    $("#last_msg_time"+current_conversation_id).html(timeSinceAgo(new_message.dateUpdated))
                  
                    scrollLast()
        }     
        
    }
    else{
            listOfConversations();
            new_conversation_message_index = new_message.index;
            new_conversation_last_read_messsage_index = new_message.conversation.lastReadMessageIndex;
    
            if(new_conversation_message_index != undefined && new_conversation_last_read_messsage_index != undefined)
            {                                                   
                unread_messages = new_conversation_message_index - new_conversation_last_read_messsage_index;
                if(unread_messages > 0)
                { 
                   unread_messages = new_conversation_message_index - new_conversation_last_read_messsage_index;
                }
            }
            unread_messages = new_conversation_message_index - new_conversation_last_read_messsage_index;
            $('#unread_count'+new_message_conversation_id).text(unread_messages);
            if($('#unread_count'+new_message_conversation_id).hasClass('d-none'))
            {
                $('#unread_count'+new_message_conversation_id).removeClass('d-none');
            }
            
           scrollLast()
            
        
    }
    
}

 function getfileType(fileName){
    var fileType=fileName.split('/')[0];
    var type={};
    if(fileType=='image'){
        type.url=baseurl+"/front/images/chat-img.png";
        type.name='image';
        return type;
    }
    else if(fileType=="video"){
        type.url=baseurl+"/front/images/chat-video.png";
        type.name='video';
        return type;
    }else if(fileType=="audio"){
        type.url=baseurl+"/front/images/microphone-solid.svg";
        type.name='audio';
        return type;
    }
    else{
        return 'file';
    }

 }





 var lastScrollTop = 0;
 



// make it hidden by default


$(document).ready(function(){
    $("#user_search_message_list").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var allItems = $("#chat_list li")
      $("#chat_list li").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });

      var matchedItems = $("#chat_list li").filter(function() {
        return $(this).text().toLowerCase().indexOf(value) > -1
      });

    // hide all items first
    allItems.toggle(false)
    
    // then show matched items
    matchedItems.toggle(true)

     if (matchedItems.length == 0) {
        $('.notFound_conv').remove();
        $('#chat_list').append(notFound);
        // $('#notFound_conv').removeClass('hide');
    }
    else{
        $('.notFound_conv').remove();
    }

    });
  });


  
$(document).ready(function(){
    $("#user_search_share_list").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var allItems = $("#chat_user_list_share aside")
        $("#chat_user_list_share aside").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });

      var matchedItems = $("#chat_user_list_share aside").filter(function() {
        return $(this).text().toLowerCase().indexOf(value) > -1
      });

    // hide all items first
    allItems.toggle(false)
    
    // then show matched items
    matchedItems.toggle(true)

     if (matchedItems.length == 0) {
        $('.notFound_conv').remove();
        $('#chat_user_list_share').append(notFound);
        // $('#notFound_conv').removeClass('hide');
    }
    else{
        $('.notFound_conv').remove();
    }

    });
  });

  
$(document).ready(function(){
    $('#user_search_group_chat').on('keyup',function(){
        $value=$(this).val();
        var id_arr=$('#appendBlankparticpant').val()
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type : 'post',
            url : baseurl+'/following-list',
            data:{
                'search_keyword':$value,
                'user_id':loggedInId,
                'id_arr':id_arr
            },
            success:function(data){
               
                $('#groupUserlist').html(data.following_list_create_group);
                if($value=='')
                {
                    $('#groupUserlist').html('');
                    $('#groupUserlist').append($('#groupUserlist_always_hide').html());
                }
                if($('#is_update').val()==1 && $value=='')
                {
                    $('#groupUserlist').html(data.following_list_create_group);
                }
            }
       });
       
   })
  });




 function timeSince(date) {

    var lastDateGet =moment(date).format('MMMM Do YYYY');
    var todate= new Date()
    todate =moment(todate).format('MMMM Do YYYY');
    var d = new Date();
    d.setDate(d.getDate() - 1);
    var Yesterday= moment(d).format('MMMM Do YYYY');
    if(lastDateGet==todate){
        return "Today";
    }
    else if(lastDateGet==Yesterday){
        return "Yesterday"
    }
    else{
        return moment(date).format('MMM DD, YYYY');
    }

  }



  function timeSinceAgo(date) {

    var seconds = Math.floor((new Date() - date) / 1000);
    var interval = seconds / 31536000;
    if (interval > 1) {
      return moment(date).format('MMM DD, YYYY');  
    }
    interval = seconds / 2592000;        
    if (interval > 1) {
      return  moment(date).format('MMM DD, YYYY');  
    }
    interval = seconds / 86400;
    if(interval > 1) {
        if(interval==1)
        {
            return 'Yesterday';
        }else{
            return moment(date).format('MMM DD, YYYY');
        }
       
    }
    interval = seconds / 3600;   
    if (interval > 1) {
      return Math.floor(interval) + " hours ago.";
    }
    interval = seconds / 60;
    if (interval > 1) {
      return Math.floor(interval) + " minutes ago.";
    }
    return "Just now.";
    
  }


  //]]>


$('.chat-body').scroll(function(){
if($('.chat-body').scrollTop() + $('.chat-body').height() <= $('#chat_html').height()){
    console.log('Start Scroll')
    //  openChatDetail(current_conversation_id);  
     
    }
});







}).catch(function(error) {
});




