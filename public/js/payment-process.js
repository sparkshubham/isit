// Payment Page js
$(document).ready(function () {

    // Plan Page JS
    $(document).on('click','.select-plan-btn',function(){
        if($(this).attr('data-type')){
            var home = 0;
            if($(this).attr('data-return') == 'home'){
                home = 1;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                url: setSessionVariablePath,
                data: {
                    'page':'plan_page',
                    'type':$(this).attr('data-type'),
                    'document_id':null,
                    'is_home': home
                },
                success: function(response){
                    if(response.login == 1){
                        window.location.replace(checkOutPagePath);
                    }else{
                        // $('.sign-in-popup-model').trigger('click');
                        $('.sign-up-popup-model').trigger('click');
                    }
                }
            });
        }
    });

    // Plan Page JS
    $(document).on('click','.home-try-it-free',function(){
        if($(this).attr('data-type')){
            var home = 0;
            if($(this).attr('data-return') == 'home'){
                home = 1;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                url: setSessionVariablePath,
                data: {
                    'page':'plan_page',
                    'type':$(this).attr('data-type'),
                    'document_id':null,
                    'is_home': home
                },
                success: function(response){
                    if(response.login == 1){
                        window.location.replace(checkOutPagePath);
                    }else{
                        $('.sign-up-popup-model').trigger('click');
                    }
                }
            });
        }
    });
    //signup flows set session variable
    $(document).on('click','.sp2-custom-radio',function(){
        if($(this).attr('data-type')){
            var type = $(this).attr('data-type');
            var payment_type=0;
            if(type==3)
                payment_type=1;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                url: setSessionVariablePath,
                data: {
                    'page':$(this).attr('data-page'),
                    'type':$(this).attr('data-type'),
                    'document_id':$(this).attr('data-document_id'),
                },
                success: function(response){
                    $('#payment_type').val(payment_type);
                    $('#plan_type').val(type);
                    $('.checkout-submit-btn').removeClass('disabled');
                    if(payment_type==1){
                        if($("#document").length){

                        }else{
                            $('<input>').attr({
                                type: 'hidden',
                                id: 'document',
                                name: 'document',
                                value: $("#document_slug").val()
                            }).appendTo('form');
                        }
                        //$("#document").val($("#document_slug").val());
                    }else{
                        $("input[id='document']").remove();
                        //$("#document").val('');
                    }
                    console.log(response);
                    /*if(response.login == 1){
                        window.location.replace(checkOutPagePath);
                    }else{
                        $('.sign-in-popup-model').trigger('click');
                    }*/
                }
            });
        }
    });

    $(document).on('change','.checkout-document-list',function(){
        
        var selected = $(this).find('option:selected');
        
        if(selected.val()){
            var price = selected.data('price'); 
            var name = selected.data('name'); 
            $('.checkout-document-name').html(name);
            $('.checkout-document-price').html('$'+price);
        }else{
            $('.checkout-document-name').html('Single Document');
            $('.checkout-document-price').html('0.00');
        }
    });

    /*Account Payment Method Form*/
    jQuery.validator.addMethod("lettersonly", function(value, element) {
      return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
    }, "Letters only please"); 
    $('.checkout-form').validate({
        debug: false,
        ignore : [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            document: {
                required: true,
                remote :
                {
                    url: 'validationDocument',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    data:
                    {
                        document: function()
                        {
                            return $('#document').find('option:selected').val();
                        }
                    }
                }
            },
            card_number: {
                required: function (element) {
                    if($("#payment_mode").val() == 1){
                      return true;
                    } else {
                        return false;
                    }
                },
                number: true,
                minlength: 14,
                maxlength: 16
            },
            card_name: {
                required: function (element) {
                    if($("#payment_mode").val() == 1){
                      return true;
                    } else {
                        return false;
                    }
                },
                lettersonly: true,
            },
            security_code: {
                required: function (element) {
                    if($("#payment_mode").val() == 1){
                      return true;
                    } else {
                        return false;
                    }
                },
                number: true,
                minlength: 3,
                maxlength: 4
            },
            card_month: {
                required: function (element) {
                    if($("#payment_mode").val() == 1){
                      return true;
                    } else {
                        return false;
                    }
                },
            },
            card_year: {
                required: function (element) {
                    if($("#payment_mode").val() == 1){
                      return true;
                    } else {
                        return false;
                    }
                },
            }
        },
        messages: {
            card_number: {
                maxlength: 'Please enter valid 14-16 digit card number.',
                minlength: 'Please enter valid 14-16 digit card number.',
                number: 'Please enter valid 14-16 digit card number.'
            },
            security_code: {
                minlength: 'Please enter valid 3-4 digit security code.',
                minlength: 'Please enter valid 3-4 digit security code.',
                number: 'Please enter valid 3-4 digit security code.'
            },
            document: {
                remote: 'You have already purchased this document, you can not purchase this document again.'
            }
        },
        errorPlacement: function(error,element){
            if($(element).prop('nodeName') == 'SELECT'){
              error.insertAfter($(element).parent('div'));
            }
            else{
              error.insertAfter($(element));
            }
          },
        highlight: function (element, errorClass, validClass)
              {
                  $(element).parents('.col-field-wrapper').addClass('has-error');
              },
              unhighlight: function (element, errorClass, validClass) {
                  $(element).parents('.col-field-wrapper').removeClass('has-error');
              },
              invalidHandler: function(form, validator)
              {
                  console.log(form);
                  if (!validator.numberOfInvalids())
                      return;
  
                  $('html, body').animate({
                      scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                  }, 500);
              }
      });

      $(document).on('click','.checkout-submit-btn',function(){
        if($('.checkout-form').valid()){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: saveTransaction,
                dataType: "json",
                data: $('.checkout-form').serialize(),
                success: function (response) {
                    console.log(response);
                    if(response.status == true){
                        
                        // Redirect User to my account page
                        //window.location.replace(myAccountPath);
                        window.location.replace(response.url);
                    }else{
                        $('.account-payment-method-error').show();
                        $('.account-payment-method-error').html('<div class="alert alert-danger alert-dismissible mt-4"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+response.message+'</div>')
                    }
                },
                beforeSend: function () {           
                    //disable the submit button
                    
                },
                error: function(jqXHR, textStatus, errorThrown)
                    {
                        var responseText = JSON.parse(jqXHR.responseText);
                        
                        $("span.help-block").remove();
                        $('.help-block').removeClass('help-block');
                        
                        if(responseText.errors)
                        {
                            $.each(responseText.errors,function(i,value){
                                
                                var error = '<span class="help-block">'+value+'</span>';

                                if(i == 'document'){
                                    $('[name="'+i+'"]').parent('div').after(error);
                                }
                                else{
                                    $('[name="'+i+'"]').after(error);
                                }
                                $('[name="'+i+'"]').addClass('help-block');
                            });
                        }
                    }
            });
        }
      });

    /* Checkout Page Edit Button */
    $(document).on('click','.checkout-method-edit',function(){
        $('.add-card-detail-block').show();
        $('.card-detail-block').hide();
        $('.checkout-form').trigger('reset');
        $("span.help-block").remove();
        $('.help-block').removeClass('help-block');
        $("#payment_mode").val(1);
        $('.back-btn-link').show();
        $('.checkout-method-close').removeClass('disabled');
        $('.checkout-method-close').show();
    });

    /* Checkout Page Cancel Button */
    $(document).on('click','.checkout-method-close',function(){
        $('.add-card-detail-block').hide();
        $('.card-detail-block').show();
        $('.checkout-form').trigger('reset');
        $("span.help-block").remove();
        $('.help-block').removeClass('help-block');
        $("#payment_mode").val(0);
        $('.back-btn-link').hide();
        $('.checkout-method-close').hide();
    });

    $(function() {
        $('select').on('change', function() {
            $(this).trigger('focusout');
        })
        .on('focusout',function(e) {
            e.preventDefault();
        });
    });


    $(document).on('click','.invoice-download-btn',function(){
        id = $(this).attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: downloadInvoice,
            dataType: "json",
            data: {id:id},
            success: function (response) {
                console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown);
            }
        });
    });

    $(document).on('click','.signup-bottom-btn',function(){
        if($('#plan_type').val() == 0){
            showAlert(0,"Please select a plan type.")
        }
    });

});