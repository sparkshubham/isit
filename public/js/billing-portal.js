$(document).ready(function () {
    window['selected_plan'] = '';
    window['plan_mode'] = '';

    /*open chang eplan popup and reset the variable*/
    $(document).on('click','.change-plan-open-btn',function(){
        var is_current_plan = $("#is_current_plan").val();
        if(is_current_plan==1){
            window['selected_plan'] = '';
            $('.subscription-plan-box').removeClass('subscription-plan-box-selected');
            $('.selected').addClass('selected-plan-disabled');
            $('.plan-option-radio:checked').not('.current-selected-radio').prop('checked',false);
            $('#changeplanmodal').modal('show');
        }else{
            bootbox.alert({
                title: "Action Required!",
                message: "You must have a credit card on file before you can purchase a plan. Close this window and click the \"Add Credit Card\" button.",
                callback: function (result) {
                    
                },
            });
        }
    });

    $(document).on('click','.subscription-plan-box',function(){
        if($(this).hasClass('subscription-plan-box-selected')){
            $(this).removeClass('subscription-plan-box-selected');
            window['selected_plan'] = '';
        }else{
            $('.subscription-plan-box').removeClass('subscription-plan-box-selected');
            $(this).addClass('subscription-plan-box-selected');
            window['selected_plan'] = $(this).attr('data-id');
        }
    });

    /**Old code by shamsheer for old function */
    // $(document).on('click','.change-subscription-plan-btn',function(){
    //     if(window['selected_plan'] != ''){
    //         $("#confirmPlanChangeModal").modal("show");
            
    //     }else{
    //         bootbox.alert({
    //             title: "Alert!!",
    //             message: "Please select plan first",
    //             callback: function (result) {
    //                 setTimeout(function(){ $('body').attr('class','modal-open'); }, 400);
    //             },
    //         });
    //     }
    // });
    /**---------------------------------------- */
    /**New code by pratyush for new functionality {monthly & annual mode} */
    $(document).on('click','.change-subscription-plan-btn',function(){

        // update plan id according to new mode approach
        if($('.plan-option-radio:checked').not('.current-selected-radio').length > 0){
            window['selected_plan'] = $('.plan-option-radio:checked').not('.current-selected-radio').parents('.subscription-plan-box').attr('data-id');
            window['plan_mode'] = $('.plan-option-radio:checked').not('.current-selected-radio').val();
        }
        
        if(window['selected_plan'] != '' && $('.plan-option-radio:checked').not('.current-selected-radio').length > 0){
            $("#confirmPlanChangeModal").modal("show");
            
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select plan first",
                callback: function (result) {
                    setTimeout(function(){ $('body').attr('class','modal-open'); }, 400);
                },
            });
        }
    });

    /*plan change confirmation*/
    $(document).on('click','.change-plan-confirmation-btn',function(){
        $("#confirmPlanChangeModal").modal("hide");
        
        if(window['selected_plan'] != ''){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: changePlan,
                dataType: "json",
                data: {id:window['selected_plan'],plan_mode:window['plan_mode']},
                success: function (response) {
                    if(response.status){
                        $('#changeplanmodal').modal('hide');
                        window['selected_plan'] = '';
                        bootbox.alert({
                            title: "Alert!!",
                            message: response.message,
                            callback: function (result) {
                                location.reload();
                            },
                        });
                    }else{
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
                }
            });
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select plan first",
                callback: function (result) {
                    
                },
            });
        }
    });

    /*validate card*/
    var form_card_info = $('.form-card-info').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            // card: {
            //     required: true,
            //     normalizer: function( value ) {
            //         return $.trim( value );
            //     },
            //     // creditcard: true,
            //     number: true,
            //     minlength: 14,
            //     maxlength: 16
            // },
            // month: {
            //     required: true,
            //     normalizer: function( value ) {
            //         return $.trim( value );
            //     },
            //     number: true,
            //     minlength: 2,
            //     maxlength: 2,
            //     max:12
            // },
            // year: {
            //     required: true,
            //     normalizer: function( value ) {
            //         return $.trim( value );
            //     },
            //     number: true,
            //     minlength: 2,
            //     maxlength: 2
            // },
            // cvv: {
            //     required: true,
            //     normalizer: function( value ) {
            //         return $.trim( value );
            //     },
            //     number: true,
            //     minlength: 3,
            //     maxlength: 4
            // },
            name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                maxlength: 100
            }
        },
        //errorPlacement: validateErrorPositioning,
        errorPlacement: function(error,element){
          //console.log($(element).prop('type'));
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
            // card: {
            //     maxlength: 'Please enter valid 14-16 digit card number.',
            //     minlength: 'Please enter valid 14-16 digit card number.',
            //     number: 'Please enter valid 14-16 digit card number.'
            // },
            // cvv: {
            //     maxlength: 'Please enter valid 3-4 digit CVV code.',
            //     minlength: 'Please enter valid 3-4 digit CVV code.',
            //     number: 'Please enter valid 3-4 digit CVV code.'
            // },
            // month: {
            //     maxlength: 'Please enter valid month.',
            //     minlength: 'Please enter valid month.',
            //     number: 'Please enter valid month.'
            // },
            // year: {
            //     maxlength: 'Please enter valid year.',
            //     minlength: 'Please enter valid year.',
            //     number: 'Please enter valid year.'
            // },
            name: {
                maxlength: 'Entered characters should not be more than 100.',
                required: 'Name is not valid'
            }
        }
    });

    /*open credit card popup*/
    $(document).on('click','.open-card-popup',function(){
        form_card_info.resetForm();
        $('#changecreditcardmodal').modal('show');
    });

    /*close account confirmation*/
    $(document).on('click','.close-account-btn',function(){
        var allChecked = true;
        $.each($('.close-account-checkbox'),function(i,v){
            if(!$(this).is(":checked")){
                allChecked = false;
            }        
        });
        
        if(allChecked==true){
            $("#confirmCloseAccountModal").modal('show');
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select all instructions.",
                callback: function (result) {
                    
                },
            });
        }
    });

    /*close account*/
    $(document).on('click','.close-account-continue-btn',function(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: closeAccount,
            dataType: "json",
            data: {},
            success: function (response) {
                if(response.status){
                    $("#confirmCloseAccountModal").modal('hide');
                    bootbox.alert({
                        title: "Alert!!",
                        message: response.message,
                        callback: function (result) {
                            location.reload();
                        },
                    });
                }else{
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
            }
        });
    });

    /*cancel plan button confirmation*/
    $(document).on('click','.cancel-plan-btn',function(){
        $("#confirmPlanCancelModal").modal("show");
    });
    $(document).on('click','.close-plan-confirmation-btn',function(){
        $("#confirmPlanCancelModal").modal("hide");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: closePlan,
            dataType: "json",
            data: {},
            success: function (response) {
                if(response.status){
                    bootbox.alert({
                        title: "Alert!!",
                        message: response.message,
                        callback: function (result) {
                            location.reload();
                        },
                    });
                }else{
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
            }
        });
    });

    /*download billing History*/
    $(document).on('click','.download-statement-btn',function(){
        //console.log($(".statement-content").html());
        var startDate = $(".billing-active").attr('data-statement-start');
        var endDate = $(".billing-active").attr('data-statement-end');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: downloadStatement,
            dataType: "json",
            data: {content:$(".statement-content").html(),startDate:startDate,endDate:endDate},
            success: function (response) {
                if(response.status){
                   var current_path = response.filename.split('/').pop();
                        download_file(response.filename,'statement.pdf');
                        //remove file after download
                        setTimeout(function() {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: removeSharedEmailFile,
                                dataType: "json",
                                data: {filename:response.filename},
                                success: function (response) {
                                    //getInboxMails(1);           
                                },
                            });
                        }, 5000);
                }else{
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
            }
        });
    });

    /*get billing History*/
    $(document).on('click','.billing-statement-month',function(){
        $(".billing-statement-month").removeClass('billing-active');
        $(this).addClass('billing-active');
        var startDate = $(this).attr('data-statement-start');
        var endDate = $(this).attr('data-statement-end');

        var month = $(this).attr('data-month');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getStatement,
            dataType: "json",
            data: {month:month,startDate:startDate,endDate:endDate},
            success: function (response) {
                if(response.status){
                   $("#total").html(response.chargeArray['total'])
                   $("#plan_fee").html(response.chargeArray['plan_fee'])
                   $("#address_fee").html(response.chargeArray['address_fee'])
                   $("#scan_fee").html(response.chargeArray['scan_fee'])
                   $("#check_deposit_fee").html(response.chargeArray['check_deposit_fee'])
                   $("#shipping_fee").html(response.chargeArray['shipping_fee'])

                   $("#extra_mail_fee").html(response.chargeArray['extra_mail_fee'])
                   $("#mail_storage_fee").html(response.chargeArray['mail_storage_fee'])
                   $("#recipients_fee").html(response.chargeArray['recipients_fee'])
                   $("#green_subscription").html(response.chargeArray['green_subscription'])
                }else{
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
            }
        });
    });

    $(document).on('click','.plan-radio-common-cls',function(){
        $(this).parents('.subscription-plan-box').not('.selected-plan-disabled').addClass('selected');
        $(this).parents('.subscription-plan-box').not('.selected-plan-disabled').addClass('subscription-plan-box-selected');

        $('.plan-radio-common-cls').not('.current-selected-mode').prev('.plan-option-radio').prop('checked',false);

    });

    // green subscription
    $(document).on('click','.green-option-cls',function(){

        let plan_status = $(this).attr('data-plan');
        let plan_cancel = $(this).attr('data-cancel');
        let status = $(this).attr('data-status');
        let type = '';
        let alertMessage = '';
        if(status == 1){
            type = 0;
            alertMessage = `Are you sure, Green subscription will be cancel on your next billing cycle.`;
        }else{
            type = 1;
            alertMessage = `Are you sure, Green subscription will be added to your plan and you will be charged $${greenOptionPrice} monthly.`;
        }

        if(plan_status == 1){

            if(status == 1 && plan_cancel == 1){
                bootbox.alert({
                    title: "Alert!!",
                    message: "Your Green Subscription has been cancelled, it will take effect from your next billing cycle.",
                    callback: function (result) {
                        
                    },
                });
            }else{
                bootbox.confirm({
                    title: `Confirmation`,
                    message: alertMessage,
                    buttons: {
                        cancel: {
                            label: 'Close',
                        },
                        confirm: {
                            label: 'Confirm',
                            className: ' bootbox-alert-ok-btn'
                        }
                    },
                    callback: function (result) {
                        if(result){
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: manageGreenSubscriptionPath,
                                dataType: "json",
                                data: {status,type},
                                success: function (response) {
                                location.reload();
                                },
                                beforeSend: function () {           
                                },
                                error: function(jqXHR, textStatus, errorThrown){
                                }
                            });
                        }
                    }
                });
            }

        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "There is no active plan available, please purchase a plan to complete this request.",
                callback: function (result) {
                    
                },
            });
        }
    });
});