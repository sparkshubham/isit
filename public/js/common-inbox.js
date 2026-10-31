$(document).ready(function () {

    $('#main').attr('style','padding-top:'+$(header).height()+'px !important');
    $(window).scrollTop(0);

    $(document).on('click', '.close-message', function () {
        $('.smb-message').hide();
        $('#main').addClass("remove-extra");
        $('#main').attr('style','padding-top: 65px !important');
         if($(window).width() < 1023)
            {
                $('#main').attr('style','padding-top: 54px !important');
            } else {
                $('#main').attr('style','padding-top: 65px !important');
            }
    });

    setTimeout(function() {
        // $('.smb-message').delay(500).slideUp("normal", function() { 
        //     $('.smb-message').remove();
        //     $('#main').addClass("remove-extra");
        //     $('#main').attr('style','padding-top:'+$(header).height()+'px !important');
        //  } );
        $('.smb-message').remove();
        $('#main').addClass("remove-extra");
        $('#main').attr('style','padding-top:'+$(header).height()+'px !important');
    }, 5000);

    $(document).ajaxStart(function(){
        $.blockUI({
            message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
        });
    });


    $(document).ajaxStop(function(){
        $.unblockUI()
    });

    /*add recipient function*/
    $(document).on('click', '.add-recipient-btn', function () {
        
        //console.log($('#form-validate-recipients'));
        if ($('#form-validate-recipients').valid()) {
            $(".add-recipient-btn").prop('disabled', true); 
            //alert('validated');return false;    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveRecipients,
                dataType: "json",
                data: $('#form-validate-recipients').serialize(),
                success: function (response) {
                    $(".add-recipient-btn").prop('disabled', false); 
                    if (response.status) {
                        $("#creatusermodal").modal("hide");
                        var message = response.message;
                        bootbox.alert({
                            title: "Alert!!",
                            message: message,
                            callback: function (result) {
                                location.reload();
                            }
                        });
                    }else{
                        //var error = '<span class="help-block" id="email-error">' + response.message + '</span>';
                        //$('.error-span').html(response.message);
                        var error = '<span class="help-block" id="recipient_name-error">' + response.message + '</span>';
                            $("#recipient_name").after(error);
                            $("#recipient_name").addClass('help-block');
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    
                    if(jqXHR.status == 422){
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
                    }else{
                        if(jqXHR.status == 401){
                            $("#creatusermodal").modal("hide");
                            bootbox.alert({
                                title: "Alert!!",
                                message: jqXHR.responseJSON.message,
                                callback: function (result) {
                                }
                            });
                        }else{
                            $("#creatusermodal").modal("hide");
                            csrfTokenHandler(jqXHR);
                        }
                    }
                }
            });
        }
    });

    /*deposit bank account*/
    /*$(document).on('keypress', '#account_number', function () {
        $('#account_number-error2').remove();
    });
    $(document).on('change', '#account_number', function () {
        $('#account_number-error2').remove();
    });*/
    /*validate bank account */
    $('[data-mask]').inputmask(); 
    $("#contact_phone_number").inputmask("999-999-9999",{ "clearIncomplete": true });
    var form_validate_bank_account = $('#form_validate_bank_account').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            bank_name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 255,
            },
            routing_number: {
                required: true,
                number:true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 9,
                maxlength: 9,
            },
            account_number: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 4,
                maxlength: 12,
            },
            contact_name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30,
            },
            contact_business_name: {
                //required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30,
            },
            contact_address: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                maxlength: 255,
            },
            contact_phone_number: {
                required: true,
            },
            account_type: {
                required: true,
            },
            account_nick_name: {
                //required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30,
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
            console.log(validator);
            if (!validator.numberOfInvalids())
                return;

            /*$('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);*/
        },
        showErrors: function(errormap,errorList){
            if(errorList.length){
                $('#account_number-error2').remove();
                /*var s = errorList.shift();
                var n = [];
                n.push(s);
                this.errorList = n;*/
            }
            this.defaultShowErrors();
        },

        messages: {
            bank_name: {
                maxlength: 'Entered characters should not be more than 255.'
            },
            routing_number: {
                maxlength: 'Entered characters should not be more than 9.'
            },
            account_number: {
                maxlength: 'Entered characters should not be more than 12.'
            },
            contact_name: {
                maxlength: 'Entered characters should not be more than 30.'
            },
            contact_business_name: {
                maxlength: 'Entered characters should not be more than 30.'
            },
            contact_address: {
                maxlength: 'Entered characters should not be more than 255.'
            },
            account_nick_name: {
                maxlength: 'Entered characters should not be more than 30.'
            }
        }
    });

    /*close add bank accoun popup*/
    $(document).on('click','.add-bank-account-cancel-btn',function(event){
        form_validate_bank_account.resetForm();
        var check_deposit_form = $('#check_deposit_form').validate({});
        check_deposit_form.resetForm();
        $('#add_bank_account').modal('hide');
        $("#checkdeposit_modal").modal('show');
    });
    /*add bank account and show check-deposit popup again*/
    $(document).on('click','.add-bank-account-btn',function(event){
        var isDepositPage = false;
        if($(this).hasClass('deposit-account-page')){
            isDepositPage = true;
        }
        if ($('#form_validate_bank_account').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveBankAccount,
                dataType: "html",
                data: $('#form_validate_bank_account').serialize(),
                success: function (response) {
                    //$("#share_mail_modal").modal('hide');
                    response = JSON.parse(response);
                    if (response.status) {
                        form_validate_bank_account.resetForm();
                        if(isDepositPage){
                            $("#secret_id").val('');
                            $('.deposit-account-page').html('Add Account');
                            bootbox.alert({
                                title: "Alert!!",
                                message: response.message,
                                callback: function (result) {
                                    location.reload();
                                },
                            });
                        }else{
                            $('#add_bank_account').modal('hide');
                            var check_deposit_form = $('#check_deposit_form').validate({});
                            check_deposit_form.resetForm();
                            $("#checkdeposit_modal").modal('show');
                            $("#account_type").html(response.options);
                        }
                    }else{
                       /* bootbox.alert({
                            title: "Alert!!",
                            message: response.message,
                            callback: function (result) {
                                
                            },
                        });*/
                        var error = '<span class="help-block" id="account_number-error2">' + response.message + '</span>';
                        $("#account_number").after(error);
                        $("#account_number").addClass('help-block');
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    if(textStatus != "Unprocessable Entity"){//Unauthorized
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Your session expired.",
                            callback: function (result) {
                                location.reload();
                            },
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
});

/* Helper function to download file */
function download_file(fileURL, fileName) {
    // for non-IE
    if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.target = '_blank';
        var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
        save.download = fileName || filename;
           if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                document.location = save.href; 
// window event not working here
            }else{
                var evt = new MouseEvent('click', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': false
                });
                save.dispatchEvent(evt);
                (window.URL || window.webkitURL).revokeObjectURL(save.href);
            }   
    }

    // for IE < 11
    else if ( !! window.ActiveXObject && document.execCommand)     {
        var _window = window.open(fileURL, '_blank');
        _window.document.close();
        _window.document.execCommand('SaveAs', true, fileName || fileURL)
        _window.close();
    }
    return true;
}

/** function to handle cookies */
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}