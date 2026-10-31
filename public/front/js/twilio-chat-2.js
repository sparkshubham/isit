chat_heading_html = '<div class="messageCols" id="{chat_list_id}" data-sid="{chat_sid}" data-lastMessage="{last_message_timestring}">'+
                        '<div class="notifRow">'+
                            '<div class="messageColsinner">'+
                                '<div class="notifLeft">'+
                                  '<div class="notifLeftImg">'+
                                    '<img src="'+BASEURL+'public/website/images/user-icon.jpg" />'+
                                  '</div>'+
                                '</div>'+
                                '<div class="notifRight">'+
                                  '<p class="deliveryAssign">{chat_user_name}<span>{chat_time}</span>'+
                                  '</p>'+
                                  '<p class="acceptence">'+
                                  '<span class="preview_chat_message d-none">{chat_user_message}</span>'+
                                  '<span class="unread_count badge-success badge d-none">{unread_count}</span></p>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

no_conversation_html = '<div class="messageCols"><div class="notifRow"><div class="messageColsinner"><div class="text-center">No Conversations</div></div></div></div>';

chat_panel_heading = '<div class="messageColsinner">'+
                            '<div class="notifLeft">'+
                              '<div class="notifLeftImg">'+
                                '<img src="'+BASEURL+'public/website/images/user-icon.jpg" />'+
                              '</div>'+
                            '</div>'+
                            '<div class="notifRight messageCols">'+
                              '<p class="deliveryAssign">{username} <span>{last_message_date}</span></p>'+
                              '<p class="acceptence">{user_email} <span>{last_message_time}</span></p>'+
                            '</div>'+
                        '</div>';
                  
token = $('#token_token').val();
identity = $('#twillio_identity').val();
friendlyName = $('#friendlyName').val();
fetch_messages_count = 5;
env=$('#env').val();
users_array = {};
current_conversation_id = '';
current_conversation = '';
current_conversation_list_count = 0;
current_conversation_last_fetched_index = '';
client ='';

$(document).ready(function()
{
    new Twilio.Conversations.Client.create(token).then(
        chatClient => 
        {
            client = chatClient;
            client.getSubscribedConversations()
            .then(updateChatList);
            // console.log();
            // updateChatList();
            
            client.on('messageAdded',function(messageAdded){
                updateNewMessage(messageAdded);
            });
        }
    ).catch(error => {
        console.log(error);
    });

});

function updateChatList() 
{
    displayOverlay();
    getConversations().then(
        sort_conversation_arr => 
        {
            sort_conversation_arr_2 = sortObjectByKeys(sort_conversation_arr);
            var promises = [];
            $.each(sort_conversation_arr_2,function (index,conversation) 
            {
                console.log(conversation);
                promises.push(
                    getChatHeading(conversation)
                    // .then(result=>{console.log(result)})
                    .catch(error=>{console.log(error)})
                );
            });  
            Promise.all(promises)
            .then(
                result => 
                {
                    // console.log(result);              
                    removeOverlay();
                }
                
            ).catch(error=>{console.log(error)});
        }
    ).catch(error => {
        $('#chat_list').html(no_conversation_html);
        removeOverlay();
    });
}

function sortObjectByKeys(o) {
    return Object.keys(o).sort().reverse().reduce((r, k) => (r[k] = o[k], r), {});
}

function getConversations()
{
    return new Promise((resolve,reject)=>
    {
        conversations = client.conversations.conversations; 
        var promises = [];
        sort_conversation_arr = {};
        if(conversations.size > 0)
        {

            $('#chat_list').html('');
            conversations.forEach(async function(single_conversation,index) 
            {
                if(single_conversation.uniqueName != null)
                {
                    // console.log(single_conversation);
                    last_message_created_date = single_conversation.lastMessage.dateCreated;
                    dateUpdated_str = new Date(last_message_created_date).getTime();
                    sort_conversation_arr[dateUpdated_str] = single_conversation;
                    promises.push(sort_conversation_arr) ;
                }
                else
                {
                    single_conversation.delete();
                }
            });

            Promise.all(promises)
            .then(
                result => 
                {
                    // console.log(result); 
                    resolve(sort_conversation_arr);
                }
            );
        }
        else
        {
            reject(sort_conversation_arr);
        }
        
    });
}

function getChatHeading(conversation) 
{
    return new Promise((resolve,reject)=>
    {
        user_identity = $.grep(conversation.uniqueName.split('_'), function(n) {
                      return n != identity;
                    })[0];
        
        last_message_created_date = conversation.lastMessage.dateCreated;
        date = preview_chat_date(last_message_created_date);
        dateUpdated_str = new Date(last_message_created_date).getTime();
        user_attributes = conversation.attributes[user_identity];
        users_array[user_attributes.name] = user_attributes;
        users_array[user_attributes.name]['sid'] = conversation.sid;
        html = chat_heading_html.replace('{chat_user_name}',user_attributes.name)
                .replace('{last_message_timestring}',dateUpdated_str)
                .replace('{chat_list_id}',conversation.sid)
                .replace('{chat_sid}',conversation.sid)
                // .replace('{chat_user_message}',last_message)
                .replace('{chat_time}',date);
        
        $('#chat_list').append(html);
        unread_messages = 0
        conversation.getUnreadMessagesCount()
        .then(
                unread_messages=>
                {   
                    if(unread_messages != null && unread_messages > 0)
                    {
                        $('#chat_list').find('#'+conversation.sid).find('.unread_count').text(unread_messages);
                        $('#chat_list').find('#'+conversation.sid).find('.unread_count').removeClass('d-none');
                        // html = html.replace('{unread_count}',unread_messages).replace('d-none','');
                    }
                    conversation.getMessages()
                    .then(
                          result=>
                          {
                            last_message = result['items'][0]['body'];
                            html = $('#'+conversation.sid).html();
                            html = html.replace('{chat_user_message}',last_message)
                                        .replace('preview_chat_message d-none','preview_chat_message');
                            $('#'+conversation.sid).html(html);
                            resolve(true);
                          }
                    )
                }
        )
        .catch(error=>{console.log(error)});
    });
}

function sort_li(a, b)
{
    return ($(b).data('lastmessage')) < ($(a).data('lastmessage')) ? 1 : -1;    
}

function getConversations_old()
{
    return new Promise((resolve,reject)=>
    {
        conversations = client.conversations.conversations; 
        var promises = [];
        sort_conversation_arr = [];
        if(conversations.size > 0)
        {

            $('#chat_list').html('');
            conversations.forEach(async function(single_conversation,index) 
            {
                // single_conversation.delete().then(console.log('delete')).catch(error=>{console.log(error)});
                // user_identity = $.grep(single_conversation.uniqueName.split('_'), function(n) {
                //   return n != identity;
                // })[0];
                promises.push(
                    single_conversation.getMessages(1)
                    .then(
                          messages=>
                          {
                            getChatHeading(messages,single_conversation)
                            .then(
                                  result=>
                                  {
                                    console.log('added');
                                  }
                            )
                            .catch(error=>{console.log(error)});                                   

                          }
                    )
                    .catch(error => {
                        removeOverlay();
                        console.log(error);
                    })
                ) ;
            }); 
            
            Promise.all(promises).then(
                () => {

                    client.getSubscribedConversations()
                    .then(
                        results => {
                            if(results.items.length > 0)
                            {
                                resolve(true);
                            }
                            else
                            {
                                reject(false);
                            }
                        }
                    )
                    .catch(error=>{reject(error)})
                }
                
            );
        }
        else
        {
            reject(false);
        }
        
    });
}

function getParticipant(single_conversation,user_identity) 
{
    return new Promise((resolve,reject)=>
    {
        single_conversation.getParticipantByIdentity(user_identity).then(
            participant=>
            {
                participant.getUser().then(
                    user=>{
                        resolve(user);
                    }
                );
            },
            error=>
            {
                console.log(error);
                reject(error);
            }
        )
    });
}

function getChatHeading_old(messages,conversation) 
{
    return new Promise((resolve,reject)=>
    {
        user_identity = $.grep(conversation.uniqueName.split('_'), function(n) {
                      return n != identity;
                    })[0];
        last_message = messages.items[0].body;
        last_message_created_date = conversation.lastMessage.dateCreated;
        date = preview_chat_date(last_message_created_date);
        dateUpdated_str = new Date(last_message_created_date).getTime();
        user_attributes = conversation.attributes[user_identity];
        html = chat_heading_html.replace('{chat_user_name}',user_attributes.name)
                .replace('{last_message_timestring}',dateUpdated_str)
                .replace('{chat_list_id}',conversation.sid)
                .replace('{chat_sid}',conversation.sid)
                .replace('{chat_user_message}',last_message)
                .replace('{chat_time}',date);
        
        $('#chat_list').append(html);
        unread_messages = 0
        conversation.getUnreadMessagesCount()
        .then(
                unread_messages=>
                {   
                    if(unread_messages != null && unread_messages > 0)
                    {
                        console.log(unread_messages);
                        console.log($('#chat_list').find('#'+conversation.sid).find('.unread_count').text())
                        $('#chat_list').find('#'+conversation.sid).find('.unread_count').text(unread_messages);
                        $('#chat_list').find('#'+conversation.sid).find('.unread_count').removeClass('d-none');
                        // html = html.replace('{unread_count}',unread_messages).replace('d-none','');
                        resolve(true);
                    }


                }
        );
    });
}


$(document).on('click','#chat_list .messageCols',function() 
{
    li_obj = $(this);
    current_conversation_id = li_obj.attr('data-sid');
    openChatDetail(current_conversation_id)
    .then()
    .catch(error=>{console.log(error)});
});

$(document).on('click','#send-message',sendMessage);

var lastScrollTop = 0;
$('#chat_body').scroll(function(event){
    var st = $(this).scrollTop();
    if (st > lastScrollTop)
    {
       // downscroll code
       // console.log('down');
    } 
    else 
    {
        // upscroll code
        if (st == 0 && (current_conversation_last_fetched_index > fetch_messages_count || current_conversation_hasPrevPage)) 
        {
            displayOverlay();
            last = 1;
            message_index = current_conversation_last_fetched_index - fetch_messages_count;
            current_conversation.getMessages(fetch_messages_count,message_index)
            .then(
                messages =>
                {
                    messages_display(messages)
                    .then(result=>{removeOverlay();})
                    .catch(error=>{console.log(error)});
                }
            )
            .catch(error=>{console.log(error)});
            // alert('top of the div');
        }
       // console.log('up');
    }
    lastScrollTop = st;
});


function start_new_conversation() 
{
    /* user_list */
    user_identity = $('#detail_section select[name="user_id"]').val();
    if(user_identity == undefined)
    {
        alertMessage(0,'Please select user');
        return;
    }

    chat_message = $('#detail_section textarea[name="chat_new_message"]').val();
    if(chat_message == '')
    {
        alertMessage(0,'Please enter message to send');
        return;
    }

    /* check whether conversation exits with selected user*/
    if(identity < user_identity)
    {
        uniqueName = identity+'_'+user_identity;
    }
    else
    {
        uniqueName = user_identity+'_'+identity;
    }
    displayOverlay();
    client.getConversationByUniqueName(uniqueName)
    .then(
            function(conversation_exits)
            {
                current_conversation_id = conversation_exits.sid;
                current_conversation = conversation_exits;
                sendMessage(chat_message);
                openChatDetail(current_conversation_id)
                .then(
                    result=>{
                        removeOverlay();
                        console.log(result);
                    }
                );
            },
            function (conversation_doesnot_exits) 
            {
                // switch the user identities and check again for conversation
                uniqueName = uniqueName.split('_').reverse().join('_');
                client.getConversationByUniqueName(uniqueName)
                .then(
                        function(conversation_exits)
                        {
                            current_conversation_id = conversation_exits.sid;
                            current_conversation = conversation_exits;
                            sendMessage(chat_message);
                            openChatDetail(current_conversation_id)
                            .then(
                                result=>{
                                    removeOverlay();
                                    console.log(result);
                                }
                            );
                        },
                        function (conversation_doesnot_exits)
                        {
                            /*
                                conversation does not exists with both the combination of user identities
                                Create new converstion
                            */
                            create_new_conversation(uniqueName);

                        }
                    );
            }
        );
}


function create_new_conversation(uniqueName)
{
    // user_identity = $.grep(uniqueName.split('_'), function(n) {
    //   return n != identity;
    // })[0];
    user_identity = $('#detail_section select[name="user_id"]').val();
    user_name = $('#detail_section select[name="user_id"] option:selected').text();
    new_message = $('#detail_section #new_message_html textarea[name="chat_new_message"]').val();
    
    client.getUser(user_identity)
    .then(
          user=>
          {            
            console.log(user);
            client.createConversation()
            .then(
                    conversation=>
                    {
                        attributes = {};
                        attributes[identity] = client.user.attributes;
                        attributes[user_identity] = user.attributes;
                        current_conversation_id = conversation.sid;
                        conversation.updateAttributes(attributes)
                        .then(
                              conversation=>
                              {
                                conversation.updateUniqueName(uniqueName)
                                .then(
                                        conversation=>
                                        {
                                            conversation.join()
                                            .then(
                                                conversation=>
                                                {
                                                    conversation.add(user_identity)
                                                    .then(
                                                          participant=> 
                                                          {
                                                            conversation.sendMessage(new_message)
                                                            .then(
                                                                    last_message_index => 
                                                                    {
                                                                        openChatDetail(current_conversation_id)
                                                                        .then(
                                                                            result=>{
                                                                                $('#detail_section select[name="user_type"]').val(3);
                                                                                $('#detail_section select[name="user_type"]').trigger('change');
                                                                                $('#detail_section #new_message_html textarea[name="chat_new_message"]').val('');
                                                                                $('#detail_section select[name="user_id"] option:selected').removeAttr('selected');
                                                                                removeOverlay();
                                                                                console.log(result);
                                                                            }
                                                                        )
                                                                        .catch(error => {
                                                                            console.log(error);
                                                                        });
                                                                    }
                                                                )
                                                            .catch(error => {
                                                                console.log(error);
                                                                conversation.delete();
                                                            });
                                                          }
                                                    )
                                                    .catch(error => {
                                                        console.log(2);
                                                        conversation.delete();
                                                    });
                                                }
                                            )
                                            .catch(error => {
                                                console.log(3);
                                                conversation.delete();
                                            });
                                        }
                                )
                                .catch(error => {
                                    console.log(4);
                                    conversation.delete();
                                });
                              }
                        )
                        .catch(error => {
                            console.log(error);
                            conversation.delete();
                        });
                        
                        
                    }
            )
            .catch(error => {
                console.log(error);
                conversation.delete();
            });
          }
    )
    .catch(error=>{console.log(1);});
}

function openChatDetail(current_conversation_id)
{
    return new Promise((resolve,reject)=>
    {
        $('#new_message_html').addClass('d-none');
        client.getConversationBySid(current_conversation_id)
        .then(
                conversation=>
                {
                    current_conversation = conversation;
                    user_identity = $.grep(conversation.uniqueName.split('_'), function(n) {
                      return n != identity;
                    })[0];
                    last_msg_date = get_date(conversation.lastMessage.dateCreated);
                    last_msg_time = get_time(new Date(conversation.lastMessage.dateCreated));
                    user_attributes = conversation.attributes[user_identity];
                    chat_panel = chat_panel_heading;
                    chat_panel = chat_panel.replace('{username}',user_attributes.name);
                    chat_panel = chat_panel.replace('{last_message_date}',last_msg_date);
                    chat_panel = chat_panel.replace('{last_message_time}',last_msg_time);
                    chat_panel = chat_panel.replace('{user_email}',user_attributes.email==""?'-':user_attributes.email);
                    $('#chat_panel #chat_panel_heading').html(chat_panel);
                    $('#chat_panel').removeClass('d-none');
                    conversation_messages(conversation)
                    .then(
                          result=>{
                            // var d = $('#chat_panel #chat_body');
                            // d.scrollTop($(document).find('#chat_body').children(':last').position().top);
                            scrollToLastMessage();
                            $('#'+current_conversation_id).find('.unread_count').text('{unread_count}');
                            $('#'+current_conversation_id).find('.unread_count').addClass('d-none');
                            resolve(true);
                          }
                    )
                    .catch(error=>{reject(error)});
                }
        )
        .catch(error=>{
            reject(error);
        });
    });
}

function conversation_messages(conversation)
{
    $('#chat_body').html('');
    return new Promise((resolve,reject)=>
    {
        // console.log(conversation);
        conversation.getMessages(fetch_messages_count)
        .then(
                messages=>
                {
                    last = 0;
                    messages_display(messages)
                    .then(
                        last_message_index=>
                        {
                            conversation.advanceLastReadMessageIndex(last_message_index)
                            .then(resolve(last_message_index));
                            // console.log(result);
                            // resolve(result)
                        })
                    .catch(error=>{console.log(error)});
                    // resolve(true);
                }
        )
        .catch(error=>{reject(error)});
    });
}

function messages_display(messages) 
{
    return new Promise((resolve,reject)=>
    {
        items = messages.items;
        current_conversation_hasNextPage = messages.hasNextPage;
        current_conversation_hasPrevPage = messages.hasPrevPage;        
        var messages_arr = [];
        messages_html = '';
        if(items.length > 0)
        {
            items.forEach(function(message) 
            {
                current_conversation_last_fetched_index = message.index;
                messages_arr.push(
                    format_message(message)
                    .then(result=>{messages_html +=result;})
                    .catch(error=>{console.log(error)})
                )
            });            
        }
        else
        {
            //empty conversation
            reject(false);
        }

        Promise.all(messages_arr)
        .then(result=>{
            $('#chat_body').prepend(messages_html);
            resolve(current_conversation_last_fetched_index);
        })
        .catch(error=>{console.log(error)});
    })
}

function format_message(message)
{
    return new Promise((resolve,reject)=>{        
        dateUpdated = preview_chat_date(message.dateUpdated);
        if(message.author == identity)
        {
            html = '<div class="message-tag" data-index="'+message.index+'">'+
                        '<div class="message-right">'+
                            '<span class="message-time">'+dateUpdated+'</span>'+
                            '<p>'+message.body+'</p>'+
                        '</div>'+
                    '</div>';
        }
        else
        {
            html = '<div class="message-tag" data-index="'+message.index+'">'+
                        '<div class="message-left">'+
                            '<span class="message-time">'+dateUpdated+'</span>'+
                            '<p>'+message.body+'</p>'+
                        '</div>'+
                    '</div>';
        }        
        resolve(html);
    })
}


function sendMessage(new_message) 
{
    if($.type(new_message) == 'object')
    {
        new_message = $('#chat_message').val();
    }
    // current_conversation = client.getConversationBySid(current_conversation_id);
    // console.log(new_message);
    // console.log(current_conversation_id);
    if(new_message == '')
    {
        alertMessage(0,'Please enter message to reply!');
        return;
    }

    client.getConversationBySid(current_conversation_id)
    .then(
            conversation=>
            {
                conversation.sendMessage(new_message).
                    then(
                            last_message_index => 
                            {
                                conversation.advanceLastReadMessageIndex(last_message_index);
                                $('#chat_message').val('');
                            }
                        ).catch(error=>{console.log(error);});
            }
        )
    .catch(error=>{console.log(error);});
}

function updateNewMessage(new_message) 
{
    new_message_conversation_id = new_message.conversation.sid;
    if(current_conversation_id == new_message_conversation_id)
    {
        //then new message to chat
        // format_message(new_message);
        format_message(new_message)
        .then(
            result=>
            {
                $('#chat_body').append(result);
                scrollToLastMessage();
                updateChatList();
            }
        )
        .catch(error=>{console.log(error)})
    }
    else
    {
        updateChatList();
        new_conversation_message_index = new_message.index;
        new_conversation_last_read_messsage_index = new_message.conversation.lastReadMessageIndex;
        unread_messages = new_conversation_message_index - new_conversation_last_read_messsage_index;
        console.log(unread_messages);
        $('#'+new_message_conversation_id).find('.unread_count').text(unread_messages);
        // $('#unread_count'+new_message_conversation_id).text(unread_messages);
        // if($('#unread_count'+new_message_conversation_id).parent().hasClass('d-none'))
        // {
        //     $('#unread_count'+new_message_conversation_id).parent().removeClass('d-none');
        // }
    }
}

function displayOverlay(){
    return true;
}
function removeOverlay (){
    return true;
}

function scrollToLastMessage() 
{
    $('#chat_body').scrollTop($("#chat_body")[0].scrollHeight);
}
function preview_chat_date(datetime)
{
    today_date = get_date();
    yesterday_date = get_yesterday();
    preview_date = get_date(datetime);
    currentYear = new Date().getFullYear();

    /*
        if today's date and preview date are same then send 
        only time.

        if preview date is yesterday's date then show yesterday

        if preview date's year isequal to current year then show month date i.e Feb 15

        else show 15 Feb 22
    */

    if (today_date == preview_date)
    {
        return get_time(datetime);
    }
    else if (yesterday_date == preview_date)
    {
        return 'Yesterday';
    }
    else if(preview_date.indexOf(currentYear) != -1)
    {
        return preview_date.split(',')[0];
    }
    else
    {
        return preview_date;
    }
}

function get_date(datetime='')
{
    var d = datetime == ''? new Date():new Date(datetime);
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var date = month[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();
    return date;
}

function get_yesterday()
{
    var d = new Date();
    d.setDate(d.getDate() - 1);
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var date = month[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();
    return date;
}

function get_time(datetime)
{
    var time = datetime.toLocaleTimeString().toLowerCase();
    time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

    if (time.length > 1) 
    { // If time format correct
        time = time.slice (1);  // Remove full string match value
        time.pop();
        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
        time[0] = +time[0] % 12 || 12; // Adjust hours
    }
    return time.join (''); // return adjusted time or original string
}


/* search section */
function filterObjectProperties(obj, filtercb){
    var ret = {};
    for(var p in obj)
        if(obj.hasOwnProperty(p))
            if(filtercb(obj[p]))
                ret[p] = obj[p];
    return ret;
}

$('#search-bar').on('keyup',function(event)
{
    search_text = $(this).val().toLowerCase();

    var newObj = filterObjectProperties(users_array, function(val)
    {
        name = val.name.toLowerCase();
        if(name.indexOf(search_text) != -1)
        {
            return val;
        }
        return false;
    });

    if(Object.keys(newObj).length > 0)
    {
        // $('')
        $('#chat_list').find('.messageCols').addClass('d-none');
        $.each(newObj,function(index,value) 
        {
            // console.log(index);
            // console.log(value.sid);
            $('#'+value.sid).removeClass('d-none');
        })
    }
    // console.log(newObj);
})