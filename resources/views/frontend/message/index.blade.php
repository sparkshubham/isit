
@extends('frontend.layouts.app')
@section('content')

<section class="main-wrapper">             
        <div class="container">
           <div class="messages-part " id="messages-part-windows">
            <div class="sidebar-group">

                <!-- Chats sidebar -->
                <div id="chats" class="sidebar active">
                    <header>
                        <div class="usericon">
                        
                        @if(Auth::user()->profile_picture)
                            <img src="{{$path.Auth::user()->profile_picture}}" class="rounded-circle" alt="image">
                            <input type="hidden" id="logged_inuser_profie" value="{{$path.Auth::user()->profile_picture}}">
                        @else
                        @php $profile_url = url('/front/images/placeholder-user.png');@endphp
                          <img height="45px" width="45px" src="{{$profile_url}}" alt="Lock">
                        @endif
                            
                        </div>
                        <span class="chat-title">Chats</span>
                        <ul class="list-inline">
                            <li class="list-inline-item" title="" data-original-title="New group">
                                <a class="btn" href="{{route('pending-request')}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23.5" height="19.533" viewBox="0 0 23.5 19.533">
                                        <g id="Icon_feather-users" data-name="Icon feather-users" transform="translate(-0.083 -1.717)">
                                          <path id="Path_12119" data-name="Path 12119" d="M16.833,18.5v-2a4,4,0,0,0-4-4h-8a4,4,0,0,0-4,4v2" transform="translate(0 2)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12120" data-name="Path 12120" d="M12.167,6.5a4,4,0,1,1-4-4,4,4,0,0,1,4,4Z" transform="translate(0.667 0)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12121" data-name="Path 12121" d="M19.667,18.478v-2a4,4,0,0,0-3-3.87" transform="translate(3.167 2.022)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12122" data-name="Path 12122" d="M13.333,2.608a4,4,0,0,1,0,7.75" transform="translate(2.5 0.022)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                        </g>
                                    </svg>    
                                    @if($request_user_count!=0)
                                    <div class="numbercount" >{{$request_user_count}}</div>   
                                    @endif                               
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="btn" title="" href="#" id="group_popup_open">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                        <g id="Icon_feather-plus" data-name="Icon feather-plus" transform="translate(-6.5 -6.5)">
                                          <path id="Path_12200" data-name="Path 12200" d="M18,7.5v16" transform="translate(-2.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                          <path id="Path_12201" data-name="Path 12201" d="M7.5,18h16" transform="translate(0 -2.5)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                            <!-- <li class="list-inline-item d-xl-none d-inline">
                                <a href="#" class="btn text-danger sidebar-close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>
                            </li> -->
                        </ul>
                    </header>

                    @include('frontend.message.user-list')


                </div>
                <!-- ./ Chats sidebar -->
            </div>


            <div class="chat tab-content" id="myTabContent">
                <div class="welcome-part tab-pane fade show active" id="welcome" role="tabpanel" aria-labelledby="welcome-tab">
                        <div class="welcome-content">
                           <img src="{{ asset('front/images/welcome-message.svg') }}" class="img-fluid" alt="Welcome">
                           <h3>Start Conversation with your contacts</h3>

                        </div>
                 </div>
             

                <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="chat-header">
                        <div class="chat-header-user">
                            <button class="btn back-btn" data-toggle="tooltip" title="" type="button" data-original-title="Back Button">
                                <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><path fill="#111" d="M8.5,12.8l5.7,5.6c0.4,0.4,1,0.4,1.4,0c0,0,0,0,0,0c0.4-0.4,0.4-1,0-1.4l-4.9-5l4.9-5c0.4-0.4,0.4-1,0-1.4c-0.2-0.2-0.4-0.3-0.7-0.3c-0.3,0-0.5,0.1-0.7,0.3l-5.7,5.6C8.1,11.7,8.1,12.3,8.5,12.8C8.5,12.7,8.5,12.7,8.5,12.8z"/></svg>                              
                            </button>
                            <figure class="avatar">
                                <img id="chat_user_profile_pic" src=" {{ asset('front/images/placeholder-user.png') }} " class="rounded-circle" alt="image">
                            </figure>
                            <div>
                                <h5 id="chat_user_name"></h5>
                                <!-- <small class="online-status" id="chat_user_active_status">Online</small> -->
                                <!-- <small class="text-success">
                                    <i>writing...</i>
                                </small> -->
                            </div>
                        </div>
                        <div class="chat-header-action">
                            <ul class="list-inline">
                                <!-- <li class="list-inline-item d-xl-none d-inline">
                                    <a href="#" class="btn btn-outline-light mobile-navigation-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                                    </a>
                                </li> -->
                                <li class="list-inline-item"  id="video_call_li"data-toggle="tooltip" title="" data-original-title="Video call">
                                    <a href="#" class="btn audio_video_call_intiate" id="" onclick="call_intite('V')" data-type="video">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                    </a>
                                </li>
                                <li class="list-inline-item hide" data-toggle="tooltip" title="" id="voice_call_li" data-original-title="Voice call">
                                    <a href="#" class="btn audio_video_call_intiate" onclick="call_intite('A')" data-type="audio">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div  class="chat-body scrollbar-outer" id="chat_body" style="overflow: hidden; outline: currentcolor none medium;" tabindex="1">
                        <div class="messages"  id="chat_html">
                           
                        </div> 
                    </div>      

                    <div class="chat-footer">
                        <form    role="form" id="sendMessage" enctype="multipart/form-data">
                            <div class="chat-type-message-input commenterinput">
                            <textarea style="height:40px;"   id="text_message_chat"  class="form-control w-100  textarea_comment" type="text" placeholder="Type your message here"  ></textarea>
                                           
                                <!-- <input     id="text_message_chat" type="text"  class="form-control w-100  textarea_comment" placeholder="Type your message here"> -->
                                <button   type="submit" id="send-message-user" class="btn hide"  title="Send">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18.667" height="16" viewBox="0 0 18.667 16"><path id="send" d="M2.009,19l18.658-8L2.009,3,2,9.222,15.333,11,2,12.778Z" transform="translate(-2 -3)" fill="#e85922"/></svg>
                                      
                                </button>                                
                                <button class="attached_file_chat btn " id="file_send_in_chat" >
                                    <img  src="{{asset('front/images/paperclip-solid.svg') }}" alt=""></img>
                                    <input type="file" id="formInputFile" name="formInputFile"   accept="video/*,audio/*,image/*">   
                                    <input type="hidden" id="globalFileTypeCheck" name="globalFileTypeCheck" />   
                                </button>
                            </div>
                 
                            <div class="form-buttons">
                                <!-- <div class="attached_file_chat btn">
                                    <img  src="{{asset('front/images/paperclip-solid.svg') }}" alt=""></img>
                                    <input type="file" id="formInputFile" name="formInputFile"   accept="video/*,audio/*,image/*">   
                                    <input type="hidden" id="globalFileTypeCheck" name="globalFileTypeCheck" />   
                                </div> -->
                            
                                <button class="btn btn-light hide" onclick="audioPopupOn()"  data-toggle="tooltip" title=""  id="testPagintion" type="button" data-original-title="Add files">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18">
                                        <g id="Icon_feather-mic" data-name="Icon feather-mic" transform="translate(-3)">
                                          <path id="Path_12124" data-name="Path 12124" d="M9,.75A2.25,2.25,0,0,0,6.75,3V9a2.25,2.25,0,0,0,4.5,0V3A2.25,2.25,0,0,0,9,.75Z" fill="none" stroke="#e85922" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12125" data-name="Path 12125" d="M14.25,7.5V9A5.25,5.25,0,1,1,3.75,9V7.5" fill="none" stroke="#e85922" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12126" data-name="Path 12126" d="M9,14.25v3" fill="none" stroke="#e85922" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                          <path id="Path_12127" data-name="Path 12127" d="M6,17.25h6" fill="none" stroke="#e85922" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                        </g>
                                      </svg>                                  
                                </button>
                                
                            </div>
                        </form>
                    </div>
                </div>
               
                
            </div>

            
            


           </div>
        </div>
        


        @include('frontend.message.chat-room')
        
        @include('frontend.message.audio-recorder')

    </section>

    <div class="loading hide" id="send_message_loader"></div>
@endsection

