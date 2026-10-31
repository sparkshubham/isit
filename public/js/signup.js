$(document).ready(function () {

    $(document).on('click','.plan-check-monthly-cls',function(){
        $('.monthly-radio-cls').prop('checked',true);
        $('.yearly-radio-cls').prop('checked',false);
    });
    
    $(document).on('click','.plan-check-yearly-cls',function(){
        $('.yearly-radio-cls').prop('checked',true);
        $('.monthly-radio-cls').prop('checked',false);
    });

    // hide plan active image
    $('.plan-active').hide();

    let currentIndex = 0;
    let previousIndex = 0;
    let navigation = [];

    $('.nav-tabs a').each((i, v) => {
        navigation.push($(v).attr('data-href'))
    })

    // handle bootstarp tabbing show callback
    $('.nav-tabs a').on('click', function (e) {

        e.preventDefault();
        currentIndex = navigation.indexOf($(this).attr("data-href"));
        previousIndex = navigation.indexOf(
            $(".nav-tabs a.active").attr("data-href")
        );
        let that = this;
        let planId = window["plan_id"];
        let address = window["address"];

        let validate = true;
        let targetElement = $(that).attr("data-href");

        if (currentIndex == 0 || currentIndex <= previousIndex) {
            validate = true;

            $(targetElement).tab("show");
            $(targetElement).show();
            $(that).addClass("active");

            $(".nav-tabs a").each(function (i, v) {
                let dataHref = $(v).attr("data-href");
                if (dataHref != targetElement) {
                    $(dataHref).hide();
                    $(v).removeClass("active");
                }
            });
        } else {
            if (previousIndex == 0 && $(".form-basic-info").valid() && currentIndex != 3) {
                window["tab_click"] = true;
                validate = true;

                $(".btn-basic-info").trigger("click");
                $(targetElement).tab("show");
                $(targetElement).show();
                $(that).addClass("active");

                $(".nav-tabs a").each(function (i, v) {
                    let dataHref = $(v).attr("data-href");
                    if (dataHref != targetElement) {
                        $(dataHref).hide();
                        $(v).removeClass("active");
                    }
                });
            } else if (currentIndex == 1 && $(".form-basic-info").valid()) {
                if (!$(".form-basic-info").valid()) {
                  validate = false;
                  bootbox.alert({
                    title: "Alert!!",
                    message: "Please select plan first",
                    callback: function (result) {},
                  });
                }else{
                    $(targetElement).tab("show");
                    $(targetElement).show();
                    $(that).addClass("active");
    
                    $(".nav-tabs a").each(function (i, v) {
                        let dataHref = $(v).attr("data-href");
                        if (dataHref != targetElement) {
                            $(dataHref).hide();
                            $(v).removeClass("active");
                        }
                    });
                }
            } else if (currentIndex == 2) {
                if(!$(".form-basic-info").valid()){
                    $(".form-basic-info").valid()
                }else if (typeof planId == "undefined") {                    
                    validate = false;

                    bootbox.alert({
                      title: "Alert!!",
                      message: "Please select plan first",
                      callback: function (result) {
                        $("#profile-tab").trigger('click');
                      },
                    });
                }else if (!$(".form-basic-info").valid()) {
                                     
                    validate = false;
                    bootbox.alert({
                      title: "Alert!!",
                      message: "Please select plan first",
                      callback: function (result) {
                        $("#profile-tab").trigger('click');
                      },
                    });
                }else{
                    validate = true;
                    $(targetElement).tab("show");
                    $(targetElement).show();
                    $(that).addClass("active");

                    $(".nav-tabs a").each(function (i, v) {
                        let dataHref = $(v).attr("data-href");
                        if (dataHref != targetElement) {
                            $(dataHref).hide();
                            $(v).removeClass("active");
                        }
                    });
                }
            } else if (currentIndex == 3) {
                if (!$(".form-basic-info").valid()) {
                    $(".btn-basic-info").trigger("click");
                } else if (typeof planId == "undefined") {
                    validate = false;
                    
                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select plan first",
                        callback: function (result) {
                            $("#profile-tab").trigger('click');
                        },
                    });
                } else if (!address.length) {
                    validate = false;

                    bootbox.alert({
                        title: "Alert!!",
                        message: "Please select address first",
                        callback: function (result) {
                            $("#contact-tab").trigger('click');
                        },
                    });
                } else {
                    validate = true;
                    $(targetElement).tab("show");
                    $(targetElement).show();
                    $(that).addClass("active");

                    $(".nav-tabs a").each(function (i, v) {
                        let dataHref = $(v).attr("data-href");
                        if (dataHref != targetElement) {
                            $(dataHref).hide();
                            $(v).removeClass("active");
                        }
                    });
                }
            }
        }
        setSignupTabHeading();
    });


    jQuery.validator.addMethod("customEmailValidation", function (value, element, params) {
        var idname = $(element).attr('id');
        var emailText = $(element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailText);
    }, jQuery.validator.format("Please enter a valid email address."));

    /**Signup basic info form */
    $('.form-basic-info').validate({
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
                customEmailValidation: true,
                remote :
                {
                    url: validateEmailPath,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    data:
                    {
                        email: function()
                        {
                            return $('.form-basic-info :input[name="email"]').val();
                        }
                    },
                    async: false, // set async = false
                }
            },
            phone_number: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 10,
                minlength: 10,
            },
            password: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                checkPassword: true
                // minlength: 8,
                // maxlength: 32
            },
            password_confirmation: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 8,
                maxlength: 32,
                equalTo: '#password'
            }
        },
        // errorPlacement: validateErrorPositioning,
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
                remote : 'The email has already been taken.'
            },
            password_confirmation: {
                equalTo: 'Confirm password and password must be same.'
            }
        }
    });

    $(document).on('click', '.btn-basic-info', function () {
        if ($('.form-basic-info').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: signupBasicInfoPath,
                dataType: "json",
                data: $('.form-basic-info').serialize(),
                success: function (response) {
                    if (response.status) {
                        if (window['tab_click'] === undefined || window['tab_click'] == false) {
                            /* $('#profile-tab').tab('show');
                            $('#profile-tab').show();
                            $('#profile-tab').addClass('active'); */
                            /* let that = $('.nav-tabs a.active');
                            $($(that).attr('data-href')).tab('show');
                            $($(that).attr('data-href')).show();
                            $(that).addClass('active'); */
                            $("#profile-tab").trigger("click");
                            $('html, body').animate({
                                scrollTop: $('#myTabContent').offset().top - 200
                            }, 'fast');

                        } else {
                            if (window['plan_id'] !== undefined) {
                                if (window['address'].length == 0) {
                                    $('#contact').trigger('click');
                                    /* $('#contact').tab('show');
                                    $('#contact').show();
                                    $('#contact').addClass('active');

                                    let that = $('.nav-tabs a.active');
                                    $($(that).attr('data-href')).tab('show');
                                    $($(that).attr('data-href')).show();
                                    $(that).addClass('active');
                                    if (previousIndex !=2){
                                        bootbox.alert({
                                            title: "Alert!!",
                                            message: "Please select address first",
                                            callback: function (result) {}
                                        });
                                    } */

                                } else {
                                    let that = $('.nav-tabs a.active');
                                    let targetElement = $(that).attr('data-href');
                                    $(targetElement).trigger("click");
                                    /* $($(that).attr('data-href')).tab('show');
                                    $($(that).attr('data-href')).show();
                                    $(that).addClass('active'); */
                                }
                            } else {
                                $("#profile-tab").trigger("click");

                                // update nav value (Code for mobile responsive tabbing)
                                let rightArrow = $('.prgs-parent-cls .right-arrow');
                                $(rightArrow).attr('data-current','2')
                                $(rightArrow).attr('data-step','3')
                                $('.left-arrow').attr('data-step','1');
                                $('.left-arrow').show();
                                $('.prgs-title').html('Choose a Plan');
                                $('.prgs-steps-current').html('2');

                            }
                            window['tab_click'] = false;
                        }
                        setSignupTabHeading();
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

    /** signup plan page*/
    $(document).on('click', '.btn-plan-purchase', function () {
        // create dynamic variable
        window['plan_type'] = $(this).attr('data-type');
        window['plan_address'] = $(this).attr('data-address');
        window['plan_id'] = $(this).attr('data-id');
        window['plan_mode'] = $(this).parents('.price').find('.plan-option-radio:checked').val();
        
        // Get old selected address
        let oldAddress = $('.signup-selected-address');
        // clear selected address
        $.each(oldAddress,function(i,v){
            $('#card' + $(v).attr('data-number')).remove();
            $(this).removeClass('signup-selected-address');
        });
        // Reset dynamic variable
        window['address'] = [];


        // updating plan details
        $('.signup-final-plan-title').text($('.signup-plan-title' + $(this).attr('data-type')).text());
        var planMode = '/month';
        if($(this).parents('.price').find('.plan-option-radio:checked').val() == 1){
            planMode = '/year';
        }

        $('.signup-final-plan-price').text('$'+$(this).parents('.price').find('.plan-option-radio:checked').attr('data-price') + planMode);

        // open address panel
        $('#contact-tab').trigger('click');

        // Scroll top
        $('html, body').animate({
            scrollTop: $('#myTabContent').offset().top - 200
        }, 'fast');

        // show plan active div
        $('.plan-active').hide();
        $('#' + $(this).parents('.price').find('.plan-active').attr('id')).show();

        // update nav value (Code for mobile responsive tabbing)
        let rightArrow = $('.prgs-parent-cls .right-arrow');
        $(rightArrow).attr('data-current','3')
        $(rightArrow).attr('data-step','3')
        $('.left-arrow').attr('data-step','1');
        $('.left-arrow').show();
        $('.prgs-title').html('Select Address');
        $('.prgs-steps-current').html('3');
    });

    /**selected address */
    $(document).on('click', '.addresses-container .card:not( .address-page-cls .card )', function () {
        let addressCount = window['plan_address'];
        if (typeof addressCount !== 'undefined') {

            let address = '<div class="card h frm" id="card' + $(this).attr('data-number') + '"><div class="card-icon"><i class="icon-point" style="font-size: 40px;"></i></div><div class="card-body"><p class="card-text">' + $(this).find('.card-text').html() + '</p></div></div>';

            let addressArr = window['address'];
            let removeItem = $(this).attr('data-id');

            // select and de-select the address
            if ($(this).hasClass('signup-selected-address')) {
                $(this).removeClass('signup-selected-address');
                $('#card' + $(this).attr('data-number')).remove();

                // Remove address from array
                addressArr = jQuery.grep(addressArr, function (value) {
                    return value != removeItem;
                });
                window['address'] = addressArr;

            } else {
                $(this).addClass('signup-selected-address');
                $('.user-selected-address').append(address);

                // Add address to array
                addressArr.push($(this).attr('data-id'));
                window['address'] = addressArr;
            }

            if ($('.signup-selected-address').length > addressCount) {
                $(this).removeClass('signup-selected-address');
                $('#card' + $(this).attr('data-number')).remove();

                // Remove address from array
                addressArr = jQuery.grep(addressArr, function (value) {
                    return value != removeItem;
                });
                window['address'] = addressArr;

                bootbox.alert({
                    title: "Alert!!",
                    message: "You can not select more than " + addressCount + " address(es)",
                    callback: function (result) {}
                });
            }
        } else {
            $(this).removeClass('signup-selected-address');
            $('#card' + $(this).attr('data-number')).remove();
            bootbox.alert({
                title: "Alert!!",
                message: "Please select plan first",
                callback: function (result) {}
            });
        }

        // if (window['address'].length > 0) {
        //     $('.btn-continue').prop('disabled', false);
        // } else {
        //     $('.btn-continue').prop('disabled', true);
        // }
    });

    /**Signup billing info form */
    $('.form-billing-info').validate({
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
            //     maxlength: 2
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
            street: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                maxlength: 50
            },
            state: {
                maxlength: 50
            },
            city: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                maxlength: 50
            },
            // zip: {
            //     minlength: 5,
            //     maxlength: 8
            //     // number: true,
            // }
        },
        errorPlacement: validateErrorPositioning,
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
            card: {
                maxlength: 'Please enter valid 14-16 digit card number.',
                minlength: 'Please enter valid 14-16 digit card number.',
                number: 'Please enter valid 14-16 digit card number.'
            },
            cvv: {
                minlength: 'Please enter valid 3-4 digit CVV code.',
                minlength: 'Please enter valid 3-4 digit CVV code.',
                number: 'Please enter valid 3-4 digit CVV code.'
            },
            month: {
                minlength: 'Please enter valid month.',
                minlength: 'Please enter valid month.',
                number: 'Please enter valid month.'
            },
            year: {
                minlength: 'Please enter valid year.',
                minlength: 'Please enter valid year.',
                number: 'Please enter valid year.'
            },
            street: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            city: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            state: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            zip: {
                maxlength: 'Entered characters should not be less than 5.',
                maxlength: 'Entered characters should not be more than 8.',
                number: 'Please enter valid zip code.'
            }
        }
    });

    $(document).on('click', '.btn-billing-info', function (e) {

        onGetCardNonce();
    });

    // load more option for custom lazy load 
    $(document).on('click', '.btn-load-more', function () {
        loadAddressPage(parseInt($('.currentPage').val()) + 1);
        if ($('.currentPage').val() == $('.lastPage').val()) {
            $('.load-more').remove();
        }
    });

    // continue button on address tab
    $(document).on('click','.btn-continue',function(){
        if(window['address'].length > 0) {
            
            $('#Billing').tab('show');
            $('#Billing').show();
            $('#Billing').addClass('active');

            $(".nav-tabs a").each(function (i, v) {
                let dataHref = $(v).attr("data-href");
                if (dataHref != "#Billing") {
                    $(dataHref).hide();
                    $(v).removeClass("active");
                }else{
                    $(v).addClass("active");
                }
            });
            
            // update nav value (Code for mobile responsive tabbing)
            let rightArrow = $('.prgs-parent-cls .right-arrow');
            $(rightArrow).attr('data-current','4')
            $(rightArrow).attr('data-step','4')
            $('.left-arrow').attr('data-step','1');
            $('.right-arrow').hide();
            $('.prgs-title').html('Billing Info');
            $('.prgs-steps-current').html('4');
            setSignupTabHeading();

            $('html, body').animate({
                scrollTop: $('.headline').offset().top - 200
            }, 'fast');

        } else {

            bootbox.alert({
                title: "Alert!!",
                message: "Please select address first",
                callback: function (result) {
                    $('html, body').animate({
                        scrollTop: $('#myTabContent').offset().top - 200
                    }, 'fast');
                },
            });
            // $('.btn-continue').prop('disabled', true);
        }
    });

    // Handling mobile response tabbing 
    // right arrow
    $(document).on('click','.right-arrow',function(){
        if($(this).attr('data-current') == 1){
            if($(".form-basic-info").valid()){

                // update nav value
                $(this).attr('data-current','2')
                $(this).attr('data-step','3')
                $('.left-arrow').attr('data-step','1');
                $('.left-arrow').show();
                $('.prgs-title').html('Choose a Plan');
                $('.prgs-steps-current').html('2');

                // show next tab
                $("#profile-tab").trigger('click');
            }
        }else if($(this).attr('data-current') == 2){
            if(window['plan_id'] !== undefined){

                // update nav value
                $(this).attr('data-current','3')
                $(this).attr('data-step','4')
                $('.left-arrow').attr('data-step','3');
                $('.left-arrow').show();
                $('.prgs-title').html('Select Address');
                $('.prgs-steps-current').html('3');

                // show next tab
                $('#contact-tab').trigger("click");

            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: "Please select plan first",
                    callback: function (result) {
                        $('profile-tab').trigger('click');
                    },
                });
            }
        }else if($(this).attr('data-current') == 3){
            if(window['address'].length > 0){

                // update nav value
                $(this).attr('data-current','4')
                $(this).attr('data-step','4')
                $('.left-arrow').attr('data-step','3');
                $('.right-arrow').hide();
                $('.prgs-title').html('Billing Info');
                $('.prgs-steps-current').html('4');

                // show next tab
                
                $('#Billing-tab').trigger("click");
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: "Please select address first",
                    callback: function (result) {
                        $('#contact-tab').trigger("click");
                    },
                });
            }
        }
    })

    // left arrow
    $(document).on('click','.left-arrow',function(){
        $('.right-arrow').show();
        let rightArrow = $(this).parents('.prgs-parent-cls').find('.right-arrow');
        if($(rightArrow).attr('data-current') == 4){
            // update nav value
            $(rightArrow).attr('data-current','3')
            $(rightArrow).attr('data-step','3')
            $('.left-arrow').attr('data-step','3');
            $('.left-arrow').show();
            $('.prgs-title').html('Select Address');
            $('.prgs-steps-current').html('3');

            // show next tab
            $('#contact-tab').trigger("click");
        }else if($(rightArrow).attr('data-current') == 3){
            // update nav value
            $(rightArrow).attr('data-current','2')
            $(rightArrow).attr('data-step','2')
            $('.left-arrow').attr('data-step','2');
            $('.left-arrow').show();
            $('.prgs-title').html('Choose a Plan');
            $('.prgs-steps-current').html('2');

            // show next tab
            $("#profile-tab").trigger('click');   
        }else if($(rightArrow).attr('data-current') == 2){
            // update nav value
            $(rightArrow).attr('data-current','1')
            $(rightArrow).attr('data-step','1')
            $('.left-arrow').attr('data-step','1');
            $('.left-arrow').show();
            $('.prgs-title').html('Basic Info');
            $('.prgs-steps-current').html('1');

            // show next tab
            $("#home-tab").trigger('click');
            $('.left-arrow').hide();
        }
    });

    // $('.prevent-space').keypress(function( e ) {
    //     if(e.which === 32){
    //         return false;
    //     }
    // })​​​​​;​
    $(document).on('keypress','.prevent-space', function(e){
        if(e.which === 32){
                return false;
        }
    });
});

const paymentForm = new SqPaymentForm({
applicationId: qaApplicationId,
inputClass: ['sq-input'],
inputStyles: [
    {
        color:'#464849',
        padding: '9px 8px',
        fontSize: '15px',
        fontWeight: 400
    }
],
cardNumber: {
    elementId: 'sq-card-number',
    placeholder: 'Card Number'
},
cvv: {
    elementId: 'sq-cvv',
    placeholder: 'CVV'
},
expirationDate: {
    elementId: 'sq-expiration-date',
    placeholder: 'MM/YY'
},
postalCode: {
    elementId: 'sq-postal-code',
    placeholder: 'Postal'
},
// SqPaymentForm callback functions
callbacks: {
    cardNonceResponseReceived: function (errors, nonce, cardData) {
        
        if( typeof nonce === 'undefined' || nonce === null ){
            
            $('#sq-card-number-error,#sq-expiration-date-error,#sq-cvv-error,#sq-postal-code-error').show();
            $.each(errors, function(i,v){
                if(v.field == "cardNumber"){
                    $('#sq-card-number-error').text(v.message);
                }
                if(v.field == "expirationDate"){
                    $('#sq-expiration-date-error').text(v.message);
                }
                if(v.field == "cvv"){
                    $('#sq-cvv-error').text(v.message);
                }
                if(v.field == "postalCode"){
                    $('#sq-postal-code-error').text(v.message);
                }
            });
            return '';
        }else{
            $('#sq-card-number-error,#sq-expiration-date-error,#sq-cvv-error,#sq-postal-code-error').text('');
            $('#sq-card-number-error,#sq-expiration-date-error,#sq-cvv-error,#sq-postal-code-error').hide();
            
            if($('.form-billing-info').valid()){
            
                // form-billing-info
                var form = $(".form-billing-info");
                var MForm = new FormData(form[0]);

                // Add plan ID
                MForm.append('plan_id', window['plan_id']);

                // Add plan mode
                MForm.append('plan_mode', window['plan_mode']);

                // Address 
                MForm.append('address', window['address']);

                // card nonce
                MForm.append('nonce', nonce);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: signupBillingInfoPath,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: MForm,
                    success: function (response) {
                        if (response.status) {
                            location.replace(response.redirect);
                        } else {
                            if (response.code == '200') {
                                $('.card-error-bloack').html('<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.message + '</div>');
                                $('.card-error-bloack').show();

                                $('html, body').animate({
                                    scrollTop: $('.card-error-bloack').offset().top - 200
                                }, 'fast');

                                // Auto hide alert (flase) messages
                                setTimeout(function() {
                                    $('.card-error-bloack').delay(4000).slideUp("normal", function() { $('.alert-danger').remove(); } );
                                }, 5000);

                            } else {
                                location.reload();
                            }
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
        }
        // alert(`The generated nonce is:\n${nonce}`);
    }
}
});

function onGetCardNonce() {
    paymentForm.requestCardNonce();
    $('.form-billing-info').valid();
}