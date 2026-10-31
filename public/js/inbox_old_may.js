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

    /**Ship popup */
    $(document).on('click','.ship-option-cls',function(){
        let id = [];
        let alreadyShipped = false;
        let alreadyShred = false;
        
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
            }        
        });

        if(id.length > 0){
            if(id.length > 1){
                bootbox.alert({
                    title: "Alert!!",
                    message: "Please select single mail to ship",
                    callback: function (result) {}
                });
            }else{
                if(!alreadyShipped){ 
                    if(!alreadyShred){
                        // to open shipping popup
                        openShippingPopup();
                    }else{
                        bootbox.alert({
                            title: "Alert!!",
                            message: "You can not submit shipping request for already shreded document",
                            callback: function (result) {}
                        });
                    }
                }else{
                    bootbox.alert({
                        title: "Alert!!",
                        message: "You can not submit shipping request for already shipped document",
                        callback: function (result) {}
                    });
                }
            }
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select mail first",
                callback: function (result) {}
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
            address_line1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
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
            state: {
                required: true
            },
            postal_code: {
                required: true,
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
            spacial_request: {
                minlength: 'Please use 140 characters or less.',
            }
        }
    });

    $(document).on('click','.add-shipping-address-btn',function(){
        $('#shipping_address_modal').modal('show');
        $('#shipping-modal1').modal('hide');
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
                }
            });
       }
    });

    $(document).on('click','.shipping-address-cancel-btn',function(event){
        event.preventDefault();
        // Hide address model
        $('#shipping_address_modal').modal('hide');
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

        // get selected mail
        let mailId = '';
        $.each($('.mail-single-checkbox'),function(i,v){
            if($(this).is(":checked")){
                mailId = $(this).attr('data-id');
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
                        html += '<div class="shipping-modal3-box-first"><div class="shipping-modal3-box-left">'+v.carrier+' ' +(v.est_delivery_days!==null?v.est_delivery_days+" Days":"") +' '+v.service+'</div><div class="shipping-modal3-box-right">$'+v.rate+' <span><img src="img/right-arrow-blue.svg"></span></div></div>';
                    });
                }
                $('.shipping-modal2-fetching').hide();
                $('.shipping-rates-block').html(html);
                $('#shipping-modal1').modal('hide');
                $('#shipping-modal2').modal('show');
            },
            beforeSend: function () {           
            },
            error: function(jqXHR, textStatus, errorThrown){
            }
        });
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
            let carriers = selectedRate.find('.shipping-modal3-box-left').text();
            let carriersRates = selectedRate.find('.shipping-modal3-box-right').text();

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
            let mailId = '';
            $.each($('.mail-single-checkbox'),function(i,v){
                if($(this).is(":checked")){
                    mailId = $(this).attr('data-id');
                }        
            });

            // Add address ID
            MForm.append('id', id);
            MForm.append('mail_id', mailId);
            MForm.append('shipping_date', $('#datetimepicker12').data('date'));
            MForm.append('special_instructions', $('.shipping-special-instructions').val());
            MForm.append('carriers', carriers);
            MForm.append('carriers_rates', carriersRates);
            
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
                    $(".shipping-step-one-form").trigger('reset');
                    $('.shipping-rates-block').html('');
                    $('.shipping-special-instructions').val('');
                    $('#shipping-modal2').modal('hide');
                    getInboxMails(1);
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
                }
            });
        }else{
            console.log('Please select rates first');

        }
    });

    /** Create tag popup */
    $(document).on('click','.create-apply-tag',function(){
        
        // creating dynamic value
        window['parentMailId'] = '';
        window['parentMailId'] = $(this).attr('data-id');

        // reset form
        $('.add-tag-form').trigger('reset');

        // open modal
        $('#add_tag_modal').modal('show');
    });

    /** Add tag form validation */
    $('.add-tag-form').validate({
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
        if($('.add-tag-form').valid()){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveTagsPath,
                dataType: "json",
                data: $('.add-tag-form').serialize(),
                success: function (response){
                    console.log(response);
                },
                beforeSend: function () {           
                },
                error: function(jqXHR, textStatus, errorThrown){
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
                    
                    html += '<div class="shipping-modal1-box-first '+(i==0?"active":"")+'" data-index="'+i+'" data-id="'+v.encryped+'"><div class="shipping-modal1-box-top">Return Item to SnapMailBox<span>'+response.phone_number+'</span></div><div class="shipping-modal1-box-right-text">'+(v.address_type == 1?"BUSINESS":"DOMESTIC")+'</div><div class="shipping-modal1-box-btm-text"><span>'+v.street1+' '+(v.street2 !== null?v.street2:"")+'</span><span>'+v.city+', '+v.state+' '+response.receiver_phone_number+'</span></div></div>';
                });
                
            }else{
                html = 'No address available';
            }
            $('.shipping-modal1-box-cover').html(html);
            $('#shipping-modal1').modal('show');
        },
        beforeSend: function () {           
        },
        error: function(jqXHR, textStatus, errorThrown){
        }
    });
}