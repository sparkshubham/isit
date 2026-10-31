

var post_id_comment='';
var orderBy=false;

var pageReloadOnrecord=false;
var recordingobj={};
function likeUnlikePost(post_id,user_id)
    {
        var data ={
            post_id:post_id,
            user_id:user_id
        };
        var liked_val= $('#liked_value_'+post_id).val()
        if(liked_val==1)
        {
            var url = 'unlike-post';
            var url = baseurl+'/unlike-post';
            $('#liked_value_'+post_id).val(0)
            $('#like_unlike_'+post_id).text('Like')
            $('#like_unlike_css'+post_id).removeClass('liked_icon')

            var count_like= $('#data-total-like_'+post_id).val();
            count_like--;
            $('#data-total-like_'+post_id).val(count_like);
            $('#total_count_like_'+post_id).text(count_like+' Likes');
            if(count_like==1)
            {
            $('#total_count_like_'+post_id).text(count_like+' Like');
            }
        }
        else
        {
            var url = 'like-post';
            var url = baseurl+'/like-post';
            $('#liked_value_'+post_id).val(1);
            $('#like_unlike_'+post_id).text('Liked')
            $('#like_unlike_css'+post_id).addClass('liked_icon')

            // liked_icon
            var count_like= $('#data-total-like_'+post_id).val();
             count_like++;
            $('#data-total-like_'+post_id).val(count_like);
            $('#total_count_like_'+post_id).text(count_like+' Likes');
            if(count_like==1)
            {
            $('#total_count_like_'+post_id).text(count_like+' Like');
            }
            
            // console.log($('#total_count_like_'+id).html());

        }
        var methodType='POST';
        callajax(url,methodType,data)
    }


    function likelistmodal(id)
    {
        $('#totalLikesList').html('');
        var data ={
            post_id:id,
        };
       
            var url = baseurl+'/like-user-list';
            // var url = `{{ url('front-panel/like-user-list') }}`; 
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
                $('#totalLikesList').html(data.htmlData);
                $('#totalLikesList').modal('show');
                $('#hidden_post_id').val(id)
                
         
                },
                error: function (data) {


                }
            });

    }

    function followUnfollowUser(id)
    {
           
            var follow_val= $('#follow_val_'+id).val()
            var search_bar_check=$('#search_bar_check').val();
            var public_visible=$('#public_visible_'+id).val();

            var data ={
                follower_id:id,
                public_visible:public_visible
            };
            var totalfollowers=$('#follower_count').val();
            if(search_bar_check==1 && follow_val==1)
            {
                return false;
            }
            if(follow_val==1)
            {
                var url = baseurl+'/unfollow-user';
                $('#follow_val_'+id).val(0)
                 var img_url=baseurl+"/front/images/follow.svg";
                $('#btn_txt_follow_unfollow_'+id).text('Follow')
                $('#img_follow_unfollow'+id).attr('src',img_url);
                

                if(search_bar_check==1)
                {
                    $('#btn_txt_follow_unfollow_'+id).addClass('btn-secondary');
                    $('#btn_txt_follow_unfollow_'+id).text('Follow')
                }
                if(public_visible==0)
                {
                }else{
                    totalfollowers--
                }
                $('#follower_count').val(totalfollowers);
                $('#total_follow_count').html(totalfollowers)
            }
            else
            {  
                var url = baseurl+'/follow-user';
                $('#follow_val_'+id).val(1);
                var img_url=baseurl+"/front/images/unfollow-user.svg";
                $('#btn_txt_follow_unfollow_'+id).text('Unfollow')

                $('#img_follow_unfollow'+id).attr('src',img_url);

                if(search_bar_check==1)
                {
                    $('#btn_txt_follow_unfollow_'+id).removeClass('btn-secondary');
                    $('#btn_txt_follow_unfollow_'+id).text('Following')
                }
                if(public_visible==0)
                {
                    $('#btn_txt_follow_unfollow_'+id).text('Requested')
                    $('#btn_txt_follow_unfollow_'+id).html('Requested')
                    $('#btn_txt_follow_unfollow_'+id).prop('disabled', true);
                }
                else{
                    totalfollowers++
                }
                $('#follower_count').val(totalfollowers);
                $('#total_follow_count').html(totalfollowers)
            }
            var methodType='POST';
            callajax(url,methodType,data)
    }


    function commentOnpost(id,user_id)
    {
        var comment=  $('#comment_value_'+id).val();
        // if(!comment)
        // {
        //     toastr.error('Enter COmment Here');
        //     return false;
        // }
        var data ={
            post_id:id,
            user_id:user_id,
            comment:comment,
        };
        $('#comment_value_'+id).val('');
        var url = baseurl+'/comment-post';
        var methodType='Post';
        // callajax(url,methodType,data)
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
                // $('#comment_list_'+id).html(data.htmlData);
                getCommentListBypost(id,orderBy);
                var count_like= $('#data-total-comment_'+id).val();
                count_like++;
                $('#data-total-comment_'+id).val(count_like);
                $('#total_count_comment_'+id).text(count_like+' Comments');
                if(count_like==1)
                {
                    $('#total_count_comment_'+id).text(count_like+' Comment');
                }
              
            },
            error: function (data) {
            }
        });
      
    }

    function replycommentOnpost(id,parent_comment_id,user_id)
    {
       var comment= $('#comment_value_reply_'+parent_comment_id).val();
        var data ={
            post_id:id,
            user_id:user_id,
            comment:comment,
            comment_id:parent_comment_id
        };
        // if(!comment)
        // {
        //     toastr.error('Enter COmment Here');
        //     return false;
        // }
      
        var url = baseurl+'/comment-post';
        var methodType='Post';
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        var formData = data;
        var type = methodType;
        var ajaxurl = url;
        $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
                $('#comment_value_reply_'+parent_comment_id).val('');
                getReplyCommentList(parent_comment_id,id)
                $('#CommentReply'+parent_comment_id).modal('show');
                return false;
            },
            error: function (data) {
            }
        });
    
       
    }
    

    function getCommentListBypost(id)
    {
        post_id_comment=id;
            var data ={
                post_id:id,
                orderBy:orderBy,
            };
            var url = baseurl+'/comment-list';
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
                   
                    $('#commentongolive').html(data.agora_comment_html);
                    $('#comment_list_'+id).html(data.htmlData);
                    $('#live_user_count_particpant').html(data.response.live_user_count)
                    var objDiv = document.getElementById("comment_list_"+id);
                    // objDiv.scrollTop = objDiv.scrollHeight;
                    $("textarea").each(function () {
                        this.setAttribute("style", "height:40px;");
                      }).on("input", function () {
                        this.style.height = "auto";
                        this.style.height = "40px";
                      });

                      
                            $('.comments_val_class').prop('disabled',true);
                            $('.comments_val_class').addClass('disable-comment')
                         

                            
                
                },
                error: function (data) {
                }
            });

         
    }


    function getReplyCommentList(comment_id,post_id)
    {
        //   $('#comment_list_reply'+comment_id).html('');
            var data ={
                comment_id:comment_id,
                post_id:post_id,
            };
            var url = baseurl+'/replied-comment-list';
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
                    $('#comment_list_reply'+comment_id).html(data.htmlData);
                    var objDiv = document.getElementById("comment_list_reply"+comment_id);
                    objDiv.scrollTop = objDiv.scrollHeight;
                    $('#CommentReply'+comment_id).modal('show');
                    $("textarea").each(function () {
                        this.setAttribute("style", "height:40px;");
                      }).on("input", function () {
                        this.style.height = "auto";
                        this.style.height = "40px";
                      });
                      
                      $('.comments_val_class').prop('disabled',true);
                      $('.comments_val_class').addClass('disable-comment')

                },
                error: function (data) {
                }
            });
    }


    

    function publicVisblityChange(value)
    {
            var data ={
                is_public_visibility :value,
            };
            var url = baseurl+'/public-visibility-change';
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
                    $('#is_public_visibility').val(data.data.is_public_visibility)
                    toastr.success(data.message);
                },
                error: function (data) {
                }
            });
    
    }



    function deletePost(id)
    {
        var data ={
            post_id:id,
        };
        var msg="Are you sure you want to delete this post?"
        // var result = confirm(msg,id);
        // if (result) {
        //     var url = baseurl+'/delete-post';
        //     var methodType='Post';
        //     callajax(url,methodType,data)
        //     $('#post_hide_show_'+id).addClass('hide');
        //     var s = window.location.pathname
        //     var final = s.substr(s.lastIndexOf('/') + 1);

        //     if(final=='post-board'||final=='home')
        //     { 
        //     }
        //     else
        //     {
        //         location.reload();
        //     }
        // }

        swal({
            title:'Are you sure you want to delete this post?',
            // text: result,
            icon: "warning",
            buttons: [
              'No, cancel it!',
              'Yes, I am sure!'
            ],
            dangerMode: true,
          }).then(function(isConfirm) {
                    if (isConfirm) {
                        var url = baseurl+'/delete-post';
                    var methodType='Post';
                    callajax(url,methodType,data)
                    $('#post_hide_show_'+id).addClass('hide');
                    var s = window.location.pathname
                    var final = s.substr(s.lastIndexOf('/') + 1);

                    if(final=='post-board'||final=='home' ||final=='my-profile')
                    { 
                    }
                    else{
                        location.reload();
                    }
            }else{      
               return false;
            }
          });

    }
    

    // function savedPost(id,user_id)
    // {
    //     var data ={
    //         post_id:id,
    //         user_id:user_id
    //     };
    //     var url = baseurl+'/save-post';
    //     var methodType='Post';
    //     callajax(url,methodType,data)
    //     // $('#post_hide_show_'+id).addClass('hide');
    // }
    // function unsavedPost(id,user_id)
    // {
       
    //     // $('#post_hide_show_'+id).removeClass('hide');
    // }


    function saveUnsavepost(id,user_id)
    {
        var saved_val= $('#saved_post_'+id).val();
            if(saved_val==1)
            {
                $('#saved_post_'+id).val(0);
                var data ={
                    post_id:id,
                    user_id:user_id
                };
                var url = baseurl+'/remove-save-post';
                var methodType='Post';
                callajax(url,methodType,data)
                $('#saved_post_text'+id).html('Save post')
                 var img_url=baseurl+"/front/images/save.svg";
                $('#saved_post_img'+id).attr('src',img_url);
            }
            else{
                $('#saved_post_'+id).val(1);
                var data ={
                    post_id:id,
                    user_id:user_id
                };
                var url = baseurl+'/save-post';
                var methodType='Post';
                callajax(url,methodType,data)
                $('#saved_post_text'+id).html('Unsave post')
                var img_url=baseurl+"/front/images/un-save.svg";
                $('#saved_post_img'+id).attr('src',img_url);


            }

       
    }

    
    function removeSaveEvent(id,user_id)
    {
        var data ={
            event_id:id,
            user_id:user_id
        };

        var patternAColor ='#fff';
        var patternBColor ='none';
        // Load patterns
        var eventVal=$('#is_saved_event_'+id).val();
        if(eventVal==1)
        {
            var url = baseurl+'/remove-save-event';
            $('#is_saved_event_'+id).val(0);
            $('#evnt_txt_'+id).text('Save the Date');
            $("svg").each(function(){
                $(".bookmark_"+id).css({ fill: patternBColor });
                $('#hide_show_event_'+id).addClass('hide');
                $('#hide_show_event_'+id).remove();

                var length=$('.save-event-count').length;
                var eventlen=length;
                if(eventlen<=0)
                {
                    var path=baseurl+'/front/images/nodata.svg';
                    html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No saved event found</p></div>';
                    $('#saved_event_list').html(html);
                }
            });
           
        }
        else
        {
            var url = baseurl+'/save-event';
            $('#is_saved_event_'+id).val(1);
            $('#evnt_txt_'+id).text('Unsave the Date');    
            $("svg").each(function(){
                $(".bookmark_"+id).css({ fill:patternAColor });
            });   

        }

        var methodType='Post';
        callajax(url,methodType,data)

    }


    function blockUnblockUser(block_user_id,user_id)
    {
        var valueB =$('#blocked_user_id_'+block_user_id).val();
        if(valueB==2){
            toastr.error('You have already blocked this user');
            
            
        }else{
            $('#blocked_user_id_'+block_user_id).val(2);
            var data ={
                block_user_id:block_user_id,
                user_id:user_id,
                block_by:user_id
            };

            swal({
                title:'Are you sure want to block user?',
                // text: result,
                icon: "warning",
                buttons: [
                  'No, cancel it!',
                  'Yes, I am sure!'
                ],
                dangerMode: true,
              }).then(function(isConfirm) {
                if (isConfirm) {
                  
                    var url = baseurl+'/block-user';
                    var methodType='Post';
                    // callajax(url,methodType,data)
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
                            toastr.success(data.message);
                            location.reload();
                            // return data;
                        },
                        error: function (data) {
                            toastr.error(data.responseJSON.message);
                            return data;
                
                        }
                    });
                   
                }else{      
                    $('#blocked_user_id_'+block_user_id).val(1);
                }
              });

            // var result = confirm("Are you sure want to block user?");
            // if (result) {
          
            // }
            // else
            // {

               
            // }
        }
    }
    
    function unblockUser(id)
    {
        // $('#blocked_userss').html('');
        var data ={
            unblock_user_id:id
        };
        var url =  baseurl+'/unblock-user';
        var methodType='Post';
        callajax(url,methodType,data)
        var length=$('.block-user-count').length;
        var blockLen= length-1
        if(blockLen==0)
        {
                var path=baseurl+'/front/images/nodata.svg';
                html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No user found</p></div>';
                $('#blocked_userss').html(html);
        }
        $('#hide_show_block'+id).remove();
        
    }

   
    

    // $('#dropdownMenuButton1').addClass('hideen');
    $(".comment-form").submit(function(e) {
        e.preventDefault();
    });
    



    ///product remove

    function deleteProduct(id)
    {
        var data ={
            product_id:id,
        };
        // var result = confirm("Are you sure you want to delete this product?");
        swal({
            title:'Are you sure you want to delete this product?',
            // text: result,
            icon: "warning",
            buttons: [
              'No, cancel it!',
              'Yes, I am sure!'
            ],
            dangerMode: true,
          }).then(function(isConfirm) {
            if (isConfirm) {
                var url = baseurl+'/delete-product';
                var methodType='Post';
                callajax(url,methodType,data)
               
                $('#hide_show_product_'+id).addClass('hide');
                var length=$('.store-count-product').length;
                var blockLen= length-1
                if(blockLen==0)
                {
                        var path=baseurl+'/front/images/nodata.svg';
                        html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No product found</p></div>';
                        $('#store_list').html(html);
                }
            }else{      
               return false;
            }
          });
        // if (result) {
           

        // }
       
    }
    


    function editProduct(id)
    {
        $('.help-block').remove();
            var id=id;
            var url = baseurl+'/create-edit-product';
            var data ={
                product_id :id,
            };
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
                     var dataRes= data.data.data;
                     var product_media= data.data.path.product_media;
                     $('#name').val(dataRes.name);
                     $('#product_id').val(dataRes.id);
                     $('#product_type').val(dataRes.product_type);
                     $('#product_category').val(dataRes.category_id);
                     $('#product_description').val(dataRes.description);
                     $('#product_country').val(dataRes.country_id);
                     $('#product_city').val(dataRes.city);
                     $('#product_zipcode').val(dataRes.zip_code);
                     $('#product_price').val(dataRes.price);
                     $('#currency').val(dataRes.currency);

                     $('#product_submit_btn').text('Update');
                     $('#add_modalLabel_product').text('Update Ad');
                        if(dataRes.product_type==2){
                            $("#sell").prop('checked', true);
                        }else{
                            $("#buy").prop('checked', true); 
                        }
                        if(dataRes.price_type==2){
                            $("#for-money").prop('checked', true);
                            $('#price_box_store').removeClass('hide');
                        }else{
                            $("#for-free").prop('checked', true); 
                            $('#price_box_store').addClass('hide');
                        }
                     $('#add_modal_store').modal('show');
                     $('#upload_products').html('')
                      var thumbanilData = dataRes.get_media;
                      var htmldata=''
                      
                      $.each(thumbanilData, function(key,val) {
                            var file_path= product_media+val.file_name; 
                            $('#product-file-input').val(1);
                            var is_video=isImage(val.file_name);
                            var videoFlag=''
                            if(is_video==false)
                            {
                                $('.videoExist').val(1);
                                videoFlag=2
                            }
                            if(val.media_type==2){
                                $('.videoExist').val(1);
                                videoFlag=2
                                file_path=product_media+val.thumb_image_name; 
                            }
                            htmldata+='<li id="image-preview_'+val.id+'" ><a class="cross-icon-prdouct" onclick="removefileProduct('+val.id+')">X<a><div class="image-video-preview" ><img src="'+file_path+'" alt=""></img></div><input type="hidden" video="'+videoFlag+'" id="remove_input_product'+val.id+'"  value="' + val.file_name + '" data-val="1"></li>';
    
                        });
                        // $('form').append()  ;   
                        $('#upload_products').append(htmldata+'<input type="hidden" id="legthFile" value="'+thumbanilData.length+'">')
                       
                        if(thumbanilData.length>=5)
                        {
                          $('#dropzone_prdouct').addClass('hide');
                        }
                        

                },
                error: function (data) {
                }
            });

    }


    
    
   

// document.getElementById("product_submit_btn").addEventListener("click", function () {
//     var form = document.getElementById("create-store-form");
//     if($('#product_id').val()!='')
//     {
//         form.submit();
//     }
 
// });

    function removefileProduct(id)
    {
        
        var valueVideo= $('#remove_input_product'+id).attr('video');
        if(valueVideo==2)
        {
            $('.videoExist').val(0);
        }
        $('#remove_input_product'+id).remove();
        $('#image-preview_'+id).remove();
        var legthFile=$('#legthFile').val();
        legthFile--;
        $('#legthFile').val(legthFile);

        if($('#legthFile').val()==0)
        {
            $('#product-file-input').val('');
        }
        $('form').append('<input type="hidden" name="deleted_media_ids[]"  value="'+id+'">')  ;   
        $('#product_submit_btn').prop('disabled', false);
        $('#dropzone_prdouct').removeClass('hide');
    }
    

    function getCurrentTime(date) {
        var hours = date.getHours(),
            minutes = date.getMinutes(),
            ampm = hours >= 12 ? 'PM' : 'AM';
    
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
    
      return hours + ':' + minutes +''+ ampm;
    }
    
    $("input[name=start_time],input[name=end_time]").clockpicker({       
        autoclose: false,
        twelvehour: true,
	    donetext: 'Done',
        placement: 'top',
        align: 'right',
        init: function() { 

            // $('.clockpicker-popover').addClass('Shubha')
            console.log("colorpicker initiated");
        },
        beforeShow: function() {
            console.log("before show");
        },
        afterShow: function() {
            console.log("after show");
        },
        beforeHide: function() {
            console.log("before hide");
        },
        afterHide: function() {
            console.log("after hide");
        },
        beforeHourSelect: function() {
            console.log("before hour selected");
        },
        afterHourSelect: function() {
            console.log("after hour selected");
        },
        beforeDone: function() {
            console.log("before done");
        },
        afterDone: function() {
            jQuery('.clockpicker-popover').css('display', 'none!important');

        }
});



    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    console.log(today);

    $('#date_event').attr('min',today);

    $('#date_event').keypress(function(event){
        event.preventDefault();
    });
    
    $('#start_time_event').keypress(function(event){
        event.preventDefault();
    });
    $('#end_time_event').keypress(function(event){
        event.preventDefault();
    });


$(function() {
    // $('#end_time_event').timepicker();
    // $('#end_time_event').timepicker();
  
    function timeToInt(time) {
      var arr = time.match(/^(0?[1-9]|1[012]):([0-5][0-9])([APap][mM])$/);
      if (arr == null) return -1;
  
      if (arr[3].toUpperCase() == 'PM') {
                if(arr[1]==12){
                    arr[1] = parseInt(arr[1]);
                }else{
                   arr[1] = parseInt(arr[1]) + 12;
                }
      }
      if (arr[3].toUpperCase() == 'AM' &&  arr[1]==12) {
        arr[1] = 00;
      }
      return parseInt(arr[1]*100) + parseInt(arr[2]);
    }
  
    function checkDates() {
      if (($('#start_time_event').val() == '') || ($('#end_time_event').val() == '')) return;
      var start = timeToInt($('#start_time_event').val());
      var end = timeToInt($('#end_time_event').val());

      if ((start == -1) || (end == -1)) {
          alert("Start or end time it's not valid");
      }
       var start_time =start+5;
      if (end < start_time) {
         alert('Event duration must be greater then 5 minutes');
         $('#end_time_event').val('');
      }
  
    }
  
    $('#start_time_event').on('change', checkDates);
    $('#end_time_event').on('change', checkDates);
  });


 

    var getoffset= new Date().getTimezoneOffset()*60;
    var gettimezone=Intl.DateTimeFormat().resolvedOptions().timeZone;
    $('#timezone').val(gettimezone);
    $('#timeoffset').val(Math.abs(getoffset));
    const now = new Date()
    var gmt_time=now.toUTCString()
    $("#gmt_time").val(gmt_time);
    $("#gmt_time_t").val(gmt_time);
    
    $('#global_timezone').val(gettimezone);
   console.log(getCurrentTime(now));

    function reportPost(post_id)
    {
         var post_id=post_id;
         $('#reportPost').modal('show');
         $('#report_post_id').val(post_id);
        //  $("#reportPost"+post_id).addClass('hide'); 
    }


    function editEvent(id)
    {
            var id=id;
            var url = baseurl+'/get-event';
            var data ={
                event_id :id,
                time_zone :gettimezone,

            };
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
                     var html= data.data.html
                     var path=data.data.path.event_media;
                    //  $('#edit_event_data').html(edit_event_data);
                     var dataRes= data.data.event;
                     $('#event_name').val(dataRes.name);
                     $('#event_hidden_id').val(dataRes.id);
                     $('#date_event').val(dataRes.formated_date);
                     $('#start_time_event').val(dataRes.formated_start_time);
                     $('#end_time_event').val(dataRes.formated_end_time);
                     $('#imagePreview').css('background-image', 'url('+path+dataRes.image +')');
                     $('#imagePreview').hide();
                     $('#imagePreview').addClass('update');
                     $('#imagePreview').fadeIn(650);

                     $('#CreateEvent').modal('show');
                     $('#CreateEventLabel').html('Edit Event');
                     $('#create_event_btn').html('Update');
                     
                     
                     $('#imageUpload').val(1);

                     
                },
                error: function (data) {
                }
            });

    }

    function CreateEventModal()
    {
        
        $("#create-event-form")[0].reset();
        $('#event_hidden_id').val('');
        $('#CreateEvent').modal('show');
        $('#CreateEventLabel').html('Create Event');
        $('#create_event_btn').html('Create');
        $('#imagePreview').removeClass('update') 
        $("#imagePreview").css('background-image', 'none');
        $('.help-block').html('')
        
    }

    function deleteEvent(id)
    {
        var data ={
            event_id:id,
        };
        // var result = confirm("");
        swal({
            title:'Are you sure you want to delete this event?',
            // text: result,
            icon: "warning",
            buttons: [
              'No, cancel it!',
              'Yes, I am sure!'
            ],
            dangerMode: true,
          }).then(function(isConfirm) {
            if (isConfirm) {
                var url = baseurl+'/delete-event';
                var methodType='Post';
                callajax(url,methodType,data)
                    // $('#hide_show_product_'+id).addClass('hide');
                 

                    var length=$('.event-count').length;
                    var eventlen= length-1
                    $('#hide_show_event_'+id).addClass('hide');
                    $('#hide_show_event_'+id).remove();
                    if(eventlen<=0)
                    {
                            var path=baseurl+'/front/images/nodata.svg';
                            html='<div class="no_data_found"><img src="'+path+'" alt=""><p>No event found</p></div>';
                            $('#event_list').html(html);
                    }

               
            }else{      
               return false;
            }
          });


        // if (result) {

        // }

          

    }


    function createAdmodalOpen()
    {
      
        $('#create-store-form')[0].reset();
        $("#buy").prop('checked', true); 
        $("#for-money").prop('checked', true);
        $("#product_id").val('');
        $('#product_submit_btn').text('Publish');
        $('#add_modalLabel_product').text('Create Ad');
        $('#add_modal_store').modal('show');
        $('.help-block').remove();
        $('#upload_products').html('')
        $('#legthFile').val(0)
        $('#dropzone_prdouct').removeClass('hide');
        $('#price_box_store').removeClass('hide');
        
        

    }


    function deleteModal(msg,id)
    {
   
            $('.message').html(msg);
            $('#delete-modal').modal('show');
            // return true;
    }


    var followingusers=[];
    var following_list_create_group;
    function followerList()
    {

        var url = baseurl+'/following-list';
        var methodType='POST';
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        // e.preventDefault();
        var data={
            user_id:loggedInId
        }
        
        var formData = data;
        var type = methodType;
        var ajaxurl = url;
        $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
                var html= data.data

                followingusers=data.followingusers;
                following_list_create_group= data.following_list_create_group
                $('#groupUserlist').html(following_list_create_group);
                
               
                
            },
            error: function (data) {
            }
        });

    }
    followerList();
    
    $("#appendBlankparticpant").val()    
    $('#group_popup_open').click(function()
    {    
        followerList();
        $('.help-block').html('');
        $("#appendBlankparticpant option").remove();
        $('input').val('');
        $('#CreateChatRoom').modal('show');
        $('#CreateChatRoom-form')[0].reset();
        $('#imagePreview').removeClass('update') 
        $('.add_remove_group').text('+ Add')
        $('.add_remove_group').addClass('btn-secondary') 
        $("#imagePreview").css('background-image', 'none');
        $("#ChatRoomName").val('')
        $('#group_create_btn').removeClass('hide')
        $('#delete_exit_btn_group').addClass('hide')
        $('#group_created_date_h').addClass('hide')
        $('#group_created_by_h').addClass('hide')
        $('#update_group').addClass('hide')
        $('#participant_count').addClass('hide')
        $('#group_uniqueName').val('')
        $('#ChatRoomName').prop('disabled', false);
        $('#appendBlankparticpant').val('');
        $('#groupUserlist').html(following_list_create_group);
        $('#exit_btn_group').addClass('hide');
        $('#hide_particpant_admin').removeClass('hide');
        $('#imageUpload').prop('disabled',false)
        $('#groupUserlist_always_hide').html('')
        // $("#appendBlankparticpant").append('<option  id="'+loggedInId+'" selected="" value="'+loggedInId+'"/>')
       $('#is_update').val(1)
       $('.help-block').html('')
          

        

    });
    

    $("#CreateChatRoom-form").submit(function(e) {
        e.preventDefault();
    });
    // $("#EditChatRoom-form").submit(function(e) {
    //     e.preventDefault();
    // });
    var groupProfilePic="";
    $(document).on('change','#imageUpload',function(){
        var property = document.getElementById('imageUpload').files[0];
      
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = baseurl+'/upload-file-S3';
        var form_data = new FormData();
        form_data.append("image",property);
        form_data.append("folder",'group');
        readURL(this);
        $('#modal-content-chat').addClass('loader-bg')
        $('#file_uploader').removeClass('hide');
        $.ajax({
          url:url,
          method:'POST',
          data:form_data,
          contentType:false,
          cache:false,
          processData:false,
          beforeSend:function(){

          },
          success:function(data){
            groupProfilePic=data.data.mediaUrl;
            $('#group_Profile_pic').val(groupProfilePic);
            $('#modal-content-chat').removeClass('loader-bg')
            $('#file_uploader').addClass('hide');
            // $('#msg').html(data);
          }
        });
      });

      

      function audioPopupOn()
      {
        recordingblob=true
        $('#audiorecorder').modal('show');
      }


   


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                    $("#imagePreview").addClass("update");
                    $('#imagePreview_edit').css('background-image', 'url('+e.target.result +')');
                    $('#imagePreview_edit').hide();
                    $('#imagePreview_edit').fadeIn(650);
                    $("#imagePreview_edit").addClass("update");
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        



        
$('#copy').on('click', function(event) {
    console.log(event);
    copyToClipboard(event);
  });
  
  // event handler
  function copyToClipboard(e) {
    // alert('this function was triggered');
    // find target element
    var
      t = e.target, 
      c = t.dataset.copytarget,
      inp = (c ? document.querySelector(c) : null);
    console.log(inp);
    // check if input element exist and if it's selectable
    if (inp && inp.select) {
      // select text
      inp.select();
      try {
        // copy text
        document.execCommand('copy');
        inp.blur();
  
        // copied animation
        t.classList.add('copied');
        setTimeout(function() {
          t.classList.remove('copied');
        }, 1500);
      } catch (err) {
        //fallback in case exexCommand doesnt work
        alert('please press Ctrl/Cmd+C to copy');
      }
  
    }
  
  }


        function  removeFile(fileName)
        {
            var script_url = baseurl+'/front/js/twillio/twillio-video.js';
            $("script[src='"+script_url+"']").remove()
        }

        function loadScript(url)
        {
            document.body.appendChild(document.createElement("script")).src = url;
        }

        
// watch live event code start 
        $( "#join-channel" ).click(function(event) {
            is_go_live=false;
            is_scroll=false
            var url = baseurl+'/agora-view';
            var script_url = baseurl+'/front/js/agora/agora-interface.js';
            loadScript(script_url)
            var methodType='get';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            // e.preventDefault();
            var type = methodType;
            var ajaxurl = url;
            var post_id=$(this).attr('post_id')
            var formData={
                post_id:$(this).attr('post_id'),
            }
            post_id_comment=$(this).attr('post_id');
            var event_id=$(this).attr('data-event-id')
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    $('.storyset-section').addClass('d-block-mx-auto')
                    $('#video_stream_go_live').removeClass('hide')
                    $("#broadcast_video").remove();
                    $('#video_stream_go_live').prepend(data.broadcast)
                    var agoraAppId = $('#form-appid').val();
                    var agora_token = $('#form-token').val();
                    var channelName = $('#form-channel').val();
                    var uid = parseInt($("#form-uid").val());
                    // $("#modalFormAgora").modal("hide");
                    $("#broadcast_video").removeClass("hide");
                    $("#post_board_data").addClass("hide");  

                    $('.video_time_hide').addClass('hide');
                    $('.storyset-section').addClass('agora-width-cover');

                    
                    brodcasting_type=2; //user-live
                    initClientAndJoinChannel(agoraAppId, agora_token, channelName, uid,brodcasting_type);
                    // $("#join-channel").attr('disable',true)

                      ///type :1 -> Event,2-> User
                      //is_add:1->add, 2 -> remove 3-> Add and Reset
                      
                    var recToken = $('#form-recToken').val();

                      recordingobj.channelName = channelName;
                      recordingobj.agoraToken = recToken;
                      recordingobj.postId = post_id;
                    var datainfo={
                        is_add:3,
                        type:1,
                        id:event_id,
                    }

                    updateUserLiveCount(datainfo)
                },
                error: function (data) {
                }
            });

            
		});

        $("#watch-live-btn").click(function(){
            is_go_live=false;
            is_scroll=false
            var url = baseurl+'/agora-view';
            var script_url = baseurl+'/front/js/agora/agora-audience-client.js';
            loadScript(script_url)
            var methodType='get';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData={
                post_id:$(this).attr('post_id'),
            }
            post_id_comment=$(this).attr('post_id');
            var event_id=$(this).attr('data-event-id')
            // e.preventDefault();
            var type = methodType;
            var ajaxurl = url;
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    $('#audience_video').remove()
                    $('.storyset-section').addClass('d-block-mx-auto')
                    $('#video_stream_go_live').removeClass('hide')
                    $('#video_stream_go_live').prepend(data.audience)
                    var agoraAppId = $('#form-appid').val(); // set app id
                    var agora_token = $('#form-token').val();
                    var channelName = $('#form-channel').val(); // set channel name
                    var uid = parseInt($("#form-uid").val());
                    // init Agora SDK
                    $('#audience_video').removeClass('hide');
                    $("#post_board_data").addClass("hide");
                    $('.video_time_hide').addClass('hide');
                    
                    console.log(agora_token,agoraAppId,channelName)
                    brodcasting_type=2; //event-live
                    clientCreateAudience(agoraAppId,agora_token,channelName,uid,brodcasting_type);
                    var brodacast_fullName = $('#broadcaster_name').val();
                    var broadcast_img = $('#broadcaster_img').val(); 
                    audinecNameImg(brodacast_fullName,broadcast_img)

                  

                     ///type :1 -> Event,2-> User
                      //is_add:1->add, 2 -> remove 3-> Add and Reset
                      var datainfo={
                        is_add:1,
                        type:1,
                        id:event_id,
                    }
                    updateUserLiveCount(datainfo)
                    $('.storyset-section').addClass('agora-width-cover');
                    
                    
                },
                error: function (data) {
                }
            });
            
          });


// watch live event code end


// function copyToClipboard(element) {
//     var $temp = $("<input>");
//     $("body").append($temp);
//     $temp.val($(element).text()).select();
//     document.execCommand("copy");
//     $temp.remove();
//   }

// live stream user start


          $("#user_join_live").click(function(){
            
            is_go_live=true;
            orderBy=true;
            is_scroll=false
            var other_user_id=$(this).attr('other-user-id');
            var url = baseurl+'/user-go-live';
            var methodType='POST';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            // e.preventDefault();
            var data={
                user_id:other_user_id,
                is_publisher:2,
            }
            var script_url = baseurl+'/front/js/agora/agora-audience-client.js';
            loadScript(script_url)
            var formData = data;
            var type = methodType;
            var ajaxurl = url;
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (data) {
                        var data =data.data;
                        $('#audience_video').remove()
                        $('#video_stream_go_live').prepend(data.audience_html)
                        $('.storyset-section').addClass('d-block-mx-auto')
                        $('.video_time_hide').addClass('hide');
                        var agora_token=data.token
                        channelName=data.channelName
                        var is_live=data.is_live
                        $('.agora-video-text-title').html(data.title)
                        
                        uid= parseInt(loggedInId);
                        $('#audience_video').removeClass('hide');
                        $("#user_profile_data").addClass("hide");
                        if(is_live==1)
                        {
                          brodcasting_type=1; //user-live
                         clientCreateAudience(agoraAppId,agora_token,channelName,uid,brodcasting_type);
                      
                         var brodacast_fullName = $('#broadcaster_name').val();
                         var broadcast_img = $('#broadcaster_img').val();
                         audinecNameImg(brodacast_fullName,broadcast_img)
                        $('.agora-video-text-title').html(data.title)
                        getCommentListBypost(data.postId,orderBy)

                    //type :1 -> Event,2-> User
                      //is_add:1->add, 2 -> remove 3-> Add and Reset
                      var datainfo={
                        is_add:1,
                        type:2,
                        id:other_user_id,
                    }
                    updateUserLiveCount(datainfo)

                        
                         
                        }else{
                                $('#no-local-video').attr("style", "display:flex");
                                $('#no-local-video').html('Broadcaster has already stopped the live streaming.');//broadcast
                        }

                
                },
                error: function (data) {
                }
            });   
            
          });


          $(document).on('click','#user_go_live',function(){
          
            $('#golivemodal').modal('show')
            $('#agora-live-form').trigger('reset');
            $('.help-block').html('');
        })

        

        $("#agora-live-form").on('submit', function(e) {
            e.preventDefault();
            orderBy=true;
            var len = $('#go_live_title').val().length;
            if (len <= 20 && len >= 3) { 
               userGolive();
            }
        });
        // $(document).on('click','#go_live_btn',function(){
           
        // })
        
     

         $("#is_recorded").change(function() {
            var ischecked= $(this).is(':checked');
            if(ischecked){
            $("#is_recorded").val(1);
            }else{
                $("#is_recorded").val(0)
            }
            // alert('uncheckd ' + $(this).val());
        }); 


        function  userGolive()
        {

            is_scroll=false
            is_go_live=true;
            var title=$('#go_live_title').val();
            var is_recorded=$("#is_recorded").val();
            var url = baseurl+'/user-go-live';
            var methodType='POST';
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            // e.preventDefault();
            var data={
                user_id:loggedInId,
                is_publisher:1,
                title:title,
                is_recorded:is_recorded
            }
            if(is_recorded==1){
               pageReloadOnrecord=true;
            }
            var script_url = baseurl+'/front/js/agora/agora-interface.js';
            loadScript(script_url)
            var formData = data;
            var type = methodType;
            var ajaxurl = url;
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (data) {
                        var channelName=data.data.channelName;
                        var broadcast_html=data.data.broadcast;
                        $("#broadcast_video").remove();
                        $('#video_stream_go_live').prepend(broadcast_html)
                        var agora_token=data.data.token;
                        var uid=loggedInId;
                        brodcasting_type=1; //user-live
                        recordingobj.channelName = data.data.channelName;
                        recordingobj.agoraToken = data.data.recToken;
                        recordingobj.postId = data.data.postId;
                        const detailsString = `AppId: ${agoraAppId} \nChannel Name: ${channelName} \nAgora token: ${agora_token}`;
                        console.log(detailsString);
                        var selectedValueBrodcast = $('input[name="broadcast_method"]:checked').val();

                        if(selectedValueBrodcast==1){
                            swal({
                                title: 'Go live Details',
                                text: detailsString,
                                icon: 'info',
                                confirmButtonText: 'ok',
                                dangerMode: true,
                                allowOutsideClick: true,
                                allowEscapeKey: true
                            }).then((result) => {
                                // Call the function after the user clicks the "OK" button
                                if (result) {
                                    var iniData = {
                                        BaseInfo: {
                                          AppId: agoraAppId,
                                          AppToken: agora_token,
                                          UID: loggedInId,
                                          ChannelName: channelName,
                                          RtmpUrl: ''
                                        }
                                      };
                                    
                                      var iniContent = '';
                                      for (var section in iniData) {
                                        iniContent += '[' + section + ']\n';
                                        for (var key in iniData[section]) {
                                          iniContent += key + '=' + iniData[section][key] + '\n';
                                        }
                                        iniContent += '\n';
                                      }
                                    
                                      var blob = new Blob([iniContent], { type: 'text/plain' });
                                    
                                      var a = document.createElement('a');
                                      a.download = 'AgoraObs.ini';
                                      a.href = window.URL.createObjectURL(blob);
                                      a.style.display = 'none';
                                      document.body.appendChild(a);
                                      a.click();
                                      document.body.removeChild(a);
                                      
                                //       $("#post_board_data").addClass("hide");  
                                //       $('.storyset-section').addClass('agora-width-cover');
                                //       //end on postaboard section
                                      
          
                                //       initClientAndJoinChannel(agoraAppId, agora_token, channelName, uid,brodcasting_type);
                                //       $("#broadcast_video").removeClass("hide");
                                //       $("#user_profile_data").addClass("hide");  
                                //       $('.video_time_hide').addClass('hide');
                                //       $('#golivemodal').modal('hide')
                                //       $('.agora-video-text-title').html(data.data.title)
                                //       $('.storyset-section').addClass('d-block-mx-auto')
                                      
                                //       $('#agora_username_broadacaster').html(data.channelName)
                                //       getCommentListBypost(data.data.postId,orderBy)
          
                                //   //type :1 -> Event,2-> User
                                //   //is_add:1->add, 2 -> remove 3-> Add and Reset
                                //       var datainfo={
                                //           is_add:3,
                                //           type:2,
                                //           id:loggedInId,
                                //       }
                  
                                //       updateUserLiveCount(datainfo)
                                
                                }
                            });
                    }else{
                            $("#post_board_data").addClass("hide");  
                            $('.storyset-section').addClass('agora-width-cover');
                            //end on postaboard section
                            

                            initClientAndJoinChannel(agoraAppId, agora_token, channelName, uid,brodcasting_type);
                            $("#broadcast_video").removeClass("hide");
                            $("#user_profile_data").addClass("hide");  
                            $('.video_time_hide').addClass('hide');
                            $('#golivemodal').modal('hide')
                            $('.agora-video-text-title').html(data.data.title)
                            $('.storyset-section').addClass('d-block-mx-auto')
                            
                            $('#agora_username_broadacaster').html(data.channelName)
                            getCommentListBypost(data.data.postId,orderBy)

                        //type :1 -> Event,2-> User
                        //is_add:1->add, 2 -> remove 3-> Add and Reset
                            var datainfo={
                                is_add:3,
                                type:2,
                                id:loggedInId,
                            }
        
                            updateUserLiveCount(datainfo)
                        
                    }
                       
                },
                error: function (data) {
                }
            });
        }

          
// live stream user end



function disconnectAndcontinueGolive()
{
    
    swal({
        title:'User already live another window. are you sure to broadcast again?',
        // text: result,
        icon: "warning",
        buttons: [
          'No, cancel it!',
          'Yes, I am sure!'
        ],
        dangerMode: true,
      }).then(function(isConfirm) {
        if (isConfirm) {
            userGolive();
        }else{      
           return false;
        }
      });

    // if(confirm(''))
    // {
      
    // }else{
    //     // userStopLive();
    // }
} 


function userStopLive()
{
  orderBy=false;
  is_scroll=true
  var methodType='Get';
  var url = baseurl+'/user-stop-live';
  var data={
    user_id:loggedInId
  }

  $('#broadcast_video').addClass('hide');
  $('#user_profile_data').removeClass('hide');

  callajax(url,methodType,data)
  var datainfo={
    is_add:2,
    type:2,
    id:loggedInId,
  }
    stopRecording(recordingobj);
  
    updateUserLiveCount(datainfo)
    
  // 
//   $('.storyset-section').removeClass('agora-width-cover'); 
}

function updateUserLiveCount(info)
{
    is_scroll=false
    //is_add:1->add, 2 -> remove 3-> brodacaster time Add and Reset
    //type :1 -> Event,2-> User
    var url = baseurl+'/update-user-live-count';
    var methodType='POST';
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    var data={
        is_add:info.is_add,
        type:info.type,
        id:info.id,
    }
    var formData = data;
    var type = methodType;
    var ajaxurl = url;
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        dataType: 'json',
        success: function (data) {
            console.log('user count increase'+data);
            return data;
                
        },
        error: function (data) {
        }
    });
}


setInterval(function() {
    // if(is_go_live){
    getCommentListBypostOnlive(post_id_comment,orderBy)
    // }
}, 5000);



$(document).on('click','#link',function(){
    url = $(this).attr('redirect-url');
    // alert(url);
    window.open(url, '_blank');
})


function audinecNameImg(name,imgsrc)
{
var html='<div class="follow-single-user brodacast_user_name"><div class="follow-username-img"><div class="followuser-img"><img id="broadcaster_img_upload" src="'+imgsrc+'" alt="Lock"></div>'+
'<div class="followuser-name fw-medium"  style="color:white;"id="broadcaster_name_upload ">'+name+'</div></div></div>';
// $('#audience_video').append(html);
$('#agora_username_broadacaster').html(name)
$('#agora_user_pic').attr('src',imgsrc)

} 



function sendBusyNotification(data)
{
  
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
    //     console.log(data);
 
    //     },
    //     error: function (data) {


    //     }
    // });
}



function likeUnlikeComment(comment_id)
    {
        var data ={
            comment_id:comment_id,
            user_id:loggedInId
        };
        var liked_val= $('#liked_value_'+comment_id).val()
        if(liked_val==1)
        {
            var url = 'unlike-comment';
            var url = baseurl+'/unlike-comment';
            $('#liked_value_'+comment_id).val(0)
            $('#like_unlike_'+comment_id).text('Like')
            $('#like_unlike_css'+comment_id).removeClass('liked_icon')

            var count_like= $('#data-total-comment-like_'+comment_id).val();
            count_like--;
            $('#data-total-comment-like_'+comment_id).val(count_like);
            $('#total_like_comment_'+comment_id).text(count_like+' Likes');
            if(count_like==1)
            {
            $('#total_like_comment_'+comment_id).text(count_like+' Like');
            }
        }
        else
        {
            var url = 'like-comment';
            var url = baseurl+'/like-comment';
            $('#liked_value_'+comment_id).val(1);
            $('#like_unlike_'+comment_id).text('Liked')
            $('#like_unlike_css'+comment_id).addClass('liked_icon')

            // liked_icon
            var count_like= $('#data-total-comment-like_'+comment_id).val();
             count_like++;
            $('#data-total-comment-like_'+comment_id).val(count_like);
            $('#total_like_comment_'+comment_id).text(count_like+' Likes');
            if(count_like==1)
            {
              $('#total_like_comment_'+comment_id).text(count_like+' Like');
            }

            // console.log($('#total_count_like_'+id).html());

        }
        var methodType='POST';
        callajax(url,methodType,data)
    }


    function commentlikelistmodal(id)
    {
        var data ={
            comment_id:id,
        };
        var url = baseurl+'/comment-like-user-list';
            // var url = `{{ url('front-panel/like-user-list') }}`; 
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
                $('#totalLikesList').html(data.htmlData);
                $('#totalLikesList').modal('show');
                },
                error: function (data) {


                }
            });

    }


    function getCommentListBypostOnlive(id)
    {
        post_id_comment=id;
            var data ={
                post_id:id,
                orderBy:orderBy,
            };
            var url = baseurl+'/comment-list';
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
                   
                    $('#commentongolive').html(data.agora_comment_html);
                    // $('#comment_list_'+id).html(data.htmlData);
                    $('#live_user_count_particpant').html(data.response.live_user_count)
                    var objDiv = document.getElementById("comment_list_"+id);
                    // objDiv.scrollTop = objDiv.scrollHeight;
                    $("textarea").each(function () {
                        this.setAttribute("style", "height:40px;");
                      }).on("input", function () {
                        this.style.height = "auto";
                        this.style.height = "40px";
                      });

                      
                            // $('.comments_val_class').prop('disabled',true);
                            // $('.comments_val_class').addClass('disable-comment')
                         

                            
                
                },
                error: function (data) {
                }
            });

         
    }

   


    $(function(){
        $('.messages-part .sidebar-group .sidebar .list-group-item').on("click", function () {
          $('.messages-part .chat').addClass("active");
          $('.messages-part .sidebar-group').addClass("d-none");
          setTimeout(RemoveClass, 1000);
        });
        $('.messages-part .chat-header-user .back-btn').on("click", function () {
            $('.messages-part .chat.active').removeClass("active");
            $('.messages-part .sidebar-group.d-none').removeClass("d-none");
            setTimeout(RemoveClass, 1000);
          });
      }); 


    //   window.onload = alert('Are you sure you want');

    // window.onbeforeunload = function() {
    //     return "Dude, are you sure you want to leave? Think of the kittens!";
    // }

    


 $(document).ready(function() {
    $(".modal").on("hidden.bs.modal", function() {
        $(this).find('form').trigger('reset');
        $('.help-block').html('');
        $('.dz-preview').remove();
        $('.videoExist').val(0)
       
        $('#file-input').val(0)
        $('#anointed-file-input').val(0)
        $('#product-file-input').val(0)

    //   $(".modal-body1").html("Where did he go?!?!?!");

    });
  });



  $(".user_join_live").click(function(){
            
    is_go_live=true;
    orderBy=true;
    is_scroll=false
    var other_user_id=$(this).attr('other-user-id');
    var broadcast_img=$(this).attr('broadcaster_img');
    var brodacast_fullName=$(this).attr('broadcaster_name');

    var url = baseurl+'/user-go-live';
    var methodType='POST';
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    // e.preventDefault();
    var data={
        user_id:other_user_id,
        is_publisher:2,
    }
    var script_url = baseurl+'/front/js/agora/agora-audience-client.js';
    loadScript(script_url)
    var formData = data;
    var type = methodType;
    var ajaxurl = url;
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        dataType: 'json',
        success: function (data) {
                var data =data.data;
                $('#audience_video').remove()
                $('#video_stream_go_live').prepend(data.audience_html)
                $('.storyset-section').addClass('d-block-mx-auto')
                $('.video_time_hide').addClass('hide');
                var agora_token=data.token
                channelName=data.channelName
                var is_live=data.is_live
                $('.agora-video-text-title').html(data.title)
                
                uid= parseInt(loggedInId);
                $('#audience_video').removeClass('hide');
                $("#user_profile_data").addClass("hide");
                if(is_live==1){
                    brodcasting_type=1; //user-live
                    clientCreateAudience(agoraAppId,agora_token,channelName,uid,brodcasting_type);

                    // var brodacast_fullName = $('#broadcaster_name').val();
                    // var broadcast_img = $('#broadcaster_img').val();
                   
                    
                    audinecNameImg(brodacast_fullName,broadcast_img)
                    $('.agora-video-text-title').html(data.title)
                    getCommentListBypost(data.postId,orderBy)

                    //type :1 -> Event,2-> User
                    //is_add:1->add, 2 -> remove 3-> Add and Reset
                    var datainfo={
                        is_add:1,
                        type:2,
                        id:other_user_id,
                    }
                    updateUserLiveCount(datainfo) 
                }else{
                        $('#no-local-video').attr("style", "display:flex");
                        $('#no-local-video').html('Broadcaster has already stopped the live streaming.');//broadcast
                }

        
        },
        error: function (data) {
            swal({
                title:'Broadcaster has stopped the live streaming',
                // text: result,
                icon: "warning",
                buttons: [
                  'Cancel!',
                  'Okay'
                ],
                dangerMode: true,
              }).then(function(isConfirm) {
                if (isConfirm) {
                  location.reload()
                }else{      
                   return false;
                }
              });
        }
    });   
    
  });


  function startRecording(data){
    var url = baseurl+'/start-agora-recording';
    var methodType='POST';
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });

    var formData = data;
    var type = methodType;
    var ajaxurl = url;
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        dataType: 'json',
        success: function (data) {
            recordingobj.resourceId= data.data.resourceId;
            recordingobj.sid=data.data.sid;
                
        },
        error: function (data) {
        }
    });

    // channelName  agoraToken sid resourceId  postId
  }

  

  function stopRecording(data){
  // channelName  agoraToken sid resourceId  postId
     var obj=data;

    var url = baseurl+'/stop-agora-recording';
    var methodType='POST';
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    showLoader()
    var formData = obj;
    var type = methodType;
    var ajaxurl = url;
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        timeout: 180000,
        dataType: 'json',
        success: function (data) {
        //   alert('Recording finsih');
        hideLoader();
     if(pageReloadOnrecord)
     {location.reload();}

                
        },
        error: function (data) {
            hideLoader();
        }
    });

  }


  function hideLoader(){
    $('#agora_loader').addClass('hide')
    $('.loading').addClass('hide')
    $('#isit_body').removeClass('onloader-time-body')
  }

  function showLoader(){
    $('#agora_loader').removeClass('hide')
    $('.loading').removeClass('hide')
    $('#isit_body').addClass('onloader-time-body')
  }



  function editPost(id){
    
        $('.help-block').remove();
            var id=id;
            var url = baseurl+'/get-post';
            var data ={
                post_id :id,
            };
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
                     var dataRes= data.data.data;
                     if(dataRes.post_type==1)
                     {
                        $('#CreatePostGeneralLabel').html('Edit General post');
                        $("#CreatePostGeneral").modal('show')
                        $('#heading').val(dataRes.heading)
                        $('#hidden_id').val(dataRes.id)
                        $('#description').val(dataRes.description)
                     }else{
                        $('#CreateAnointedPostlLabel').html('Edit Anointed post');
                        $('#category').val(dataRes.category_id)
                        $('#hidden_id').val(dataRes.id)
                        $('#hidden_id_anointed').val(dataRes.id)
                        $('#country').val(dataRes.country_id)
                        $('#zip_code').val(dataRes.zip_code)
                        $('#city_name').val(dataRes.city)
                        $('#anointedDetails').val(dataRes.description)
                        $("#CreateAnointedPost").modal('show')
                     }
                   
                     $('.upload_post').html('')
                     var thumbanilData = dataRes.get_media;
                     var post_media= data.data.path.post_media;
                     var htmldata='';
                     $.each(thumbanilData, function(key,val) {
                           var file_path= post_media+val.file_name; 
                         //   $('#product-file-input').val(1);
                           var is_video=isImage(val.file_name);
                           var videoFlag=''
                           if(is_video==false)
                           {
                               $('.videoExist').val(1);
                               videoFlag=2
                           }
                           if(val.media_type==2){
                               $('.videoExist').val(1);
                               videoFlag=2
                               file_path=post_media+val.thumb_image_name; 
                           }
                           htmldata+='<li id="image-preview_'+val.id+'" ><a class="cross-icon-prdouct" onclick="removefilePost('+val.id+')">X<a><div class="image-video-preview" ><img src="'+file_path+'" alt=""></img></div><input type="hidden" video="'+videoFlag+'" id="remove_input_product'+val.id+'"  value="' + val.file_name + '" data-val="1"></li>';
   
                       });
                       // $('form').append()  ;   
                       $('.upload_post').append(htmldata+'<input type="hidden" id="legthFile" value="'+thumbanilData.length+'">')
                      
                       if(thumbanilData.length>=5)
                       {
                         $('#dropzone_post').addClass('hide');
                       }
               
                        

                },
                error: function (data) {
                }
            });
  }


  
  function removefilePost(id)
  {
      var valueVideo= $('#remove_input_product'+id).attr('video');
      if(valueVideo==2)
      {
          $('.videoExist').val(0);
      }
      $('#remove_input_product'+id).remove();
      $('#image-preview_'+id).remove();
      var legthFile=$('#legthFile').val();
      legthFile--;
      $('#legthFile').val(legthFile);

      if($('#legthFile').val()==0)
      {
          $('#file-input').val('');
      }
      $('form').append('<input type="hidden" name="deleted_media_ids[]"  value="'+id+'">')  ;   
      $('.savebtngenral').prop('disabled', false);
      $('#dropzone_post').removeClass('hide');
  }
  

  



  function CreatePostGeneral(){
    $("#CreatePostGeneral").modal('show')
    $('#heading').val('')
    $('#hidden_id').val('')
    $('#description').val('')
    $('.upload_post').html('')
    $('#dropzone_post').removeClass('hide');
    $('#CreatePostGeneralLabel').html('Create General post');
  }
  function CreateAnointedPost(){
    $('#CreateAnointedPostlLabel').html('Create Anointed post');
    $('#category').val('')
    $('#hidden_id_anointed').val('')
    $('#hidden_id').val('')
    $('#country').val('')
    $('#zip_code').val('')
    $('#city_name').val('')
    $('#anointedDetails').val('')
    $("#CreateAnointedPost").modal('show')
  }