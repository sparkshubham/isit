$(document).ready(function () {
    window['account_id'] = '';
    loadDepositAccountTableData(1);
    
    // manage user paging
    $(document).on('click','.user-arrow-left',function(){
        loadDepositAccountTableData($(this).attr('data-page'));
    });

    $(document).on('click','.user-arrow-right',function(){
        loadDepositAccountTableData($(this).attr('data-page'));
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

            $('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
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

    //save sub user
    /*$(document).on('click', '.add-subuser-next', function () {
        
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
                                    loadDepositAccountTableData(1);
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
                                loadDepositAccountTableData(1);
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
    });*/

    /*delete confirmation*/
    $(document).on('click', '.delete-deposit-account', function () {
        window['account_id'] = $(this).attr('data-id');
        $("#deleteAccountConfirmModal").modal('show');
    });

    /*delete account*/
    $(document).on('click','.delete-account-btn',function(){
        $("#deleteAccountConfirmModal").modal('hide');
        var id = window['account_id'];
        if(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: removeDepositAccount,
                dataType: "json",
                data: {id:id},
                success: function (response) {
                    if(response.status){
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                loadDepositAccountTableData(1);
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

    /*edit button click auto fill all the fields*/
    $(document).on('click','.edit-deposit-account',function(){
        var id = $(this).data('id');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: getDepositAccountById,
            dataType: "json",
            async: false,
            data: {id:id},
            success: function (response) {
                if(response.status){
                    //var account_type = response.data.account_type.toString();
                    console.log(response.data.account_type);
                    var result = response.data;
                    $("#bank_name").val(result.bank_name);
                    $("#routing_number").val(result.routing_number);
                    $("#account_number").val(result.account_number);
                    $("#account_nick_name").val(result.account_nick_name);
                    $("#account_type").val(result.account_type);
                    $("#account_type").trigger("change");
                    
                    $("#contact_name").val(result.contact_name);
                    $("#contact_business_name").val(result.contact_business_name);
                    $("#contact_address").val(result.contact_address);
                    $("#contact_phone_number").val(result.contact_phone_number);
                    $("#secret_id").val(result.id);
                    $('.deposit-account-page').html('Update Account');
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function (jqXHR, textStatus, errorThrown) {}
        });
    });
    
    /*reset deposit account form*/
    $(document).on('click','.reset-bank-account-btn',function(){
        $("#secret_id").val('');
        $("#form_validate_bank_account").trigger("reset");
        $('.deposit-account-page').html('Add Account');
        $('.help-block').remove();
    });
});

function loadDepositAccountTableData(page=1,searchKeyword=null){

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: loadDepositAccountTable,
        dataType: "html",
        async: false,
        data: {page:page,searchKeyword:searchKeyword},
        success: function (response) {
            response = JSON.parse(response);
            if(response.status){
                
                $('.deposit-account-table').empty();
                $('.deposit-account-table').html(response.htmlData);               
            }
        },
        beforeSend: function () {
            //disable the submit button

        },
        error: function (jqXHR, textStatus, errorThrown) {}
    });
    
}
