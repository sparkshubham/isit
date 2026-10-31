/*

    uniqueName of conversation will be in pattern
    user_identity1_user_identity2 where uder identity will be sorted in acsending order
    i.e. if users with id 1 and 3 are chatting then 
    uniquename of conversation will be 1_3
*/



token = $('#token_token').val();
identity = $('#twillio_identity').val();
friendlyName = $('#friendlyName').val();
// var conversations;
current_conversation_id = '';
current_conversation = '';
new_conversation_created = 0;
start_new_conversation_with_user = '';
current_conversation_list_count = 0;


new Twilio.Conversations.Client.create(token).then(chatClient => {
    const client  = chatClient;
    
    
$(document).ready(function()
{
    //fetch conversation list
    listOfConversations();

});
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
        console.log(pushNotification);
    // if new conversation is initiated by someone else
    // if(conversationAdded.createdBy !== identity)
    // {
    //     listOfConversations();
    // }
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

// $(document).on('click','#chat_list li',function() 
$(document).on('click','#chat_list_liCH3ca353013168460da6623f5e127fd1a9',function() 
{
    li_obj = $(this);
    current_conversation_id = li_obj.find('.name').attr('data-sid');
    new_conversation_created = 0;
    start_new_conversation_with_user = '';
    openChatDetail(current_conversation_id);
});

var obj={};
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
    // obj.chat_user_id=$(this).attr('data-tc-user-id');
    obj.other_twillio_id=$(this).attr('data-user-twillio-id');
    obj.other_user_id=$(this).attr('data-tc-user-id');
    obj.profile_pic=$(this).attr('data-tc-profile');
    convoId= getConversationId(obj)
    obj.chat_user_id='local_'+obj.other_user_id;
    start_new_conversation();
    // createConversation(obj);
});

$(document).on('click','#send-message-user',sendMessage);

function start_new_conversation() 
{
  
    user_identity =identity;
    if(user_identity == undefined)
    {
        alert('Please select user');
        return;
    }
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
    // create_new_conversation(uniqueName);
    client.getConversationByUniqueName(uniqueName)
    .then(
            function(conversation_exits)
            {
                current_conversation_id = conversation_exits.sid;
                current_conversation = conversation_exits;
                openChatDetail(current_conversation_id);
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

    var attributes={ 
        AUTHOR_NAME:obj.username,
        USER_PROFILE_PIC :obj.profile_pic,
    }

    client.createConversation({'friendlyName':uniqueName,'uniqueName':uniqueName,'attributes':attributes})
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
                                                    conversation.add(start_new_conversation_with_user)
                                                    .then(
                                                            function(participantResponse)
                                                            {
                                                                listOfConversations();
                                                            }
                                                        );  
                                                },
                                                function (error) 
                                                {
                                                    console.log(error);
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
                            listOfConversations();
                            if(conversation_id == current_conversation_id)
                            {
                                // $('#chat_body').html('');
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
    // conversations = client.getSubscribedConversations();
    client.getSubscribedConversations()
    .then(
        function(list_of_conversations) 
        {
            
            items = list_of_conversations.items;
            hasNextPage = list_of_conversations.hasNextPage;
            hasPrevPage = list_of_conversations.hasPrevPage;
            // console.log(hasPrevPage);
            // console.log(hasNextPage);
            if(!hasPrevPage)
            {
                current_conversation_list_count = items.length;
                $('#myTab').html('');
            }
            else
            {
                current_conversation_list_count += items.length;
            }
            console.log(current_conversation_list_count);

            if(items.length > 0)
            {
                $.each(items,function(index,value) 
                {

                    if(value.uniqueName == null)
                    {
                        updateConversationUniqueName(value);
                    }
                    else
                    {
                        chat_US519fe2b79981407f8cd665b07d2db71e_US1532682f232c40bdb32eedb117f156bf

                        // user_identity = $.grep(value.uniqueName.split('_'), function(n) {
                        //   return n != identity;
                        // })[0];

                        user_identity = identity;

                        value.getParticipantByIdentity(user_identity)
                        .then(
                                function(participant)
                                {
                                    participant.getUser()
                                    .then(
                                            function (user) 
                                            {
                                                unread_messages = 0;
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
                                                // html='<li   id="chat_list_li'+value.sid+'" class="list-group-item nav-item" role="presentation">'+
                                                // '<button class="nav-link" class="nav-link" id="home-tab" role="tab" data-bs-toggle="tab" data-bs-target="#home"  aria-controls="home" aria-selected="false">'+
                                                // '<figure class="avatar avatar-group avatar-state-warning">'+
                                                // '<img src="images/users-group.svg" class="rounded-circle" alt="image"></figure>'+
                                                // '<div  class="users-list-body name"  data-sid="'+value.sid+'" data-username="'+user.friendlyName+'" id="chat_username'+value.sid+'" ><div><h5>'+user.friendlyName+' USERNAEM</h5><p>'+
                                                // '<i class="fa fa-camera mr-1"></i> Thank you so much. These files are very important to me.</p></div>'+
                                                // '<div class="users-list-action"><small class="text-muted">Yesterday</small></div></div></button></li>'

                                                html = '<li class="clearfix" id="chat_list_li'+value.sid+'"   class="nav-link" id="home-tab" role="tab" data-bs-toggle="tab" data-bs-target="#home"  aria-controls="home" aria-selected="false">'+
                                                            '<img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">'+
                                                            '<div class="about">'+
                                                                '<div class="name" data-sid="'+value.sid+'" data-username="'+user.friendlyName+'" id="chat_username'+value.sid+'">'+user.friendlyName+'</div>'+
                                                                '<div class="status '+visible_unread_message_count+'"> <i class="fa fa-circle online"></i> <span id="unread_count'+value.sid+'" class="unread_count">'+unread_messages+'</span> new messages </div>'+
                                                            '</div>'+
                                                        '</li>';
                                                        

                                                $('#myTab').append(html);
                                            }
                                        );
                                },
                                function(error)
                                {
                                    value.delete();
                                }
                            );
                    }


                });
            }
            else
            {
                html = '<li class="clearfix" >'+
                            '<div class="about">'+
                                '<div class="name" > No Conversations Yet </div>'+
                            '</div>'+
                        '</li>';
                $('#myTab').append(html);
            }
            
        },
        function(error) 
        {
            console.log(error);
        }
    );
}

function updateConversationUniqueName(conversation) 
{
    participants = conversation.getParticipants();
    participants
    .then(
            function(participants_data)
            {
                identity_array = [];
                if(participants_data.length > 0)
                {

                    $.each(participants_data,function(index,participant)
                    {
                        identity_array.push(participant.identity);
                    });
                    conversation.updateUniqueName(identity_array.join('_'));
                }
            },
            function(error)
            {
                conversation.delete();
                console.log(error);
            }
        );
}

function openChatDetail(current_conversation_id)
{
    // li_obj = $(this);
    // current_conversation_id = li_obj.find('.name').attr('data-sid');
    current_conversation = client.getConversationBySid(current_conversation_id);
    var last_msg_index;
    current_conversation.
    then(
        function(value)
        {
            messages(value.getMessages(5))
            .then(
                    function (last_msg_number)
                    {
                        // console.log(vv);
                        value.advanceLastReadMessageIndex(last_msg_number);
                        // user_img = li_obj.find('img').attr('src');
                        $('#chat_panel').find('#user_name').text($('#chat_username'+current_conversation_id).attr('data-username'));
                        // $('#chat_panel').find('#chat_image').attr('src',user_img);
                        $('#chat_panel').removeClass('d-none');
                        $('#chat_list_li'+current_conversation_id).addClass('active');
                        $('#delete-conversation').attr('onClick','delete_conversation("'+current_conversation_id+'")');
                        $('#unread_count'+current_conversation_id).text(0);
                        $('#unread_count'+current_conversation_id).parent().addClass('d-none');
                        var d = $('#chat_panel .chat-history');
                        d.scrollTop(d.height());
                    }
                );
        },
        function(error) {console.log(error);}
    );

    return true;
}

async function messages(list_of_messages) 
{

    $('#chat_html').html('');
    let last_message_index = new Promise( function (resolve) 
    {
        list_of_messages.then(
            function(messages)
            {
                items = messages.items;
                hasNextPage = messages.hasNextPage;
                hasPrevPage = messages.hasPrevPage;
                last = 0;
                if(items.length > 0)
                {
                    $.each(items,function(index,value) 
                    {
                        dateUpdated = value.dateUpdated;
                        if(value.author != identity)
                        {
                            html = '<li class="clearfix">'+
                                        '<div class="message-data text-right">'+
                                            '<span class="message-data-time">'+dateUpdated+'</span>'+
                                            '<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">'+
                                        '</div>'+
                                        '<div class="message other-message float-right"> '+value.body+' </div>'+
                                    '</li>';
                        }
                        else
                        {
                            html = '<li class="clearfix">'+
                                        '<div class="message-data">'+
                                            '<span class="message-data-time">'+dateUpdated+'</span>'+
                                            '<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">'+
                                        '</div>'+
                                        '<div class="message other-message"> '+value.body+'  </div>'+
                                    '</li>';
                        }
                        $('#chat_html').append(html);
                       // console.log(value.state.index); 
                       last = value.state.index;
                    });
                   resolve(last); 
                }
                else
                {
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

function sendMessage() 
{
    new_message = $('#text_message_chat').val();
    // console.log(new_conversation_created);
    // console.log(current_conversation);
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
                                    conversation.sendMessage(new_message).
                                        then(
                                                last_message_index => 
                                                {
                                                    console.log(last_message_index);
                                                    conversation.setAllMessagesUnread();
                                                    // conversation.advanceLastReadMessageIndex(last_message_index);
                                                    $('#text_message_chat').val('');
                                                }
                                            );
                                },
                                function(error) {console.log(error);}
                        );
                    }
                    else
                    {

                        conversation.sendMessage(new_message).
                            then(
                                    last_message_index => 
                                    {
                                        conversation.advanceLastReadMessageIndex(last_message_index);
                                        $('#text_message_chat').val('');
                                    }
                                );
                    }
                },
                function(error) {console.log(error);}
            );
}

function updateNewMessage(new_message) 
{
    new_message_conversation_id = new_message.conversation.sid;
    if(current_conversation_id == new_message_conversation_id)
    {
        //then new message to chat
        dateUpdated = new_message.dateUpdated;
        if(new_message.author != identity)
        {
            html = '<li class="clearfix">'+
                        '<div class="message-data text-right">'+
                            '<span class="message-data-time">'+dateUpdated+'</span>'+
                            '<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">'+
                        '</div>'+
                        '<div class="message other-message float-right"> '+new_message.body+' </div>'+
                    '</li>';
        }
        else
        {
            html = '<li class="clearfix">'+
                        '<div class="message-data">'+
                            '<span class="message-data-time">'+dateUpdated+'</span>'+
                            '<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">'+
                        '</div>'+
                        '<div class="message other-message"> '+new_message.body+'  </div>'+
                    '</li>';
        }
        $('#chat_body').append(html);
    }
    else
    {
        listOfConversations();
        new_conversation_message_index = new_message.index;
        new_conversation_last_read_messsage_index = new_message.conversation.lastReadMessageIndex;
        unread_messages = new_conversation_message_index - new_conversation_last_read_messsage_index;
        $('#unread_count'+new_message_conversation_id).text(unread_messages);
        if($('#unread_count'+new_message_conversation_id).parent().hasClass('d-none'))
        {
            $('#unread_count'+new_message_conversation_id).parent().removeClass('d-none');
        }
    }
}

}).catch(function(error) {
});

// const conversation = client.createConversation();
// const conversation = client.createConversation('CH9fca1390d6c14540a33506cac2c610cd');
// const conversations = client.getSubscribedConversations();

// console.log(conversations);
/*const user = client.getUser(identity);
user.then(
            function(value) {console.log(value.updateFriendlyName(friendlyName));},
            function(error) {console.log(error);}
          );*/