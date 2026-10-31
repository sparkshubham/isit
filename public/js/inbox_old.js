/** Ready start here*/
$(document).ready(function () {

    // Load default first page' mail
    getInboxMails();

    // hide content tab
    $('.content-nav-item').hide();

    // hide scan related data from shred confirmation
    $('.shared-confirm-model-text').hide();
    $('.shared-confirm-scan-btn').hide();

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
            data: {id:$(this).attr('data-id')},
            success: function (response) {
                if(response.status){
                    location.reload();
                }
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
            }
        });
    });

    // inbox mail paging
    $(document).on('click','.inbox-arrow-left',function(){
        getInboxMails($(this).attr('data-page'));
    });

    $(document).on('click','.inbox-arrow-right',function(){
        getInboxMails($(this).attr('data-page'));
    });

    // Inbox mail preview
    $(document).on('click','.mail-single-checkbox',function(event){
        // event.stopPropagation();
    });

    // Inbox mail preview
    $(document).on('click','.clickable-section-for-preview',function(event){
        // event.stopPropagation();
        getInboxMailPreview($(this).attr('data-id'));

        $.each($('.inbox-single-mail-block'),function(i,v){
            $(this).removeClass('active');
        });
        
        $(this).parents('.inbox-single-mail-block').addClass('active');

    });

    // check all checkbox
    $(document).on('click','.mail-select-all-checkbox',function(){
        if($(this).is(":checked")){
            $.each($('.mail-single-checkbox'),function(i,v){
                $(v).prop("checked",true);
            });
        }else{
            $.each($('.mail-single-checkbox'),function(i,v){
                $(v).prop("checked",false);
            });
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
            console.log(element);
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
        event.preventDefault();
        let id = [];
        let alreadyScanned = [];
        // get select mail ids
        $.each($('.mail-single-checkbox'),function(i,v){
            if($(this).is(":checked")){
                id.push($(this).attr('data-id'));
                if($(this).attr('data-scan') == 1){
                    alreadyScanned.push($(this).attr('data-id'));
                }
            }        
        });

        if(id.length > 0){
            if(alreadyScanned.length > 0){
                bootbox.alert({
                    title: "Alert!!",
                    message: "You can not submit scan request for already scanned document",
                    callback: function (result) {}
                });
            }else{
                $('#content_scan_modal').modal('show');   
            }
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select mail first",
                callback: function (result) {}
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
                    
                    getInboxMails(1);
                    $('#content_scan_modal').modal('hide');
                    $('.mail-select-all-checkbox').prop("checked",false);
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                }
            });
        }
    });

    /**This function will trigger shred confirmation model */
    $(document).on('click','.shred-option-cls',function(){
        event.preventDefault();
        let id = [];
        let alreadyShred = [];
        let alreadyShipped = false;
        let alreadyScanned = true;
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
            }        
        });

        if(id.length > 0){
            if(!alreadyShipped){
                if(alreadyShred.length > 0){
                    bootbox.alert({
                        title: "Alert!!",
                        message: "You can not submit shred request for already shred document",
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
                    message: "You can not submit shred request for already shipped document",
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
                getInboxMails(1);
                $('#confirm_modal').modal('hide');
                $('.mail-select-all-checkbox').prop("checked",false);
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
            }
        });
    });

    /**To show scan popup when user click on scan first in shred model */
    $(document).on('click','.shared-confirm-scan-btn',function(){
        window['fromShredPop'] = true;
        $('#confirm_modal').modal('hide');
        $('#content_scan_modal').modal('show');
    });

    /** Share Via Email popup show if a mail selected **/
    $(document).on('click','.share-mail-modal',function(){
        share_email_form.resetForm();
        //$('#share_via_email_form').trigger("reset");
        event.preventDefault();
        let selectedMailId = [];
        // get select mail ids
        $.each($('.mail-single-checkbox'),function(i,v){
            if($(this).is(":checked")){
                selectedMailId.push($(this).attr('data-id'));
            }        
        });
        if(selectedMailId.length>0){
            $("#selectedMailIds").val(selectedMailId);
            $("#share_mail_modal").modal('show');
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select mail first",
                callback: function (result) {}
            });
        }
    });

    /*add new email address*/
    $(document).on('click','.add-new-eamil',function(){
        var newRow = '<div class="form-group form-group-share-mail"><div class="inbox-modal-share-email-cover"><input class="form-control" type="text" name="email_address[]" placeholder="Email address"></div><a href="#" class="user-modal-plusicon2 remove-email-address" onclick="removeEmailAddress(this);"><img src="'+minus_icon_img+'" width="20"></a></div>';
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
                        var current_path = response.filename.split('/').pop();
                        var isZip = current_path.split('.').pop();
                        download_file(response.filename,current_path);
                        
                        //remove zip temporary file
                        bootbox.alert({
                            title: "Alert!!",
                            message: "Shared mail document successfully.",
                            callback: function (result) {
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
                            },
                        });
                        
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

/** this function will be to get and display mail*/
function getInboxMails(page=1){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getInboxMailsPath,
        dataType: "html",
        async: false,
        data: {page:page},
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

                $('.index-mail-block').empty();
                $('.index-mail-block').html(response.htmlData);
                if(response.paging.total_record > 0){
                    $('.inbox-page-number').html(response.paging.from+' - '+response.paging.to+' of '+response.paging.total_record);
                }else{
                    $('.inbox-page-number').html(response.paging.total_record+' - '+response.paging.total_record+' of '+response.paging.total_record);
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
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
        }
    });
}

/**This function will show mail's preview */
function getInboxMailPreview(id){
        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: getInboxMailPreviewPath,
        dataType: "json",
        async: false,
        data: {id:id},
        success: function (response) {
            if(response.status){
                $('.inbox-mail-preview-block').empty();
                $('.inbox-mail-preview-block').html(response.htmlData);

                if(response.scanStatus){
                    
                    // var options = {
                    //     width: "35rem",
                    //     height: "35rem",
                    //     color: "#cdd6df",
                    //     overflow: "hidden",
                    //     background: "transparent !important"
                    // };
                    var viewer = $('#pdf-viewer');
                    PDFObject.embed(response.pdfPath, viewer);
                    
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
            }
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
        }
    });
}

/*function to remove share email address row*/
function removeEmailAddress(element){
    $(element).closest('.form-group').remove();
}