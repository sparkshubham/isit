/** Ready start here*/
$(document).ready(function () {

    //redirected from the authorization process
    window['sendEmail'] = 0;
    window['shareSelectedMailId']=[];
    window['shareType']='';
    window['currentRoom']='pending';
    window['shipping_try'] = 0;
    var commonMessage = "You can not request for the mail which is already in-process";
    var smbcommonMessage = "You can not create a request until your CMRA process not completed.";
    var commonScanMessage = "You need to scan the mail first to complete the process.";
    closePopupWindow();

    // clear sort
    clearSort();

    /*remove drop down class of smb no selector if there are only one smb*/    
    $(document).on('click','#dropdownMenuButtonSmb',function(){
        var showDropDown = $.trim($('#smb_number_dropdown').html());
        if(showDropDown==''){
            $("#smb_number_dropdown").removeClass('dropdown-menu');
        }
    });
    $(document).on('click','#dropdownMenuButtonAccount',function(){
        var showDropDown = $.trim($('#dropdownMenuButtonAccount').next('.dropdown-menu').html());
        if(showDropDown==''){
            $('#dropdownMenuButtonAccount').next('.dropdown-menu').removeClass('dropdown-menu');
        }
    });

    

    // load all tags
    updateTags();

    // hide content tab
    $('.content-nav-item').hide();

    // Hide mail room indicator
    $('.mail-room-indicator').hide();

    // hide scan related data from shred confirmation
    $('.shared-confirm-model-text').hide();
    $('.shared-confirm-scan-btn').hide();

    /**
     * Clearable text inputs
     */
    $(".clearable").each(function() {
    
        var $inp = $(this).find("input:text"),
            $cle = $(this).find(".clearable__clear");
    
        $inp.on("input", function(){
        $cle.toggle(!!this.value);
        });
        
        $cle.on("touchstart click", function(e) {
        e.preventDefault();
        $inp.val("").trigger("input");
        });
        
    });

    // validate and submit select account form
    $(document).on('click','.select-account-btn',function(){
        if($('input[name=selecaccount]:checked').length > 0){
            $('.select-account-error-block').html('');
            $('.select-account-form').submit();
        }else{
            $('.select-account-error-block').html('<div class="alert alert-danger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please select account first.</div>');
        }
    });

    $(document).on('click','.smb-number-selector',function(){
                
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getSmbData,
            dataType: "json",
            data: {id:$(this).attr('data-id'),page_name:currentPageName},
            success: function (response) {
                if(response.status){
                    // location.reload();
                    location.replace(response.redirect)
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
                csrfTokenHandler(jqXHR);
            }
        });
    });

    //account name selector
    if($.trim($('#dropdownMenuButtonAccount').html())==''){
        //$("#dropdownMenuButtonAccount").next().children(':first').trigger('click');
        //console.log($(".account-name-selector:first"));
        //$(".account-name-selector:first").trigger('click');
    }
    $(document).on('click','.account-name-selector',function(){
        //console.log($(this).html());
        $("#dropdownMenuButtonAccount").html($(this).html());
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getAccountSmbData,
            dataType: "html",
            data: {id:$(this).attr('data-id')},
            success: function (response) {
                response = JSON.parse(response);
                if(response.status){
                    //location.reload();
                    $('#smb_number_dropdown').html(response.response);
                    $("#smb_number_dropdown").children(":first").trigger('click');
                    $("#smb_number_dropdown").addClass('dropdown-menu');
                    if(response.response == ''){
                        $("#smb_number_dropdown").removeClass('dropdown-menu');
                    }
                    if(response.showDropDown==1){
                        $("#dropdownMenuButtonSmb").addClass('dropdown-toggle');    
                    }else{
                        $("#dropdownMenuButtonSmb").removeClass('dropdown-toggle');
                    }
                    //$("#dropdownMenuButton").addClass('dropdown-toggle');
                    //$("#dropdownMenuButton").after(response.response);
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
                csrfTokenHandler(jqXHR);
            }
        });
    });

    // inbox mail paging
    $(document).on('click','.inbox-arrow-left',function(){
        
        if($('.paginator-min-counter').html() > 1){
            getInboxMails($(this).attr('data-page'));
        }
    });

    $(document).on('click','.inbox-arrow-right',function(){
        if($('.paginator-max-counter').html() != $('.paginator-total-counter').html()){
            getInboxMails($(this).attr('data-page'));
        }
    });

    // Inbox mail preview
    $(document).on('click','.inbox-single-mail-block',function(event){
        
        if(event.target.type != "checkbox" && !$(event.target).hasClass('mail-sectional-check-cls')){
            getInboxMailPreview($(this).attr('data-id'),this);
            $.each($('.inbox-single-mail-block'),function(i,v){
                if(!$(v).find('.mail-single-checkbox').is(':checked')){
                    $(this).removeClass('active');
                }
            });
            $(this).addClass('active');
        }
    });

    // Inbox mail preview
    // $(document).on('click','.clickable-section-for-preview',function(event){
        
    //     getInboxMailPreview($(this).attr('data-id'));

    //     $.each($('.inbox-single-mail-block'),function(i,v){
    //         $(this).removeClass('active');
    //     });
        
    //     $(this).parents('.inbox-single-mail-block').addClass('active');

    // });

    // check all checkbox
    $(document).on('click','.mail-select-all-checkbox',function(){
        if($(this).is(":checked")){
            $.each($('.mail-single-checkbox'),function(i,v){
                $(v).prop("checked",true);
                // if(!$(this).parents('.inbox-single-mail-block').hasClass('active')){
                    $(this).parents('.inbox-single-mail-block').addClass('checkbox-selected');
                // }
            });
        }else{
            $.each($('.mail-single-checkbox'),function(i,v){
                $(v).prop("checked",false);
                $(this).parents('.inbox-single-mail-block').removeClass('checkbox-selected');
            });
        }
    });

    // Add active class to all checked checkbox's parent div
    $(document).on('click','.mail-single-checkbox',function(){
        if($(this).is(":checked")){
            $(this).parents('.inbox-single-mail-block').addClass('checkbox-selected');
        }else{
            $(this).parents('.inbox-single-mail-block').removeClass('checkbox-selected');
        }
    });

    // scan request form validation
    $('.scan-request-form').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            image_quality: {
                required: true,
            },
            post_scan: {
                required: true,
            },
            spacial_request: {
                maxlength: 140,
            },
        },
        errorPlacement: function(error,element){
            if($(element).hasClass('image-quality')){
                error.insertAfter($(element).parents('.inbox-radio-cover'));
            }
            if($(element).hasClass('post_scan')){
                error.insertAfter($(element).parents('.post-scan-block'));
            }
            if($(element).hasClass('spacial-request')){
                error.insertAfter($(element).parents('.inbox-content-scan-input'));
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

            $('modal').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            image_quality: {
                required: 'Please choose image quality.'
            },
            post_scan: {
                required: 'Please choose post scan action',
            },
            spacial_request: {
                minlength: 'Please use 140 characters or less.',
            }
        }
    });

    // overriding popup open method

    $(document).on('click','.scan-btn',function(event){
        if(isPaymentAvailable){

            if(smbStatus){
                if(window['currentRoom'] !== undefined && window['currentRoom'] != 'pending'){
                    event.preventDefault();
                    let id = [];
                    let alreadyScanned = [];
                    let alreadyShipped = false;
                    var alreadyShred = false;
                    // get select mail ids
                    $.each($('.mail-single-checkbox'),function(i,v){
                        if($(this).is(":checked")){
                            id.push($(this).attr('data-id'));
                            if($(this).attr('data-scan') == 1){
                                alreadyScanned.push($(this).attr('data-id'));
                            }

                            if(($(this).attr('data-shipped') == 1) && alreadyShipped == false){
                                alreadyShipped = true;
                            }

                            /*already request*/
                            if($(this).attr('data-shred') == 1 && alreadyShred == false){
                                alreadyShred = true;
                            }
                        }        
                    });

                    if(id.length > 0){
                        if(alreadyScanned.length > 0){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "You can not submit scan request for in-process or already scanned mail",
                                callback: function (result) {}
                            });
                        }else if(alreadyShipped){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "You can not submit scan request for in-process or already shipped mail",
                                callback: function (result) {}
                            });
                        }else if(alreadyShred){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "You can not submit scan request for in-process or already shreded mail",
                                callback: function (result) {}
                            });
                        }else{
                            window['shredOption'] = '';
                            window['showScanChargealert'] = false;
                            window['scanRequestCharge'] = 0;
                            window['scanRemainRequest'] = 0;

                            promise = new Promise(function(resolve, reject) {
                                // check remaining scan request credit
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: 'POST',
                                    url: getScanCreditPath,
                                    dataType: "json",
                                    success: function(result){
                                        resolve(result);
                                    },
                                    error: function(jqXHR, textStatus, errorThrown){
                                        csrfTokenHandler(jqXHR);
                                        if(jqXHR.status){
                                            bootbox.alert({
                                                title: "Alert!!",
                                                message: jqXHR.responseJSON.message,
                                                callback: function (result) {
                                                }
                                            });
                                        }
                                    }
                                });
                              }).then(function(result){
                                    if(result.status && result.credit_status ==2){
                                        $('.sc-credit-section').html(`<strong>Your remaining scan request credit(no of mails) is ${result.credit.remaining_credit} (Out of ${result.credit.total_credit}).</strong><br/><br/>`);

                                        window['showScanChargealert'] = true;
                                        window['scanRequestCharge'] = result.credit.charge;
                                        window['scanRemainRequest'] = result.credit.remaining_credit;
                                    }
                                    $('#content_scan_modal').modal('show');                                     
                              });                            
                        }
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Please select mail first",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: commonMessage,
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    // reset scan request form on modal shown
    $('#content_scan_modal').on('shown.bs.modal', function () {
        $('span.help-block').remove();
        $('.scan-request-form').trigger('reset');
        $('.post_scan_envelope').prop("disabled", false);

        if(window['fromShredPop'] !== undefined && window['fromShredPop'] == true){
            $('.post_scan_shred').prop("checked", true);
            $('.post_scan_envelope').prop("disabled", true);
            window['fromShredPop'] = false;
        }
    })

    // submit scan request
    $(document).on('click','.scan-request-btn',function(){
        if($('.scan-request-form').valid()){
            let id = []; //scan request id
            let shredId = []; //scan request id
            // get select mail ids
            $.each($('.mail-single-checkbox'),function(i,v){
                if($(this).is(":checked")){

                    // scan request array
                    if($(this).attr('data-scan') == 0){
                        id.push($(this).attr('data-id'));
                    }

                    // shred request array
                    if($(this).attr('data-scan') == 1 && $(this).attr('data-shred') == 0){
                        shredId.push($(this).attr('data-id'));
                    }

                }        
            });
            
            // creating form object
            var form = $(".scan-request-form");
            var MForm = new FormData(form[0]);

            // Add Scan ID
            MForm.append('id', id);

            // Add Scan ID
            MForm.append('shred_id', shredId);
            
            if(window['shredOption'] !== undefined){
                // Add shred option
                MForm.append('shred_option', window['shredOption']);
            }
            
            if(window['showScanChargealert'] && id.length > window['scanRemainRequest']){

                // $('#content_scan_modal').modal('hide');
                var charges = (id.length - window['scanRemainRequest']) * window['scanRequestCharge'];

                bootbox.confirm({
                    title: "Action Required!",
                    message: `You are going over your plan limit, you will be charge $${charges} for this additional scan this month.`,
                    buttons: {
                        confirm: {
                            label: 'Confirm',
                            className: ' bootbox-alert-ok-btn scan-charge-cus-btn'
                        }
                    },
                    callback: function (result) {
                        if(result){
                            MForm.append('additional_charge', charges);
                            submitScanRequest(MForm);
                        }else{
                            bootbox.hideAll();
                            setTimeout(() => {
                                $('body').attr('class','modal-open');    
                            }, 500);
                        }
                    }
                });
            }else{
                submitScanRequest(MForm);
            }
        }
    });

    /**This function will trigger shred confirmation model */
    $(document).on('click','.shred-option-cls',function(){
        event.preventDefault();

        if(isPaymentAvailable){
            if(smbStatus){
                let id = [];
                let alreadyShred = [];
                let alreadyShipped = false;
                let alreadyScanned = true;
                let alreadyInProcess = false;
                // get select mail ids
                $.each($('.mail-single-checkbox'),function(i,v){
                    if($(this).is(":checked")){
                        id.push($(this).attr('data-id'));

                        if($(this).attr('data-shred') == 1){
                            alreadyShred.push($(this).attr('data-id'));
                        }

                        if(($(this).attr('data-shipped') == 1) && alreadyShipped == false){
                            alreadyShipped = true;
                        }

                        if(($(this).attr('data-scan') == 0) && alreadyScanned == true){
                            alreadyScanned = false;
                        }

                        if(($(this).attr('mail-status') == 1) && alreadyInProcess == false){
                            alreadyInProcess = true;
                        }
                    }        
                });

                if(id.length > 0){
                    if(!alreadyShipped){
                        if(alreadyShred.length > 0){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "You can not submit shred request for already shred mail",
                                callback: function (result) {}
                            });
                        }else if(alreadyInProcess){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "You can not request for the mail which is already in-process",
                                callback: function (result) {}
                            });
                        }else{
                            
                            if(!alreadyScanned){
                                // show scan related data from shred confirmation
                                $('.shared-confirm-model-text').show();
                                $('.shared-confirm-scan-btn').show();
                            }else{
                                // hide scan related data from shred confirmation
                                $('.shared-confirm-model-text').hide();
                                $('.shared-confirm-scan-btn').hide();
                            }

                            $('#confirm_modal').modal('show');   
                            window['shredOption'] = $(this).attr('data-type');
                        }
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "You can not submit shred request for already shipped mail",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select mail first",
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    // submit scan request
    $(document).on('click','.shred-request-btn',function(){
        let id = [];
        // get select mail ids
        $.each($('.mail-single-checkbox'),function(i,v){
            if($(this).is(":checked")){
                id.push($(this).attr('data-id'));
            }        
        });
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: saveShredReuqestePath,
            dataType: "json",
            data: {
                id:id,
                type: window['shredOption']
            },
            success: function (response) {

                if(response.status){
                    getInboxMails(1);
                    $('#confirm_modal').modal('hide');
                    $('.mail-select-all-checkbox').prop("checked",false);
                }else{
                    $('#confirm_modal').modal('hide');
                    $('input:checkbox').prop("checked",false);
                    bootbox.alert({
                        title: "Alert!!",
                        message: response.message,
                        callback: function (result) {
                        }
                    });
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
                csrfTokenHandler(jqXHR);
                $('#confirm_modal').modal('hide');
                $('.mail-select-all-checkbox').prop("checked",false);
                bootbox.alert({
                    title: "Alert!!",
                    message: jqXHR.responseJSON.message,
                    callback: function (result) {
                    }
                });
            }
        });
    });

    /**To show scan popup when user click on scan first in shred model */
    $(document).on('click','.shared-confirm-scan-btn',function(){
        window['fromShredPop'] = true;
        $('#confirm_modal').modal('hide');
        $('#content_scan_modal').modal('show');
    });

    /**Ship popup */
    $(document).on('click','.ship-option-cls',function(){
        if(isPaymentAvailable){
            if(smbStatus){
                if(window['currentRoom'] !== undefined && window['currentRoom'] != 'pending'){

                    let id = [];
                    let alreadyShipped = false;
                    let alreadyShred = false;
                    let inProcess = false;
                    window['shipping_try'] = 0;

                    // get select mail ids
                    $.each($('.mail-single-checkbox'),function(i,v){
                        if($(this).is(":checked")){
                            id.push($(this).attr('data-id'));

                            if($(this).attr('data-shred') == 1){
                                alreadyShred = true;
                            }

                            if(($(this).attr('data-shipped') == 1) && alreadyShipped == false){
                                alreadyShipped = true;
                            }

                            if(($(this).attr('mail-status') == 1)){
                                inProcess = true;
                            }
                        }        
                    });

                    if(id.length > 0){
                        // if(id.length > 1){
                        //     bootbox.alert({
                        //         title: "Alert!!",
                        //         message: "Please select single mail to ship",
                        //         callback: function (result) {}
                        //     });
                        // }else{
                            if(!alreadyShipped){ 
                                if(!alreadyShred){
                                    if(!inProcess){
                                        // to open shipping popup
                                        $('.shipping-step-one-form').trigger('reset');
                                        $('.shipping-modal1-box-first').removeClass('active');
                                        $('.custom-error-block').html('');
                                        openShippingPopup();
                                    }else{
                                        bootbox.alert({
                                            title: "Alert!!",
                                            message: commonMessage,
                                            callback: function (result) {}
                                        });
                                    }
                                }else{
                                    bootbox.alert({
                                        title: "Alert!!",
                                        message: "You can not submit shipping request for already shreded mail",
                                        callback: function (result) {}
                                    });
                                }
                            }else{
                                bootbox.alert({
                                    title: "Alert!!",
                                    message: "You can not submit shipping request for already shipped mail",
                                    callback: function (result) {}
                                });
                            }
                        //}
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Please select mail first",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: commonMessage,
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    // Ship request form validation

    $('.shipping-address-form').validate({
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
                maxlength: 30,
            },
            address_line1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                maxlength: 250,
            },
            address_line2: {
                maxlength: 250,
            },
            city: {
                required: true,
                maxlength: 50,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            phone_number: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            // state: {
            //     required: true
            // },
            postal_code: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 5,
                maxlength: 8,
            }
        },
        errorPlacement: function(error,element){
            // if($(element).hasClass('state')){
            //     error.insertAfter($(element).next('.jcf-select'));
            // }else{
                error.insertAfter($(element));
            // }
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

            $('modal').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            spacial_request: {
                minlength: 'Please use 140 characters or less.',
            },
            postal_code: {
                minlength: 'Entered characters should not be less than 5.',
                maxlength: 'Entered characters should not be more than 8.',
            },
            city: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            address_line1: {
                maxlength: 'Entered characters should not be more than 250.',
            },
            address_line2: {
                maxlength: 'Entered characters should not be more than 250.',
            },
            name: {
                maxlength: 'Entered characters should not be more than 30.',
            },
        }
    });

    // shipping request form
    $('.shipping-request-form').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            spacial_request: {
                maxlength: 140,
            }
        },
        errorPlacement: function(error,element){
            if($(element).hasClass('state')){
                error.insertAfter($(element).next('.jcf-select'));
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

            $('modal').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            spacial_request: {
                maxlength: 'Please use 140 characters or less.',
            }
        }
    });

    $(document).on('click','.add-shipping-address-btn',function(event){
        event.preventDefault();
        $('.shipping-address-form').trigger('reset');
        $('#scret_id').val('');

        // update states 
        $('.state-dropdown-cls').empty();
        $('.state-required-mark').html('*');
        var firstOptionVal = '';
        $.each(getStateLists, function (i, item) {
            if(firstOptionVal == ''){
                firstOptionVal = i;
            }
            $('.state-dropdown-cls').append($('<option>', { 
                value: i,
                text : item
            }));
        });
        $('.state-dropdown-cls').val(firstOptionVal);
        $('.state-dropdown-cls').rules('remove',  'required');

        // $('.state-dropdown-cls').prop('required',true);
        // jcf.getInstance(jQuery(".state-dropdown-cls")).refresh();

        $('#shipping_address_modal').modal('show');
        $('#shipping-modal1').modal('hide');
        $('.help-block').hide();

        $(document).on('change','.country-dropdown-cls',function(){
       
            let countryId = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: getRegionsPath,
                dataType: "json",
                data:{country_id:countryId},
                success: function (response) {
                    if(response.status){
                        
                        $('.state-dropdown-cls').empty();
                        
                        $.each(response.data, function (i, item) {
                            $('.state-dropdown-cls').append($('<option>', { 
                                value: item.id,
                                text : item.value
                            }));
                        });
                        $('.state-dropdown-cls').rules('remove',  'required');
                        if(response.data.length > 0){
                            $('.state-required-mark').html('*');
                            // $('.state-dropdown-cls').prop('required',true);
                        }else{
                            $('.state-required-mark').html('');
                            // $('.state-dropdown-cls').prop('required',false);
                        }

                        // jcf.getInstance(jQuery(".state-dropdown-cls")).refresh();

                    }
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    csrfTokenHandler(jqXHR);
                }
            });
        });
    });
    
    
    $(document).on('click','.shipping-address-btn',function(){
       if($('.shipping-address-form').valid()){

        // creating form object
           var form = $('.shipping-address-form');
           var MForm = new FormData(form[0]);

           // Getting user
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: storeShippingAddressPath,
                dataType: "json",
                processData: false,
                contentType: false, 
                data: MForm,
                success: function (response) {
                    
                    $('#shipping_address_modal').modal('hide');
                    // to open shipping popup with updated address
                    openShippingPopup();

                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    csrfTokenHandler(jqXHR);
                    $.unblockUI()
                    $('#shipping_address_modal').modal('hide');

                    bootbox.alert({
                        title: "Alert!!",
                        message: jqXHR.responseJSON.message,
                        callback: function (result) {
                        }
                    });
                }
            });
       }
    });

    $(document).on('click','.shipping-address-cancel-btn',function(event){
        event.preventDefault();
        // Hide address model
        $('#shipping_address_modal').modal('hide');
        
        // to open shipping popup with updated address
        // openShippingPopup();
    });

    $('#shipping_address_modal').on('hidden.bs.modal', function () {
        // to open shipping popup with updated address
        openShippingPopup();
    });

    /** select address on click */ 
    $(document).on('click','.shipping-modal1-box-first',function(){
        $('.shipping-modal1-box-first').removeClass('active');
        $(this).addClass('active');
    });

    /** Shipping address step one data submition */ 
    $(document).on('click','.shipping-address-step-1',function(){
        
        // creating form object
        var form = $(".shipping-step-one-form");
        var MForm = new FormData(form[0]);
        
        // get selected address
        let id = '';
        $.each($('.shipping-modal1-box-first'), function(i, v){
            if($(v).hasClass('active')){
                id = $(v).attr('data-id');
            }
        });
        
        if(id != ''){

            // get selected mail
            let mailId = [];
            $.each($('.mail-single-checkbox'),function(i,v){
                if($(this).is(":checked")){
                    mailId.push($(this).attr('data-id'))
                }        
            });

            // Add address ID
            MForm.append('id', id);
            MForm.append('mail_id', mailId);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: getShippingPricesPath,
                dataType: "json",
                processData: false,
                contentType: false, 
                data: MForm,
                success: function (response) {
                    let html = '';
                    if(response.status){
                        $.each(response.rates, function(i, v){
                            html += '<div class="shipping-modal3-box-first" data-id="'+v.id+'"><div class="shipping-modal3-box-left">'+v.carrier+' ' +(v.est_delivery_days!==null?v.est_delivery_days+" Days":"") +' '+v.service+'</div><div class="shipping-modal3-box-right">$'+v.rate+' <span><img src="img/right-arrow-blue.svg"></span></div></div>';
                        });
                        window['shipping_try'] = 0;
                        $('.custom-error-block').empty();
                        $('.shipping-modal2-fetching').hide();
                        $('.shipping-rates-block').show();
                        $('.shipping-rates-block').html(html);
                        $('.carrier-rate-block').show();
                    }else{
                        window['shipping_try'] = window['shipping_try']+1;
                        let errorMessage = 'Opps something went wrong, please try with different address or you can try later.';
                        if(window['shipping_try'] >= 3){
                            errorMessage = 'Opps something went wrong, please try after sometime.'
                        }
                        $('.shipping-modal2-fetching').empty();
                        $('.shipping-modal2-fetching').html(errorMessage);
                        $('.shipping-modal2-fetching').show();

                        $('.shipping-rates-block').empty();
                        $('.shipping-rates-block').hide();
                        $('.carrier-rate-block').hide();
                    }


                    if(!response.status){

                        $('#shipping-modal1').modal('hide');

                        bootbox.alert({
                            title: "Alert!!",
                            message: 'Please select a valid address to continue.',
                            callback: function (result) {
                                $('#shipping-modal1').modal('show');
                                $('#shipping-modal1').on('shown.bs.modal', function (e) {
                                    $('body').attr('class','modal-open');
                                });
                            }
                        });

                    }else{
                        $('.shipping-request-form').trigger('reset');
                        $('#shipping-modal1').modal('hide');
                        $('#shipping-modal2').modal('show');

                        // Reset datetime picker
                        $('#datetimepicker12').datetimepicker('destroy').datetimepicker({
                            inline: true,
                            sideBySide: true,
                            minDate: moment().format('YYYY-MM-DDTHH:mm:ss'),
                            defaultDate:null,
                            daysOfWeekDisabled: [0,6]
                        });
                    }

                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    csrfTokenHandler(jqXHR);
                    $.unblockUI()
                    if(jqXHR.status == 401 || jqXHR.status == 406){
                        $('#shipping-modal1').modal('hide');
                        $('#shipping-modal2').modal('hide');
                        bootbox.alert({
                            title: "Alert!!",
                            message: jqXHR.responseJSON.message,
                            callback: function (result) {
                            }
                        });
                    }else{
                        $('.custom-error-block').empty();
                        $('.custom-error-block').html('<div class="alert alert-denger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Oops something went wrong!!</div>');
                        $('#shipping-modal1').animate({ scrollTop: 0 }, 'slow');
                    }
                }
            });
        }else{
            $('.custom-error-block').empty();
            $('.custom-error-block').html('<div class="alert alert-denger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please select address first.</div>');
            $('#shipping-modal1').animate({ scrollTop: 0 }, 'slow');
        }
    });

    /** back button on shipping modal 2 */ 
    $(document).on('click','.shipping-modal2-back',function(){
        $('#shipping-modal2').modal('hide');
        $('#shipping-modal1').modal('show');
    });

    /** select rates on click */ 
    $(document).on('click','.shipping-modal3-box-first',function(){
        $('.shipping-modal3-box-first').removeClass('active');
        $(this).addClass('active');
    });

    /** Submit Shipping request */
    $(document).on('click','.shipping-request-submit-btn',function(){

        // Getting carrier details
        let selectedRate = $('.shipping-modal3-box-first.active');

        if(selectedRate.length > 0){
            let rate_id = $(selectedRate).attr('data-id');

            // creating form object
            var form = $(".shipping-step-one-form");
            var MForm = new FormData(form[0]);
            
            // get selected address
            let id = '';
            $.each($('.shipping-modal1-box-first'), function(i, v){
                if($(v).hasClass('active')){
                    id = $(v).attr('data-id');
                }
            });

            // get selected mail
            let mailId = [];
            $.each($('.mail-single-checkbox'),function(i,v){
                if($(this).is(":checked")){
                    mailId.push($(this).attr('data-id'))
                }        
            });

            
            // Add address ID
            MForm.append('id', id);
            MForm.append('mail_id', mailId);
            MForm.append('shipping_date', $('#datetimepicker12').data('date'));
            MForm.append('special_instructions', $('.shipping-special-instructions').val());
            MForm.append('rate_id', rate_id);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveShippingReuqestPath,
                dataType: "json",
                processData: false,
                contentType: false, 
                data: MForm,
                success: function (response) {
                    if(response.status){
                        $(".shipping-step-one-form").trigger('reset');
                        $('.shipping-rates-block').html('');
                        $('.shipping-special-instructions').val('');
                        $('#shipping-modal2').modal('hide');
                        getInboxMails(1);
                    }else{
                        $('#shipping-modal2').modal('hide');
                        $('input:checkbox').prop("checked",false);
                        bootbox.alert({
                            title: "Alert!!",
                            message: response.message,
                            callback: function (result) {
                                
                            },
                        });
                    }
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    if(jqXHR.status != 422){
                        csrfTokenHandler(jqXHR);
                        // reset popup and fields
                        $(".shipping-step-one-form").trigger('reset');
                        $('.shipping-rates-block').html('');
                        $('.shipping-special-instructions').val('');
                        $('#shipping-modal2').modal('hide');

                        // showing alert
                        bootbox.alert({
                            title: "Alert!!",
                            message: jqXHR.responseJSON.message,
                            callback: function (result) {
                            }
                        });
                    }else{
                        $('.custom-error-block').empty();
                        $('.custom-error-block').html('<div class="alert alert-denger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Invalid form fields, please select "Carrier/Rate" and "Shipping date first."</div>');
                        $('#shipping-modal2').animate({ scrollTop: 0 }, 'slow');                
                    }
                }
            });
        }else{
            $('.custom-error-block').empty();
            $('.custom-error-block').html('<div class="alert alert-denger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please select "Carrier/Rate" first.</div>');
            $('#shipping-modal2').animate({ scrollTop: 0 }, 'slow');
        }
    });

    /** Create tag popup */
    $(document).on('click','.create-apply-tag',function(){
        
        // creating dynamic value
        window['parentMailId'] = '';
        window['parentMailId'] = $(this).attr('data-id');
        window['identityKey'] = '';
        window['identityKey'] = $(this).attr('data-key');

        // reset form
        $('.add-tag-form').trigger('reset');
        $('.help-block').hide();

        // get all avaible tags
        var allTags = [];
        $.each($('.sidemenu-tag-block a'), function(i, v){
            allTags.push($(v).html());
        });
        
        // tag inputs
    //     $("#tag").empty();
    //     $.each(allTags, function(key, value) {   
    //         $('#tag')
    //             .append($("<option></option>")
    //                        .attr("value", value)
    //                        .text(value)); 
    //    });

        // $("#tag").select2({
        //     tags: true
        //   });

        // $('#tag').tagsinput({
        //     typeahead:{
        //         source: allTags
        //     }
        // });
        
        var amsifySuggestags = $('input[name="tag"]').amsifySuggestags({
            type : 'amsify',
            suggestions: allTags,
            printValues: false,
            selectOnHover: false,
            afterAdd : function(value) {
                
                if(value.length > 30){
                    $('#tag-error').show();
                    $('#tag-error').html('Please create tag using 30 characters or less.');
                    $('.add-tag-submit-btn').prop('disabled',true);
                }else if($('#tag-error').html() == 'Please enter tag.'){
                    $('#tag-error').html('');
                    $('.add-tag-submit-btn').prop('disabled',false);
                }
            },
            afterRemove : function(value) {
                if(value.length > 30){
                    $('#tag-error').empty();
                    $('.add-tag-submit-btn').prop('disabled',false);
                }
            },
            // whiteList: true
        });

        // open modal
        $('#add_tag_modal').modal('show');
    });

    /** Add tag form validation */
    $('.add-tag-form1').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            tag: {
                required: true,
                maxlength: 30,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            }
        },
        errorPlacement: function(error,element){
            if($(element).hasClass('state')){
                error.insertAfter($(element).next('.jcf-select'));
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

            $('modal').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            tag: {
                minlength: 'Please use 30 characters or less.',
            }
        }
    });

     /** add tags submit button */
     $(document).on('click','.add-tag-submit-btn',function(){

        var e = $.Event('keyup');
        e.which = 13;
        $('.amsify-suggestags-input').trigger(e);
        
        if($('#tag').val()){

            // check if user has enter tag with more than 30 charactor
            let testPassed = true;
            
            $.each($('#tag').val().split(','), function(i, v){
                if(v.length > 30){
                    testPassed = false;
                }
            });

            if(testPassed){
                var form = $(".add-tag-form");
                var MForm = new FormData(form[0]);

                MForm.append('mail_id', window['parentMailId']);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: saveTagsPath,
                    dataType: "json",
                    processData: false,
                    contentType: false, 
                    data: MForm,
                    success: function (response){
                        if(response.status){
                            let html = '';
                            
                            $.each(response.tags,function(i, v){
                                html += '<span class="dropdown-item" href="javascript:void(0);">'+v.tag+'<a href="javascript:void(0);" class="inbox-delete-icon delete-tag-cls" data-id="'+v.encryped+'" data-mail="'+v.mail_id+'" data-key="'+window['identityKey']+'"><img src="'+globalImagePath+'/img/delete.svg")}}"></a></span>';
                            });

                            $('.unique-tags-'+window['identityKey']).html(html);

                            // reset form
                            $('.add-tag-form').trigger('reset');

                            // open modal
                            $('#add_tag_modal').modal('hide');

                            // update tags in sidemenu
                            updateTags();
                        }
                    },
                    beforeSend: function () {           
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        csrfTokenHandler(jqXHR);
                        var responseText = JSON.parse(jqXHR.responseText);
                        if (!responseText.status) {
                            $("span.help-block").remove();
                            $('.help-block').removeClass('help-block');

                            var error = '<span class="help-block" id="tag-error">' + responseText.message + '</span>';
                            $("#tag").after(error);
                        }
                    }
                });
            }else{
                $('#tag-error').show();
                $('#tag-error').html('Please create tag using 30 characters or less.');
                $('.add-tag-submit-btn').prop('disabled',true);
            }

        }else{
            $('#tag-error').empty();
            $('#tag-error').html('Please enter tag.');
            $('#tag-error').show();
        }
    });

    /**on focus lost */
    // $(document).on('blur','.amsify-suggestags-input',function(){
        
    // });

    // Function to handle amsify input bugs
    document.addEventListener("click", function(evt) {
        var flyoutElement = document.getElementsByClassName('amsify-list-item'),
        targetElement = evt.target;  // clicked element

        do {
            if (targetElement == flyoutElement) {
                // This is a click inside. Do nothing, just return.
                return;
            }
            // Go up the DOM
            targetElement = targetElement.parentNode;
        } while (targetElement);

        if($('#add_tag_modal').hasClass('show')){
            var e = $.Event('keyup');
            e.which = 13;
            $('.amsify-suggestags-input').trigger(e);
        }
    });

    /**Delete tags */
    $(document).on('click','.delete-tag-cls',function(event){
        
        event.preventDefault();

        let key = $(this).attr('data-key');
        let id = $(this).attr('data-id');
        let mail_id = $(this).attr('data-mail');

        bootbox.confirm({
            title: "Alert!!",
            message: "Are you sure want to delete the tag?",
            buttons: {
                confirm: {
                    label: 'Yes',
                },
                cancel: {
                    label: 'No',
                }
            },
            callback: function (result) {
                if(result){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: destroyTagPath,
                        dataType: "json",
                        data: { id:id, mail_id:mail_id },
                        success: function (response){
                            if(response.status){
                                let html = '';
                                
                                $.each(response.tags,function(i, v){
                                    html += '<span class="dropdown-item" href="javascript:void(0);">'+v.tag+'<a href="javascript:void(0);" class="inbox-delete-icon delete-tag-cls" data-id="'+v.encryped+'" data-mail="'+v.mail_id+'" data-mail="'+mail_id+'" data-key="'+key+'"><img src="'+globalImagePath+'/img/delete.svg")}}"></a></span>';
                                });
                                $('.unique-tags-'+key).empty();
                                $('.unique-tags-'+key).html(html);
        
                                $('.inbox-tag-dropdown-btn'+key).trigger('click');
        
                                // update tags in sidemenu
                                updateTags();
                            }
                        },
                        beforeSend: function () {           
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            csrfTokenHandler(jqXHR);
                        }
                    });
                }
            }
        });
    });

    /** Share Via Email popup show if a mail selected **/
    $(document).on('click','.share-mail-modal',function(){

        if(isPaymentAvailable){

            if(smbStatus){

                share_email_form.resetForm();
                $(".removable-emaal-div").remove();
                //$('#share_via_email_form').trigger("reset");
                event.preventDefault();
                let selectedMailId = [];
                let notScanned = [];
                // get select mail ids
                $.each($('.mail-single-checkbox'),function(i,v){
                    if($(this).is(":checked")){
                        selectedMailId.push($(this).attr('data-id'));
                        if($(this).attr('data-scan') == 0){
                            notScanned.push($(this).attr('data-id'));
                        }
                    }        
                });
                if(selectedMailId.length>0){
                    if(notScanned.length>0){
                        bootbox.alert({
                            title: "Alert!!",
                            message: commonScanMessage,
                            callback: function (result) {}
                        });
                    }else{
                        $("#selectedMailIds").val(selectedMailId);
                        $("#share_mail_modal").modal('show');
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select mail first",
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }

    });

    /*add new email address*/
    $(document).on('click','.add-new-eamil',function(){
        var newRow = '<div class="form-group form-group-share-mail removable-emaal-div"><div class="inbox-modal-share-email-cover"><input class="form-control" type="text" name="email_address[]" placeholder="Email address"></div><a href="#" class="user-modal-plusicon2 remove-email-address" onclick="removeEmailAddress(this);"><img src="'+minus_icon_img+'" width="20"></a></div>';
        $(".add-email-div").append(newRow);
    });

    //add sub user validation
    $.validator.addMethod("customShareEmailValidation", function (value, element) {
        var flag = true;

      $("[name^=email_address]").each(function (i, j) {
        $(this).parent('div').find('span.help-block').remove();
        //$(this).parent('p').find('label.error').remove();                        
        if ($.trim($(this).val()) == '') {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">This field is required.</span>');
        }else if(($.trim($(this).val()).length) > 255) {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">Entered characters should not be more than 255.</span>');
        }else{
            var emailText = $.trim($(this).val())
            var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
            if(!pattern.test(emailText)){
                flag=false;
                $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">Please enter valid email address.</span>');    
            }
        }

      });
      return flag;
    }, "");

    
    /** share via email form */
    var share_email_form = $('#share_via_email_form').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            "email_address[]":{customShareEmailValidation:true},
            message: {
                maxlength: 500,
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
            email_address: {
                maxlength: 'Entered characters should not be more than 255.'
            },
            message: {
                maxlength: 'Entered characters should not be more than 500.'
            }
        }
    });
    /* share via email functionality*/
    $(document).on('click', '.inbox-shar-modal-btn', function () {
        
        if ($('#share_via_email_form').valid()) {
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: shareViaEmail,
                dataType: "json",
                data: $('#share_via_email_form').serialize(),
                success: function (response) {
                    share_email_form.resetForm();
                    $("#share_mail_modal").modal('hide');
                    if (response.status) {
                        $('.mail-select-all-checkbox').prop("checked",false);
                        //var current_path = response.filename.split('/').pop();
                        var current_path = response.filename;
                        var isZip = current_path.split('.').pop();
                        //download_file(response.filename,current_path);
                        if(isZip=='zip'){
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: removeSharedEmailFile,
                                dataType: "json",
                                data: {filename:current_path},
                                success: function (response) {
                                    
                                },
                            });
                        }
                        
                        //remove zip temporary file
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Mail document(s) shared successfully.",
                            callback: function (result) {
                                getInboxMails(1);       
                            },
                        });
                        
                    }else{
                        if(response.filename){
                            //check access token and file on google drive
                            let selectedMailId = [];
                            let notScanned = [];
                            // get select mail ids
                            $.each($('.mail-single-checkbox'),function(i,v){
                                if($(this).is(":checked")){
                                    selectedMailId.push($(this).attr('data-id'));
                                    if($(this).attr('data-scan') == 0){
                                        notScanned.push($(this).attr('data-id'));
                                    }
                                }        
                            });
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: checkAccessToken,
                                dataType: "json",
                                data: {type:'GDRIVE',shareSelectedMailId:selectedMailId,sendEmail:1},
                                success: function (response) {
                                    
                                    if(response.status){
                                        //var current_path = response.filename.split('/').pop();
                                        var current_path = response.filename;
                                        //download_file(response.filename,current_path);
                                        var isZip = current_path.split('.').pop();
                                        if(isZip=='zip'){
                                            $.ajax({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                type: 'POST',
                                                url: removeSharedEmailFile,
                                                dataType: "json",
                                                data: {filename:current_path},
                                                success: function (response) {
                                                    
                                                },
                                            });
                                        }

                                        bootbox.alert({
                                            title: "Alert!!",
                                            message: "You have shared document(s) successfully.",
                                            callback: function (result) {
                                                $('.mail-select-all-checkbox').prop("checked",false);
                                                getInboxMails(1);     
                                            }
                                        });
                                    }else{
                                        window['sendEmail'] = 1;
                                        window['shareSelectedMailId'] = selectedMailId;
                                        window['shareType'] = "GDRIVE";
                                        authUrl = gdriveAuthAddresses;
                                        openPopupWindow(authUrl,500,400); 
                                    }
                                },
                                beforeSend: function () {           
                                },
                                error: function(jqXHR, textStatus, errorThrown){
                                }
                            });
                        }
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    csrfTokenHandler(jqXHR);
                    if(textStatus != "Unprocessable Entity"){//Unauthorized
                        bootbox.alert({
                            title: "Alert!!",
                            message: jqXHR.responseJSON.message,
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
    
    /** Trigger filter by tag */
    $(document).off('click','.sidebar-tag-cls');
    $(document).on('click','.sidebar-tag-cls',function(){
        $('.sidebar-tag-cls.active-item').not(this).removeClass('active-item');
        $(this).toggleClass('active-item');
        getInboxMails();
    });

    /** mail type filter by tag */

    $(document).off('click','.sidemenu-mail-filter');
    $(document).on('click','.sidemenu-mail-filter',function(){
        $('.sidemenu-mail-filter.active-item').not(this).removeClass('active-item');
        $(this).toggleClass('active-item');
        getInboxMails();
    });

    $(document).off('click','.sidebar-mailtype-cls');
    $(document).on('click','.sidebar-mailtype-cls',function(){
        $('.sidebar-mailtype-cls.active-item').not(this).removeClass('active-item');
        $(this).toggleClass('active-item');
        getInboxMails();
    });

    /** recipient filter by tag */
    // $(document).off('click','.sidemenu-recipient-cls');
    // $(document).on('click','.sidemenu-recipient-cls',function(){
    //     $('.sidemenu-recipient-cls.active').not(this).removeClass('active');
    //     $(this).toggleClass('active');
    //     getInboxMails();
    // });

    // Mail Sorting
    $(document).on('click','.inbox-sort-box-cls', function(){
        $('.inbox-sort-box-cls.active').not(this).removeClass('active');
        $(this).toggleClass('active');
        $('.inbox-sort-box-title').html($(this).text());
        getInboxMails();
    });
    
    /** Share Via box.com popup show if a mail selected **/
    $(document).on('click','.share-mail-class',function(){

        event.preventDefault();

        if(isPaymentAvailable){

            if(smbStatus){
                var shareType = '';
                var authUrl = '';
                if($(this).hasClass('share-via-box')){
                    shareType = 'BOX';
                    authUrl = boxAuthAddresses;
                }else if($(this).hasClass('share-via-dropbox')){
                    shareType = 'DROPBOX';
                    authUrl = dropboxAuthAddresses
                }else if($(this).hasClass('share-via-gdrive')){
                    shareType = 'GDRIVE';
                    authUrl = gdriveAuthAddresses;
                }
                let selectedMailId = [];
                let notScanned = [];
                // get select mail ids
                $.each($('.mail-single-checkbox'),function(i,v){
                    if($(this).is(":checked")){
                        selectedMailId.push($(this).attr('data-id'));
                        if($(this).attr('data-scan') == 0){
                            notScanned.push($(this).attr('data-id'));
                        }
                    }        
                });
                if(selectedMailId.length>0){
                    if(notScanned.length>0){
                        bootbox.alert({
                            title: "Alert!!",
                            message: commonScanMessage,
                            callback: function (result) {}
                        });
                    }else{
                        
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            url: checkAccessToken,
                            dataType: "json",
                            data: {type:shareType,shareSelectedMailId:selectedMailId},
                            success: function (response) {
                                
                                if(response.status){
                                    //var current_path = response.filename.split('/').pop();
                                    var current_path = response.filename;
                                    //download_file(response.filename,current_path);
                                    var isZip = current_path.split('.').pop();
                                    if(isZip=='zip'){
                                        $.ajax({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            type: 'POST',
                                            url: removeSharedEmailFile,
                                            dataType: "json",
                                            data: {filename:current_path},
                                            success: function (response) {
                                                
                                            },
                                        });
                                    }
                                    bootbox.alert({
                                        title: "Alert!!",
                                        message: "You have shared document(s) successfully.",
                                        callback: function (result) {
                                            $('.mail-select-all-checkbox').prop("checked",false);
                                            getInboxMails(1);     
                                        }
                                    });
                                }else{
                                    window['sendEmail'] = 0;
                                    window['shareSelectedMailId'] = selectedMailId;
                                    window['shareType'] = shareType;
                                    openPopupWindow(authUrl,500,400); 
                                }
                            },
                            beforeSend: function () {           
                            },
                            error: function(jqXHR, textStatus, errorThrown){
                                csrfTokenHandler(jqXHR);
                            }
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select mail first",
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    /*Code for download files*/
    $(document).on('click','.download-files',function(){

        if(isPaymentAvailable){

            if(smbStatus){

                let selectedMailId = [];
                let notScanned = [];
                // get select mail ids
                $.each($('.mail-single-checkbox'),function(i,v){
                    if($(this).is(":checked")){
                        selectedMailId.push($(this).attr('data-id'));
                        if($(this).attr('data-scan') == 0){
                            notScanned.push($(this).attr('data-id'));
                        }
                    }        
                });
                if(selectedMailId.length>0){
                    if(notScanned.length>0){
                        bootbox.alert({
                            title: "Alert!!",
                            message: commonScanMessage,
                            callback: function (result) {}
                        });
                    }else{
                        //download file
                        
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            url: downloadFiles,
                            dataType: "json",
                            data: {shareSelectedMailId:selectedMailId},
                            success: function (response) {
                                
                                if(response.status){
                                    //var current_path = response.filename.split('/').pop();
                                    var current_path = response.filename;
                                    $isDownload = download_file(response.filename,response.displayFileName);

                                    var isZip = current_path.split('.').pop();
                                    if(isZip=='zip' && $isDownload){
                                        setTimeout(function() {
                                            $.ajax({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                type: 'POST',
                                                url: removeSharedEmailFile,
                                                dataType: "json",
                                                data: {filename:current_path},
                                                success: function (response) {
                                                    $('.mail-select-all-checkbox').prop("checked",false);
                                                    getInboxMails(1);           
                                                },
                                            });
                                        }, 5000);
                                    }
                                }else{
                                    bootbox.alert({
                                        title: "Alert!!",
                                        message: "Something went wrong, Please try again.",
                                        callback: function (result) {}
                                    });
                                }
                            },
                            beforeSend: function () {           
                            },
                            error: function(jqXHR, textStatus, errorThrown){
                                csrfTokenHandler(jqXHR);
                            }
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select mail first",
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }

    });

    /**Move to functionlity */
    $(document).on('click','.move-to-cls', function(event){
        
        if(isPaymentAvailable){

            if(smbStatus){
                if(window['currentRoom'] !== undefined && window['currentRoom'] != 'pending'){
                    event.preventDefault();
                    
                    let selectedMailId = [];
                    let roomType = $(this).attr('data-key');
                    let recipientId = null;
                    let mailInProcess = false;

                    // get select mail ids
                    $.each($('.mail-single-checkbox'),function(i,v){
                        if($(this).is(":checked")){
                            selectedMailId.push($(this).attr('data-id'));

                            if(!mailInProcess){
                                if($(this).attr('mail-status') == 1 || $(this).attr('mail-status') == 3 || $(this).attr('mail-status') == 5 || $(this).attr('mail-status') == 7){
                                    mailInProcess = true;
                                }
                            }
                        }
                    });

                    // prevent in-process mails
                    
                    if(!mailInProcess){
                        if($(this).attr('data-key') == 3){
                            recipientId = $(this).attr('data-id');
                        }
                        
                        if(selectedMailId.length>0){
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: saveMailRoomPath,
                                dataType: "json",
                                data: {mail_id:selectedMailId,room_type:roomType,recipient_id:recipientId},
                                success: function (response) {
                                    
                                    getInboxMails();
                                },
                                beforeSend: function () {           
                                },
                                error: function(jqXHR, textStatus, errorThrown){
                                    csrfTokenHandler(jqXHR);
                                    // showing alert
                                    bootbox.alert({
                                        title: "Alert!!",
                                        message: jqXHR.responseJSON.message,
                                        callback: function (result) {
                                        }
                                    });
                                }
                            });
                        }else{
                            bootbox.alert({
                                title: "Alert!!",
                                message: "Please select mail first",
                                callback: function (result) {}
                            });
                        }
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "You can not move the mail which is already in-process",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "You can not move the mail which is already in-process",
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    /** sidemenu room */
    $(document).on('click', '.sidemenu-room', function(){
        // alert($(this).attr('data-room'));
        $('.header-search-box').val('');
        if($(this).attr('data-url') != 'inbox'){

            // Set cookie to remember the page name
            setCookie('page',$(this).attr('data-room'),1);

            // redirect on index page
            location.replace($(this).attr('data-href'));
        }else{

            $('.sidemenu-room.active-item').removeClass('active-item');
            $('.sidemenu-recipient-anchor.active-item').removeClass('active-item');
            $(this).addClass('active-item');

            // if($('.sidemenu-room.active-item').length == 0){
            //     $('.sidemenu-room-inbox').addClass('active-item');
            // }

            getInboxMails();
        }
    });

    /** sidemenu recipient */
    $(document).off('click','.sidemenu-recipient-anchor');
    $(document).on('click','.sidemenu-recipient-anchor',function(){
        $('.sidemenu-recipient-anchor.active-item').removeClass('active-item');
        $('.sidemenu-room.active-item').removeClass('active-item');
        $(this).addClass('active-item');

        // if($('.sidemenu-recipient-anchor.active-item').length == 0){
        //     $('.sidemenu-room-inbox').addClass('active-item');
        // }

        getInboxMails();
        // updateMoveTo();
    });

    // search function
    $(document).on('click','.header-search-btn', function(){
        if($.trim($('.header-search-box').val()) != ''){
            $('.sidemenu-room.active-item').removeClass('active-item');
            $('.clearable__clear').show();
            getInboxMails();
        }
    });

    // search on enter

    $('.header-search-box').keypress(function(e){
        if(e.which == 13){//Enter key pressed
            $('.header-search-btn').click();//Trigger search button click event
        }
    });

    // Clear search
    $(document).on('click','.clearable__clear',function(){
        $('.sidemenu-room').removeClass('active-item');
        $('.sidemenu-room-inbox').addClass('active-item');
        $('.clearable__clear').hide();
        $('.header-search-box').val('');
        getInboxMails();
    });

    /**detail button */
    $(document).on('click','.detail-btn-cls',function(){
        // show preview tab
        $('#home-tab').tab('show');
        
        $('html, body').animate({
            scrollTop: ($('.inbox-right-tab-content-left').offset().top - 100)
        }, 500);
    });

    // close tag modal
    $(document).on('click','.close-tag-modal',function(){
        $('.help-block').hide();
        $('#add_tag_modal').modal('hide');
    });
    /** cheque deposit popup **/
    $(document).on('click','.show-check-deposit-popup',function(event){
        //share_email_form.resetForm();
        //$(".removable-emaal-div").remove();
        //$('#share_via_email_form').trigger("reset");
        //alert(window['currentRoom']);

        if(isPaymentAvailable){
            if(smbStatus){
                if(window['currentRoom'] !== undefined && window['currentRoom'] != 'pending'){
                    event.preventDefault();
                    let selectedMailId = [];
                    let notCheck = [];
                    var alreadyDeposit = false;
                    var alreadyShipped = false;
                    var alreadyScannedNew = false;
                    var alreadyScanned = false;
                    var alreadyShred = false;
                    var ScanInProcess = false;
                    // get select mail ids
                    $.each($('.mail-single-checkbox'),function(i,v){
                        if($(this).is(":checked")){
                            selectedMailId.push($(this).attr('data-id'));
                            if($(this).attr('data-check') == 0){
                                notCheck.push($(this).attr('data-id'));
                            }
                            if(($(this).attr('data-deposit') == 1) && alreadyDeposit == false){
                                alreadyDeposit = true;
                            }

                            /*already request*/
                            if($(this).attr('data-shred') == 1 && alreadyShred == false){
                                alreadyShred = true;
                            }

                            if(($(this).attr('data-shipped') == 1) && alreadyShipped == false){
                                alreadyShipped = true;
                            }

                            if(($(this).attr('data-scan') == 1) && alreadyScannedNew == false){
                                alreadyScannedNew = true;
                            }

                            if(($(this).attr('mail-status') == 1) && ScanInProcess == false){
                                ScanInProcess = true;
                            }
                        }        
                    });
                    if(selectedMailId.length>0){
                        if(selectedMailId.length > 1){
                            bootbox.alert({
                                title: "Alert!!",
                                message: "Please select single mail to deposit",
                                callback: function (result) {}
                            });
                        }else{
                            if(notCheck.length>0){
                                bootbox.alert({
                                    title: "Alert!!",
                                    message: "You can not select a mail that do not have a check",
                                    callback: function (result) {}
                                });
                            }else{
                                if(alreadyDeposit){
                                    bootbox.alert({
                                    title: "Alert!!",
                                    message: "You can not submit check deposit request for already check deposit mail.",
                                    callback: function (result) {}
                                });
                                }else{
                                    if(alreadyScannedNew){
                                        var checkPending = checkAlreadyPending(alreadyShred,alreadyShipped,alreadyScanned,0,ScanInProcess);
                                        if(checkPending){
                                            $("#selected_mail_id").val(selectedMailId);
                                            check_deposit_form.resetForm();
                                            $("#checkdeposit_modal").modal('show');
                                        }
                                    }else{
                                        bootbox.alert({
                                            title: "Alert!!",
                                            message: "To generate a deposit request you need to scan the mail first.",
                                            callback: function (result) {}
                                        });
                                    }
                                }
                            }
                        }
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Please select mail first",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: commonMessage,
                        callback: function (result) {}
                    });
                }
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: smbcommonMessage,
                    callback: function (result) {}
                });
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
                        className: ' bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    });

    /** cheque deposit popup **/
    $(document).on('click','.add-new-check-account-btn',function(event){
        $('#form_validate_bank_account').resetForm();
        $("#checkdeposit_modal").modal('hide');
        $('#add_bank_account').modal('show');
    });

    

    /*validate check deposit */
    var check_deposit_form = $('#check_deposit_form').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            account_type: {
                required: true
            },
            post_scan: {
                required: true
            }
        },
        // errorPlacement: validateErrorPositioning,
        errorPlacement: function(error,element){
          if($(element).prop('nodeName') == 'SELECT'){
            error.insertAfter($(element).next('span'));
          }else if($(element).prop('type') == 'radio'){
            error.insertAfter($(element).parent('label').parent('div').next('div'));
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
    });

    /*add check-deposit request*/
    $(document).on('click','.add-check-deposit-btn',function(event){
        if ($('#check_deposit_form').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveCheckDepositRequest,
                dataType: "json",
                data: $('#check_deposit_form').serialize(),
                success: function (response) {
                    check_deposit_form.resetForm();
                    //$("#share_mail_modal").modal('hide');
                    if (response.status) {
                        $("#checkdeposit_modal").modal('hide');
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Your check deposit request has been sent to administrator",
                            callback: function (result) {
                                getInboxMails(1);
                            },
                        });
                    }else{
                        $("#checkdeposit_modal").modal('hide');
                        $('input:checkbox').prop("checked",false);
                        bootbox.alert({
                            title: "Alert!!",
                            message: response.message,
                            callback: function (result) {
                                
                            },
                        });
                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    csrfTokenHandler(jqXHR);
                    $.unblockUI()
                    if(textStatus != "Unprocessable Entity"){//Unauthorized
                        
                        bootbox.alert({
                            title: "Alert!!",
                            message: jqXHR.responseJSON.message,
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

    $('#add-tag-form').submit(function(e){
        e.preventDefault();
    });

    // shipping page js
    $(document).on('click','.add-shipping-address-p-btn',function(){
        // console.log($(this).attr('data-id'));
        let id = '';

        if($(this).attr('data-id') !== undefined){
            id = $(this).attr('data-id');
        }
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getShippingAddressFormPath,
            dataType: "json",
            data:{id},
            success: function (response) {
                if(response.status){
                    $('.add-shipping-modal-content').html(response.data)
                    $('#shipping_address_modal').modal('show');
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
                csrfTokenHandler(jqXHR);
            }
        });

        // load state according to country id
        $(document).on('change','.country-dropdown-cls',function(){

            let countryId = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: getRegionsPath,
                dataType: "json",
                data:{country_id:countryId},
                success: function (response) {
                    if(response.status){
                        
                        $('.state-dropdown-cls').empty();
                        
                        $.each(response.data, function (i, item) {
                            $('.state-dropdown-cls').append($('<option>', { 
                                value: item.id,
                                text : item.value
                            }));
                        });

                        $('.state-dropdown-cls').rules('remove',  'required');
                        if(response.data.length > 0){
                            $('.state-required-mark').html('*');
                            // $('.state-dropdown-cls').prop('required',true);
                        }else{
                            $('.state-required-mark').html('');
                            // $('.state-dropdown-cls').prop('required',false);
                        }

                    }
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    csrfTokenHandler(jqXHR);
                }
            });
        });
    });

    // delete shipping address
    $(document).on('click','.delete-shipping-address',function(){
    
        let id = $(this).attr('data-id');
        bootbox.confirm({
            title: "Alert!!",
            message: "Are you sure want to delete the address?",
            backdrop:true,
            onEscape:true,
            buttons: {
                confirm: {
                    label: 'Yes',
                    className:'btn primary-delete-btn-color'
                },
                cancel: {
                    label: 'No',
                }
            },
            callback: function (result) {
                if(result){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: deleteShipplingAddressPath,
                        dataType: "json",
                        data: { id },
                        success: function (response){
                            location.reload();
                        },
                        beforeSend: function () {           
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            csrfTokenHandler(jqXHR);
                        }
                    });
                }else{
                    bootbox.hideAll()
                }
            }
        });
    });

    // submit shipping address
    $(document).on('click','.submit-shipping-address-btn',function(){
        $('.shipping-address-form').submit();
    });

    // edit shipping address from popup
    $(document).on('click','.edit-shipping-address-btn',function(event){
        event.preventDefault();
        
        let id = $(this).attr('data-id');
        
        if($(this).attr('data-id') !== undefined){
            id = $(this).attr('data-id');
        }
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getSingleShippingAddress,
            dataType: "json",
            data:{id},
            success: function (response) {
                
                if(response.status){
                    
                    // reset old form
                    $('#shipping-modal1').modal('hide');
                    $('.shipping-address-form').trigger('reset');
                    $('.help-block').hide();

                    // update data
                    $('#address_type').val(response.data.address_type);
                    $('#name').val(response.data.receiver_name);
                    $('#address_line1').val(response.data.street1);
                    $('#address_line2').val(response.data.street2);
                    $('#phone').val(response.data.phone_number);
                    $('#city').val(response.data.city);
                    // $('#state').val(response.data.state_code);
                    $('#postal_code').val(response.data.zip_code);
                    $('#scret_id').val(response.data.encrypted);
                    $('#country').val(response.data.country_id);

                    $('#state').empty();
                    $.each(response.states, function (i, item) {
                        $('#state').append($('<option>', { 
                            value: item.id,
                            text : item.value
                        }));
                        
                    });
                    
                    $('.state-dropdown-cls').val(response.data.region_id);
                    
                    if(response.states.length > 0){
                        $('.state-required-mark').html('*');
                        $(".state-dropdown-cls").rules( "add", { required: true });

                    }else{
                        $('.state-required-mark').html('');
                        $(".state-dropdown-cls").rules( "add", { required: false });
                    }
                    
                    // show modal in edit mode
                    $('#shipping_address_modal').modal('show');

                    // jcf.getInstance($(".state-dropdown-cls")).refresh();
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
                csrfTokenHandler(jqXHR);
            }
        });

        $(document).on('change','.country-dropdown-cls',function(){
            let countryId = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: getRegionsPath,
                dataType: "json",
                data:{country_id:countryId},
                success: function (response) {
                    if(response.status){
                        
                        $('.state-dropdown-cls').empty();
                        
                        $.each(response.data, function (i, item) {
                            $('.state-dropdown-cls').append($('<option>', { 
                                value: item.id,
                                text : item.value
                            }));
                        });
                        $('.state-dropdown-cls').rules('remove',  'required');
                        if(response.data.length > 0){
                            $('.state-required-mark').html('*');
                        }else{
                            $('.state-required-mark').html('');
                        }

                        // jcf.getInstance(jQuery(".state-dropdown-cls")).refresh();

                    }
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                    csrfTokenHandler(jqXHR);
                }
            });
        });
    });

    // validate and submit green option form
    $(document).on('click','.green-option-class',function(){
        if($('input[name=greenoption]:checked').length > 0){
            $('.green-option-error-block').html('');
            $('.green-option-form').submit();
        }else{
            $('.green-option-error-block').html('<div class="alert alert-danger alert-dismissible custom-alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please choose an option to continue.</div>');
        }
    });
});

/** this function will be to get and display mail*/
function getInboxMails(page=1){
    
    // hide mobile resposive menu
    $('.nav-active').removeClass('nav-active');

    // mail room indicator
    $roomIndicator = false;

    // get active mail recipient
    let mailRecipient = null;
    let mailRecipientElement = $('.sidemenu-recipient-anchor.active-item');
    if(mailRecipientElement.length > 0){
        mailRecipient = mailRecipientElement.attr('data-id');
    }

    // sidemenu mail filter
    let mailFilter = null;
    let mailFilterElement = $('.sidemenu-mail-filter.active-item');
    if(mailFilterElement.length > 0){
        mailFilter = mailFilterElement.attr('data-id');
    }

    // get active tag id
    let tagId = null;
    let tagElement = $('.sidebar-tag-cls.active-item');
    if(tagElement.length > 0){
        tagId = tagElement.attr('data-id');
    }
    
    // get active mail type
    let mailType = null;
    let mailTypeElement = $('.sidebar-mailtype-cls.active-item');
    if(mailTypeElement.length > 0){
        mailType = mailTypeElement.attr('data-id');
    }

    // Get active sorting
    let mailSort = 0;
    let mailSortElement = $('.inbox-sort-box-cls.active');
    
    if(mailSortElement.length > 0){
        mailSort = mailSortElement.attr('data-id');
    }else{
        clearSort();
    }

    // .sidemenu-recipient-cls.active

    // get active mail room
    mailRoom = null;
    let MailRoomElement = $('.sidemenu-room.active-item');
    if(MailRoomElement.length > 0){
        mailRoom = MailRoomElement.attr('data-room');
        window['currentRoom'] = MailRoomElement.attr('data-room');
        if(MailRoomElement.attr('data-room') == 'all-mail'){
            $roomIndicator = true;
        }
    }
    // Set cookie to remember the page name
    let pageCookie = getCookie('page');
    if(pageCookie !== null){
        mailRoom = pageCookie;
        eraseCookie('page');
        
        $('.sidemenu-room.active-item').not(this).removeClass('active-item');
        $.each($('.sidemenu-room'),function(i, v){
            if($(v).attr('data-room') == mailRoom){
                $(v).addClass('active-item');
                window['currentRoom'] = mailRoom;

                if(mailRoom){
                    $roomIndicator = true;
                }
            }
        })
    }

    // get search text
    let searchParams = '';
    if($.trim($('.header-search-box').val()) != ''){
        searchParams = $('.header-search-box').val();
        $roomIndicator = true;
    }

    updateMoveTo();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getInboxMailsPath,
        dataType: "html",
        // async: false,
        data: {
            page,
            tag_id: tagId,
            mail_type: mailType,
            mail_sort: mailSort,
            mail_room: mailRoom,
            mail_recipient: mailRecipient,
            search_params: searchParams,
            selected_check_deposit_mail_id: $('#check_deposit_mail_id').val(),
            mail_filter: mailFilter
        },
        success: function (response) {
            
            response = JSON.parse(response);
            if(response.status){
                
                // hide content tab
                $('.content-nav-item').hide();

                // show preview tab
                $('#home-tab').tab('show');

                let previousPage = 1;
                let nextPage = parseInt(response.paging.current_page) + 1;
                
                if(response.paging.current_page > 2){
                    previousPage = parseInt(response.paging.current_page) - 1;
                }

                if(response.paging.current_page == response.paging.last_page){
                    nextPage = response.paging.last_page;
                }

                $('.index-mail-block').empty();
                $('.index-mail-block').html(response.htmlData);
                if(response.paging.total_record > 0){
                    $('.inbox-page-number').html('<span class="paginator-min-counter">'+response.paging.from+'</span> - <span class="paginator-max-counter">'+response.paging.to+'</span> of <span class="paginator-total-counter">'+response.paging.total_record+'</span>');

                    // Change cursor for min and max limits
                    if(response.paging.from == 1){
                        $('.inbox-arrow-left').addClass('default-cursor');
                    }else{
                        $('.inbox-arrow-left').removeClass('default-cursor');
                    }

                    if(response.paging.to == response.paging.total_record){
                        $('.inbox-arrow-right').addClass('default-cursor');
                    }else{
                        $('.inbox-arrow-right').removeClass('default-cursor');
                    }

                }else{
                    $('.inbox-page-number').html(response.paging.total_record+' - '+response.paging.total_record+' of '+response.paging.total_record);
                    
                    // change cursor when no record is available
                    $('.inbox-arrow-left').addClass('default-cursor');
                    $('.inbox-arrow-right').addClass('default-cursor');

                }
                $('.inbox-arrow-left').attr('data-page',previousPage)
                $('.inbox-arrow-right').attr('data-page',nextPage)
                
                if(response.paging.total_record > 0){
                    $('.inbox-page-pagination').show();
                }else{
                    $('.inbox-page-pagination').hide();
                }

                // empty preview div
                $('.inbox-mail-preview-block').html('<div class="inbox-right-no-item"><div class="inbox-right-no-item-text">No items selected</div></div>');

                if($roomIndicator){
                    $('.mail-room-indicator').show();   
                }else{
                    $('.mail-room-indicator').hide();
                }

                /* show pre selected mail if user comes from the check deposit mail*/
                $(".pre-selected-mail").trigger('click');
                $('#check_deposit_mail_id').val('');

                let activeRecipient = $('.sidemenu-recipient-anchor.active-item');
                
                // update recipients
                $('.sidebar-recipient-block').empty();
                $('.sidebar-recipient-block').html(response.recipients);
                if(activeRecipient.length > 0){
                    $.each($('.sidemenu-recipient-anchor'),function(i,v){
                        
                        if($(v).attr('data-slug') == $(activeRecipient).attr('data-slug')){
                            $(v).addClass('active-item');
                        }else{
                            $(v).removeClass('active-item');
                        }
                    });
                }

                // update mailroom unread mails counts
                $('.mail-room-count').hide();

                let indicator = $('.mail-room-count');
                let resIndie = response.indicator;
                $.each(indicator, function(i, v){
                    
                    let item = $(v).attr('data-type');
                    let itemSlug = $(v).attr('data-slug');
                    if(item != 'recipients'){
                        if (resIndie[item] && resIndie[item] > 0) {
                            if((item == 'mailroom') && resIndie[item] > 99){
                                $(v).html('99+');
                                $(v).show();
                            }else{
                                $(v).html(resIndie[item]);
                                $(v).show();
                            }
                        }else{
                            $(v).hide();
                        }
                    }else{
                        // console.log(resIndie.recipients);
                        if (resIndie.recipients[itemSlug] && resIndie.recipients[itemSlug] > 0) {
                            $(v).html(resIndie.recipients[itemSlug]);
                            $(v).show();
                        }else{
                            $(v).hide();
                        }
                    }
                });
                
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
            if(errorThrown=='Unauthorized'){
                location.reload();
            }
        }
    });

    // for sidemenu filtering

    $(document).on('keyup','.recipient-search-cls', function(){

        let searchValue = String($(this).val()).toLowerCase();
        if($.trim(searchValue)){
            $.each($('.sidemenu-recipient-cls'), function(i, v){

                let result = String($(v).attr('data-name')).toLowerCase().search(searchValue);
                if(result == -1){
                    $(v).hide();
                }else{
                    $(v).show();
                }
            });
        }else{
            $.each($('.sidemenu-recipient-cls'), function(i, v){
                    $(v).show();
            });
        }
    });
    // return true;

    // off previous click
    $(document).off('click','.address-delete-cls');
    $(document).on('click','.address-delete-cls', function(){
        $('#shipping-modal1').modal('hide');
        let id = $(this).attr('data-id');
        bootbox.confirm({
            title: "Alert!!",
            message: "Are you sure want to delete the address?",
            backdrop:true,
            onEscape:true,
            buttons: {
                confirm: {
                    label: 'Yes',
                },
                cancel: {
                    label: 'No',
                }
            },
            callback: function (result) {
                if(result){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: deleteShipplingAddressPath,
                        dataType: "json",
                        data: { id },
                        success: function (response){
                            //bootbox.hideAll()
                            openShippingPopup();

                            $('#shipping-modal1').on('shown.bs.modal', function (e) {
                                $('body').attr('class','modal-open');
                            });
                        },
                        beforeSend: function () {           
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            csrfTokenHandler(jqXHR);
                        }
                    });
                }else{
                    // bootbox.hideAll()
                    openShippingPopup();
                   
                    $('#shipping-modal1').on('shown.bs.modal', function (e) {
                        $('body').attr('class','modal-open');
                    });
                }
            }
        });
    });
}

/**This function will show mail's preview */
function getInboxMailPreview(id,that){

    // console.log(id);
        let parentDiv = $('.inbox-single-mail-block');
        let element = that;
        // $.each(parentDiv,function(i, v){
        //     if($(v).hasClass('inbox-left-list')){
        //         element = parentDiv;
        //     }else if($(v).attr('data-id') == id){
        //         element = $(v).parent('.inbox-left-list');
        //     }
        // });

        
        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getInboxMailPreviewPath,
        dataType: "json",
        // async: false,
        data: {id:id},
        success: function (response) {
            if(response.status){
                $('.inbox-mail-preview-block').empty();
                $('.inbox-mail-preview-block').html(response.htmlData);

                // remove bold class after clicking on mail
                $(element).removeClass('inbox-left-list-bold');
                // if(element.length > 0){
                //     $(element).removeClass('inbox-left-list-bold');
                // }
                
                if(response.scanStatus){
                    
                    // var options = {
                    //     width: "35rem",
                    //     height: "35rem",
                    //     color: "#cdd6df",
                    //     overflow: "hidden",
                    //     background: "transparent !important"
                    // };
                    var viewer = $('#pdf-viewer');
                    PDFObject.embed(response.pdfPath, viewer,{fallbackLink :true});
                    
                    // hide content tab
                    $('.content-nav-item').show();

                    // show preview tab
                    $('#home-tab').tab('show');

                }else{
                    // hide content tab
                    $('.content-nav-item').hide();

                    // show preview tab
                    $('#home-tab').tab('show');
                }

                // update mailroom unread mails counts
                $('.mail-room-count').hide();
                
                let indicator = $('.mail-room-count');
                let resIndie = response.indicator;
                $.each(indicator, function(i, v){
                    
                    let item = $(v).attr('data-type');
                    let itemSlug = $(v).attr('data-slug');
                    if(item != 'recipients'){
                        if (resIndie[item] && resIndie[item] > 0) {
                            if((item == 'mailroom') && resIndie[item] > 99){
                                $(v).html('99+');
                                $(v).show();
                            }else{
                                $(v).html(resIndie[item]);
                                $(v).show();
                            }
                        }else{
                            $(v).hide();
                        }
                    }else{
                        // console.log(resIndie.recipients);
                        if (resIndie.recipients[itemSlug] && resIndie.recipients[itemSlug] > 0) {
                            $(v).html(resIndie.recipients[itemSlug]);
                            $(v).show();
                        }else{
                            $(v).hide();
                        }
                    }
                });
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
        }
    });
}

/**function to update shipping address and open shipping popup */
function openShippingPopup(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getShippingAddressPath,
        dataType: "json",
        data: {
            
        },
        success: function (response) {
            
            let html = '';

            if(response.status){
                let address = response.data;
                $.each(address,function(i,v){
                    
                    html += '<div class="shipping-modal1-box-first" data-index="'+i+'" data-id="'+v.encryped+'"><div class="shipping-modal1-box-top">'+v.receiver_name+'<span>'+v.phone_number+'</span></div><a class="address-delete-cls" data-id="'+v.encryped+'" href="javascript:void(0);"><img src="'+globalImagePath+'/img/delete-address.svg"/></img></a><a class="edit-shipping-address-btn" data-id="'+v.encryped+'" href="javascript:void(0);"><img src="'+globalImagePath+'/img/edit.svg"/></img></a><div class="shipping-modal1-box-right-text">'+(v.address_type == 1?"BUSINESS":"DOMESTIC")+'</div><div class="shipping-modal1-box-btm-text"><span>'+v.street1+' '+(v.street2 !== null?v.street2:"")+'</span><span>'+v.city;

                    if(v.state != null){
                        html += ', '+v.state+' ';
                    }

                    html += ' '+v.zip_code+',</span><span>'+ v.country_name+'</span></div></div>';
                });
                
            }else{
                html = 'No address available';
            }
            $('.shipping-modal1-box-cover').html(html);
            $('#shipping-modal1').modal('show');
            

            //setTimeout(function(){
                //$('body').attr('class','modal-open');
           // },500);
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
        }
    });
}

/*function to remove share email address row*/
function removeEmailAddress(element){
    $(element).closest('.form-group').remove();
}

/* function to get code from redirected URL */
function closePopupWindow(){
    var currentURL = window.location.href;
    
    if (currentURL.indexOf('?code=') > 0) {
        var url = new URL(currentURL);
        var code = url.searchParams.get("code");
        window.opener.setAccessToken(code);
        window.close();
    }else if (currentURL.indexOf('error=') > 0) {
        window.close();
    }
}

/**to update tags according to smb number */
function updateTags(){

    let activeFilter = $('.sidebar-tag-cls.active-item');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getAllTags,
        dataType: "json",
        success: function (response) {
            if(response.status){
    
                $('.sidemenu-tag-block').empty();

                let html = '';
                $.each(response.tags, function(i, v){
                    let activeClass = "";

                    if(activeFilter.length > 0 && $(activeFilter).html() == v.tag){
                        activeClass = "active-item";
                    }

                    html += '<li class="list-unstyled pb-2"><a class="px-3 px-vxl-4 py-1 d-block sidebar-tag-cls '+activeClass+'" data-id="'+v.encryped+'" href="javascript:void(0);">'+v.tag+'</a></li>';
                });
                $('.sidemenu-tag-block').html(html);
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
        }
    });
}

function setAccessToken(code){
    
    var shareSelectedMailId = window['shareSelectedMailId'];
    var shareType = window['shareType'];
    var sendEmail=0;
    if(window['sendEmail']==1){
        sendEmail=1;
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getAccessToken,
        dataType: "json",
        data: {code:code,shareSelectedMailId:shareSelectedMailId,type:shareType,sendEmail:sendEmail},
        success: function (response) {
            
            if(response.status){
                //var current_path = response.filename.split('/').pop();
                var current_path = response.filename;
                //download_file(response.filename,current_path);
                var isZip = current_path.split('.').pop();
                if(isZip=='zip'){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: removeSharedEmailFile,
                        dataType: "json",
                        data: {filename:current_path},
                        success: function (response) {
                            
                        },
                    });
                }
                bootbox.alert({
                    title: "Alert!!",
                    message: "You have shared document(s) successfully.",
                    callback: function (result) {
                        $('.mail-select-all-checkbox').prop("checked",false);    
                        getInboxMails(1);       
                    }
                });
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: "Something went wrong, Please try again.",
                    callback: function (result) {}
                });
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
        }
    });

}

function openPopupWindow(authUrl,w,h){
    
    //redirect for authorization
    //boxAuthAddresses = "https://account.box.com/api/oauth2/authorize?response_type=code&client_id="+box_client_id+"&redirect_uri="+window.location.href;
    //window.location = boxAuthAddresses;
    //window.open(boxAuthAddresses);
    // Fixes dual-screen position                             Most browsers      Firefox
    const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    const systemZoom = width / window.screen.availWidth;
    const left = (width - w) / 2 / systemZoom + dualScreenLeft
    const top = (height - h) / 2 / systemZoom + dualScreenTop
    const newWindow = window.open(authUrl, 'Authentication', 
      `
      scrollbars=yes,
      width=${w / systemZoom}, 
      height=${h / systemZoom}, 
      top=${top}, 
      left=${left}
      `
    )

    if (window.focus) newWindow.focus();
}

/** to clear sort */
function clearSort(){
    
    $('.inbox-sort-box-title').text('Newest First');
    $('.inbox-sort-box-cls').removeClass('active');
    $.each($('.inbox-sort-box-cls'),function(i, v){
        if($(v).attr('data-id') == 0){
            $(v).addClass('active');
        }
    });
}

/** update move to */
function updateMoveTo(){
    let element = $('.sidemenu-room.active-item');
    
    if(element.length > 0){
        if($(element).attr('data-room') == 'inbox') {
            $('.move-to-room').html('<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="1">Archive</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="2">Trash</a>');
        }else if($(element).attr('data-room') == 'archive'){
            $('.move-to-room').html('<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="0">Inbox</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="2">Trash</a>');
        }else if($(element).attr('data-room') == 'all-mail'){
            $('.move-to-room').html('<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="0">Inbox</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="1">Archive</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="2">Trash</a>');
        }else if($(element).attr('data-room') == 'trash'){
            $('.move-to-room').html('<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="0">Inbox</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="1">Archive</a>');
        }
    }
    
    let element2 = $('.sidemenu-recipient-anchor.active-item');
    
    if(element2.length > 0){
        
        $('.move-to-room').html('<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="0">Inbox</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="1">Archive</a><a class="dropdown-item move-to-cls" href="javascript:void(0);" data-key="2">Trash</a>');
    }
}

/**to update recipients according to smb number */
function updateRecipients(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getAllRecipients,
        dataType: "json",
        success: function (response) {

            if(response.status){
                
                let sidebarHtml = '';
                let moveToRecipient = '<div class="dropdown-divider"></div>';
                $('.sidemenu-recipient-block').empty();
                $('.move-to-recipient').empty();

                $.each(response.recipients, function(i, v){
                    sidebarHtml += '<li class="list-unstyled pb-2 sidemenu-recipient-cls" data-name="'+v.name+'" data-id="'+v.encryped+'"><a class="px-3 px-vxl-4 py-1 d-block sidemenu-recipient-anchor" href="javascript:void(0);" data-id="'+v.encryped+'" data-name="'+v.name+'">'+v.name+'</a></li>';
                    
                    moveToRecipient += '<a class="dropdown-item move-to-cls" href="javascript:void(0);" data-id="'+v.encryped+'" data-key="3">'+v.name+'</a>';
                });

                $('.sidemenu-recipient-block').html(sidebarHtml);
                $('.move-to-recipient').html(moveToRecipient);
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
        }
    });
}

/*check already in pending request*/
function checkAlreadyPending(alreadyShred,alreadyShipped,alreadyScanned,alreadyDeposit,ScanInProcess){
    var message = '';
    
    if(alreadyShred){
        message = "You can not submit request for already shred mail";
    }else if(alreadyShipped){
            message = "You can not submit request for already shipped mail";
    }else if(alreadyScanned){
            message = "You can not submit request for already scanned mail";
    }else if(alreadyDeposit){
        message = "You can not submit request for already deposit mail";
    }else if(ScanInProcess){
        message = "You can not request for the mail which is already in-process";
    }
    if(message !=''){
        bootbox.alert({
            title: "Alert!!",
            message: message,
            callback: function (result) {}
        });
        return false;
    }else{
        return true;
    }
}

$(window).bind("load", function(){
// comes from check deposit then show all mail and selected mail details
if($('#check_deposit_mail_id').val() != '' && typeof $('#check_deposit_mail_id').val() !== 'undefined'){
    //$('.sidemenu-room-inbox').removeClass('active-item');
    $.each($('.sidemenu-room'),function(i,v){
        $(this).removeClass('active-item');
    });
    $('.all-mail').addClass('active-item');
    setTimeout(() => {
        getInboxMails();
    }, 200);
}
else{
    // Load default first page' mail
    if(currentPageName != 'select-account'){
        setTimeout(() => {
            getInboxMails();
        }, 200);
    }
}
});
function csrfTokenHandler(xhr){
    if(xhr.status == 406){
        if($('.bootbox').hasClass('show') == false){
            bootbox.alert({
                title: "Alert!!",
                message: "Your session has been expired, please login again to continue.",
                callback: function (result) {
                    location.replace(loginPagePath);
                }
            });
        }
    }
  }

function submitScanRequest(MForm){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: saveScanReuqestPath,
        dataType: "json",
        processData: false,
        contentType: false, 
        data: MForm,
        success: function (response) {
            
            if(response.status){
                getInboxMails(1);
                $('#content_scan_modal').modal('hide');
                $('.mail-select-all-checkbox').prop("checked",false);
            }else{
                $('#content_scan_modal').modal('hide');
                //$('.mail-select-all-checkbox').prop("checked",false);
                $('input:checkbox').prop("checked",false);
                bootbox.alert({
                    title: "Alert!!",
                    message: response.message,
                    callback: function (result) {
                    }
                });
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
            csrfTokenHandler(jqXHR);
            if(jqXHR.status){

                $('span.help-block').remove();
                $('.scan-request-form').trigger('reset');
                $('.post_scan_envelope').prop("disabled", false);
                $('#content_scan_modal').modal('hide');

                bootbox.alert({
                    title: "Alert!!",
                    message: jqXHR.responseJSON.message,
                    callback: function (result) {
                    }
                });
            }
        }
    });
}

function setFocus(first){
    $('.jcf-hover').removeClass('jcf-hover'); 
    $(first).data('jcfInstance').instance.fakeElement.find('.jcf-selected').addClass('jcf-hover');
    $(first).data('jcfInstance').instance.fakeElement.find('.jcf-selected')[0].scrollIntoView();
}