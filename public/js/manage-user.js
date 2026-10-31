$(document).ready(function () {
    
    loadManageUsersTableData(1);
    window['selected_role']='';
    // manage user paging
    $(document).on('click','.user-arrow-left',function(){
        loadManageUsersTableData($(this).attr('data-page'));
    });

    $(document).on('click','.user-arrow-right',function(){
        loadManageUsersTableData($(this).attr('data-page'));
    });

    $(document).on('click','.search-manage-users',function(){
        var searchKeyword = $('#filter-users').val();
        loadManageUsersTableData(1,searchKeyword);
    });

    $('#filter-users').keyup(function(e){
        //if($.trim(this.value).length >= 3 || e.keyCode == 13) {
        if(e.keyCode != 37 && e.keyCode != 39 && e.keyCode != 35){
            if (((e.keyCode == 8 || event.keyCode == 46) && ($.trim(this.value).length == 0) || $.trim(this.value).length >= 3) || e.keyCode == 13) {
                var searchKeyword = $(this).val();
                loadManageUsersTableData(1,searchKeyword); 
            }
        }
    });

    $(document).on('click','.revoke-access',function(){
        window['sub_user_id'] = $(this).attr('data-id');
        window['revoke_status'] = $(this).attr('data-value');
        if(window['revoke_status']==1)
            $("#revoke_grant_user").html('The recipients of this user will be removed and this user will unable to access your account.');
        else
            $("#revoke_grant_user").html('Are you sure to grant access to this user ?');    
        $("#confirmModal").modal('show');
    });

    //filter recipients
    $("#search_recipient").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#content-1 div").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    
    $(document).on('click','.revoke-access-btn',function(){
        $("#confirmModal").modal('hide');
        var id = window['sub_user_id'];
        var status = window['revoke_status'];
        if(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: revokeAccess,
                dataType: "json",
                data: {id:id,status:status},
                success: function (response) {
                    if(response.status){
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadManageUsersTableData(1);
                            }
                        });       
                    }else{
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                
                            }
                        }); 
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {}
            });
        }
    });

    //change admin permission
    $(document).on('click','.toggle-handle',function(){
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        if(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: changeSubUserPermission,
                dataType: "json",
                data: {id:id,status:status},
                success: function (response) {
                    if(response.status){
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadManageUsersTableData(1);
                            }
                        });       
                    }else{
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                
                            }
                        }); 
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {}
            });
        }
    });

    //add sub user validation
    jQuery.validator.addMethod("customEmailValidation2", function (value, element, params) {
        var idname = $(element).attr('id');
        var emailText = $(element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailText);
    }, jQuery.validator.format("Please enter a valid email address."));
    /** personal preferences form */
    var add_subuser_form = $('#add_subuser_form').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            first_name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30
            },
            last_name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30
            },
            email: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                customEmailValidation2: true,
                remote :
                {
                    url: validateSubUserEmail,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    data:
                    {
                        email: function()
                        {
                            return $('.add-subuser-form :input[name="email"]').val();
                        },
                    },
                    async: false, // set async = false
                }
            },
            is_admin: {
                required: true
            },
        },
        // errorPlacement: validateErrorPositioning,
        errorPlacement: function(error,element){
          if($(element).prop('nodeName') == 'SELECT'){
            error.insertAfter($(element).next('span'));
          }else{
            error.insertAfter($(element));
          }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).parents('.col-field-wrapper').addClass('has-error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents('.col-field-wrapper').removeClass('has-error');
        },
        invalidHandler: function (form, validator) {
            if (!validator.numberOfInvalids())
                return;

            /*$('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);*/
        },
        messages: {
            first_name: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            last_name: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            email: {
                maxlength: 'Entered characters should not be more than 255.',
                remote : 'The user already exist.'
            }
        }
    });

    $(document).on('click', '.open-add-subuser-popup', function () {
        if($(this).attr('data-admin-user')==1){
            add_subuser_form.resetForm();
            $("#add_subuser_recipients_form").trigger('reset');
            $("#creatusermodal").modal('show');
            //$("#grantrecipientmodal").modal('show');
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Only SMB number owner can add sub user as you are not owner of this SMB number you can not add sub user. Please try again to change SMB number",
                callback: function (result) {}
            });
        }
    });
    //save sub user
    $(document).on('click', '.add-subuser-next', function () {
        
        //console.log($('#form-validate-recipients'));
        if ($('#add_subuser_form').valid()) {
            window['first_name'] = $("#first_name").val();
            window['selected_role'] = $("#is_admin").val();

            //$(".add-recipient-btn").prop('disabled', true); 
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveSubUser,
                dataType: "json",
                data: $('#add_subuser_form').serialize(),
                success: function (response) {
                    //$(".add-recipient-btn").prop('disabled', false); 
                    if (response.status) {
                        $("#creatusermodal").modal("hide");
                        $('#subusername').html(window['first_name']);
                        $("#sub_user_id").val('');
                        if(window['selected_role']==1){
                            var message = response.message;
                            bootbox.alert({
                                title: "Alert!!",
                                message: message,
                                callback: function (result) {
                                    loadManageUsersTableData(1);
                                }
                            });
                        }else{
                            $("#grantrecipientmodal").modal("show");
                        }
                        
                    }else{
                        $("#creatusermodal").modal("hide");
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadManageUsersTableData(1);
                            }
                        });
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 402) {
                      var message = 'CSRF token expires.';//response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }else if(jqXHR.status == 500){
                        var message = 'Something went wrong.';//response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }
                    var responseText = JSON.parse(jqXHR.responseText);
                    if (responseText.errors) {
                        $("span.help-block").remove();
                        $('.help-block').removeClass('help-block');

                        $.each(responseText.errors, function (i, value) {
                            var error = '<span class="help-block" id="email-error">' + value + '</span>';
                            $("#" + i).after(error);
                            $("#" + i).addClass('help-block');
                        });
                    }
                }
            });
        }
    });

    //select all recipient
    $(document).on('click','.grant-select-all-link',function(){
        //alert(132);
        //$('input:checkbox').prop('checkbox',true);
        $('.recipient-checkbox').each(function(){
            this.checked=true; 
        });
    });

    //deselect all recipient
    $(document).on('click','.grant-select-clear-link',function(){
        $('.recipient-checkbox').each(function(){
            this.checked=false; 
        });
    });

    //add recipient
    $(document).on('click', '.add-subuser-recipient', function () {
        $(".add-subuser-recipient").prop('disabled', true);
        var checkedSize = $('.recipient-checkbox:checkbox:checked').length;
        if(checkedSize>0){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveSubUserRecipients,
                dataType: "json",
                data: $('#add_subuser_recipients_form').serialize(),
                success: function (response) {
                    $(".add-subuser-recipient").prop('disabled', false);
                    //$(".add-recipient-btn").prop('disabled', false); 
                    $("#grantrecipientmodal").modal("hide");
                    if (response.status) {
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadManageUsersTableData(1);
                            }
                        });
                    }else{
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadManageUsersTableData(1);
                            }
                        });
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 402) {
                      var message = 'CSRF token expires.';//response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }else if(jqXHR.status == 500){
                        var message = 'Something went wrong.';//response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }
                    
                }
            });
        }else{
            /*var message = "Please select at least one recipient.";
            bootbox.alert({
                title: "Alert!!",
                message: message,
                callback: function (result) {
                    $(".add-subuser-recipient").prop('disabled', false);
                }
            });*/
            $("#recipientAlertModal").modal('show');
            $(".add-subuser-recipient").prop('disabled', false);
        }
    });

    //edit grant recipients
    $(document).on('click', '.manage-user-permission-class', function () {
        var sub_user_id = $(this).attr('data-id');
        $("#sub_user_id").val(sub_user_id);
        $('#subusername').html($(this).attr('data-name'));
        $("#add_subuser_recipients_form").trigger('reset');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: getUserRecipientsId,
            dataType: "json",
            async: false,
            data: {sub_user_id:sub_user_id},
            success: function (response) {
                //console.log(response);
                //response = JSON.parse(response);
                if(response.status){
                    if((response.getRecipients)){
                        var recipient = response.getRecipients;
                        for(var j =0;j<(recipient.length);j++){
                            $.each($('.recipient-checkbox'),function(i,v){
                                var rec_id = $(v).prop("value");
                                if(rec_id==recipient[j]){
                                    $(v).prop("checked",true);
                                }
                            });
                        }
                    }
                    $("#grantrecipientmodal").modal("show");
                }
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function (jqXHR, textStatus, errorThrown) {}
        });
        
    });
});

function loadManageUsersTableData(page=1,searchKeyword=null){

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: loadManageUsersTable,
        dataType: "html",
        //async: false,
        data: {page:page,searchKeyword:searchKeyword},
        success: function (response) {
            response = JSON.parse(response);
            if(response.status){
                
                let previousPage = 1;
                let nextPage = parseInt(response.paging.current_page) + 1;
                
                if(response.paging.current_page > 2){
                    previousPage = parseInt(response.paging.current_page) - 1;
                }

                if(response.paging.current_page == response.paging.last_page){
                    nextPage = response.paging.last_page;
                }

                

                $('.manage-user-table').empty();
                $('.manage-user-table').html(response.htmlData);
                $('.manage-user-tooltip-cover').tooltip();
                if(response.paging.total_record > 0){
                    $('.manageuser-page-number').html(response.paging.from+' - '+response.paging.to+' of '+response.paging.total_record);
                }else{
                    $('.manageuser-page-number').html(response.paging.total_record+' - '+response.paging.total_record+' of '+response.paging.total_record);
                }
                $('.user-arrow-left').attr('data-page',previousPage)
                $('.user-arrow-right').attr('data-page',nextPage)
                
                if(response.paging.total_record > 0){
                    $('.manageuser-page-pagination').show();
                }else{
                    $('.manageuser-page-pagination').hide();
                }

                /*disable right and left arrow paging */
                if(response.paging.current_page == response.paging.last_page){
                    nextPage = response.paging.last_page;
                   $('.user-arrow-right').addClass('disable'); 
                }else{
                    $('.user-arrow-right').removeClass('disable');
                }
                if(response.paging.current_page==1){
                    $('.user-arrow-left').addClass('disable');
                }else{
                    $('.user-arrow-left').removeClass('disable');
                }
                
            }
        },
        beforeSend: function () {
            //disable the submit button

        },
        error: function (jqXHR, textStatus, errorThrown) {}
    });
    
}
