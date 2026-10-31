$(document).ready(function () {
    /*$(document).ajaxStart(function(){
        $.blockUI({
            message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
        });
    });*/


    /*$(document).ajaxStop(function(){
        //$.unblockUI()
    });*/

    window['recipient_delete_id']=0;
    window['recipient_alias_delete_id']=0;
    jQuery.validator.addMethod("customEmailValidation2", function (value, element, params) {
        var idname = $(element).attr('id');
        var emailText = $(element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailText);
    }, jQuery.validator.format("Please enter a valid email address."));
    /** personal preferences form */
    $('#form-validate-recipients').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30
            },
        },
        // errorPlacement: validateErrorPositioning,
        success: function(label,element) {
            $('#recipient_name-error').remove();
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
            name: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            email: {
                maxlength: 'Entered characters should not be more than 255.',
                //remote : 'The email has already been taken.'
            }
        }
    });

    

    //Delete recipients
    $(document).on('click', '.delete-recipient', function () {
        window['recipient_delete_id'] = $(this).attr('data-id');;
        $("#deleteRecipientConfirmModal").modal('show');
    });
    $(document).on('click', '.delete-recipient-confirmation-btn', function () {
        $("#deleteRecipientConfirmModal").modal('hide');
        var id = window['recipient_delete_id'];
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: removeRecipient,
            dataType: "json",
            data: {id:id},
            success: function (response) {
                if (response.status) {
                    
                    var message = "Recipient removed successfully !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            location.reload();
                        }
                    });
                    //location.reload(); 
                }
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function (jqXHR, textStatus, errorThrown) {
                var message = "Spmething went wrong !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            //location.reload();
                        }
                    });
            }
        });
    });

    //Delete recipients alias
    $(document).on('click', '.delete-recipient-alias', function () {
        window['recipient_alias_delete_id'] = $(this).attr('data-id');;
        $("#deleteRecipientAliasConfirmModal").modal('show');
    });
    $(document).on('click', '.delete-recipient-alias-confirmation-btn', function () {
        $("#deleteRecipientAliasConfirmModal").modal('hide');
        var id = window['recipient_alias_delete_id'];
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: removeRecipientAlias,
            dataType: "json",
            data: {id:id},
            success: function (response) {
                if (response.status) {
                    
                    var message = "Recipient alias removed successfully !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            location.reload();
                        }
                    });
                    //location.reload(); 
                }
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function (jqXHR, textStatus, errorThrown) {
                var message = "Spmething went wrong !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            //location.reload();
                        }
                    });
            }
        });
    });


    /** recipient alias add form */
    $('#form-validate-recipient-alias').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30
            }
        },
        // errorPlacement: validateErrorPositioning,
        success: function(label,element) {
            $('#alias_name-error').remove();
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
            name: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            email: {
                maxlength: 'Entered characters should not be more than 255.',
                //remote : 'The email has already been taken.'
            }
        }
    });

    $(document).on('click', '.add-alias-class', function () {
        $("#alias_name-error").remove();
        var recipientCount = $(this).attr('data-total-recipient');
        if(recipientCount<5){
            $("#addalias").modal("show");
            var recipient_id = $(this).attr('data-recipient-id');
            $('#recipient_id').val(recipient_id);
            $('#alias_name').val('');
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "You can only add 5 alias in an recipient, Remove existing alias to add more.",
                callback: function (result) {}
            });
        }
    });

    $(document).on('click', '.add-recipient-alias-btn', function () {
        
        //console.log($('#form-validate-recipients'));
        if ($('#form-validate-recipient-alias').valid()) {
            //alert('validated');return false;    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveRecipientAlias,
                dataType: "json",
                data: $('#form-validate-recipient-alias').serialize(),
                success: function (response) {
                    
                    if (response.status) {
                        $("#addalias").modal("hide");
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }else{
                        var error = '<span class="help-block" id="alias_name-error">' + response.message + '</span>';
                        $("#alias_name").after(error);
                        $("#alias_name").addClass('help-block');
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {

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

    /*show add recipient popup on confirm*/
    $(document).on('click', '.add-recipient-confirmation-btn', function () {
        $("#addRecipientConfirmModal").modal('hide');
        showAddRecipientPopup();
    });

});

function showAddRecipientPopup(){
    //if(isAccountOwner==1){
        $("span.help-block").remove();
        $('.help-block').removeClass('help-block');

        $("#recipient_name").val('');
        $("#email").val('');
        $("#scret_id").val('');

        $(".modal-heading-class").html("Add Recipient");
        $(".modal-title-class").html("Add a new recipient to this account.");
        $(".add-recipient-btn").html("Submit");

        $("#creatusermodal").modal("show");
    /*}else{
        bootbox.alert({
            title: "Alert!!",
            message: "Only SMB number owner can add recipients as you are not owner of this SMB number you can not add recipients. Please try again to change SMB number",
            callback: function (result) {}
        });
    }*/
}

function showRecipientPopup(isAccountOwner,recipientFee=0){
    if(isPaymentAvailable){
        if(!recipientFee==-1){
            bootbox.alert({
                title: "Alert!!",
                message: "You can not add recipient in this account as this account has no subscription plan",
                callback: function (result) {}
            });
        }else if(recipientFee>0){
            $("#addRecipientConfirmModal").modal('show');
        }else{
            showAddRecipientPopup();
        }
    }else{
        bootbox.confirm({
            title: "Action Required!",
            message: commonPaymentMethodMsg,
            cancel: false,
            buttons: {
                cancel: {
                    className: 'bootbox-alert-hide-btn'
                }
                ,
                confirm: {
                    label: '+ Add a payment method',
                    className: 'bootbox-alert-ok-btn'
                }
            },
            callback: function (result) {
                if(result){
                    location.replace(subScriptionPagePath);
                }
            }
        });
    }
}

function getRecipientsForm(id,name,email){
    //alert(id)
    $("span.help-block").remove();
    $('.help-block').removeClass('help-block');
    //$("#email-error").remove();

    $("#recipient_name").val(name);
    $("#email").val(email);
    $("#scret_id").val(id);

    $(".modal-heading-class").html("Edit Recipient");
    $(".modal-title-class").html("Update a recipient to this account.");
    $(".add-recipient-btn").html("Update");

    $("#creatusermodal").modal("show");
   /* $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getRecipientsPopup,
        dataType: "html",
        data: {
        id: id
        },
        success: function (response) {
            $('#creatusermodal').modal('show');
            $(".addeditusermodal").html(response);
        },
        beforeSend: function () {           
        }
    });*/
}