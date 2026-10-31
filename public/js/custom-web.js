//Custom Scrollbar
(function($){
    $(window).on("load",function(){
        $(".customScrollbar").mCustomScrollbar({
        scrollButtons:{enable:true},
        theme: 'dark-thick'

        });
        $(".legal-documents-sm-filters").mCustomScrollbar({
            // scrollButtons:{enable:true},
            axis:"x",
            theme: 'dark-thin'
        });
    });   
})(jQuery);

/*Menu # Scroll on Document Page, Controls and UI management*/
$(window).resize(function(){
    //headerSet();
    //headerFixAddRemove();
    //transformSet();
    //setBgHeight();
    //checkMedia1199();
});

/*@summary custom alert messages
* @param1 type (1-success/0-error)
* @param2 message (custom message)
* @return alert popup
*/

function showAlert(type,message){
    if(type == 1){
        $(".alert-box").addClass("alert-success-box");
    }else if(type == 0){
        $(".alert-box").addClass("alert-error-box");
    }

    $(".alert-box-text").html(message);
    $(".alert-box").css("display", "block");
}

function getPaginationData(url,className) {
    
    $.ajax({
        url : url,  
        type: "get",
        datatype: "html"
    }).done(function (data) {
        
        $('.'+className).html(data);  
    }).fail(function () {
        console.log('No response from server.');
    });
}

function readCookie(name) {
    var i, c, ca, nameEQ = name + "=";
    ca = document.cookie.split(';');
    for(i=0;i < ca.length;i++) {
        c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return '';
}

function validateErrorPositioning(error, element) {
    if(element.next('.help-block').length > 0){
        element.next('.help-block').text(error.text());
    }
    else{
        error.insertAfter(element);
    }
}

$('.smooth-goto').on('click', function() {  
    $('html, body').animate({scrollTop: $(this.hash).offset().top - 80}, 1000);
    return false;
});

$(".pass-show-hide-btn").click(function() {

var input = $($(this).attr("toggle"));
    // console.log(input);
    if (input.attr("type") == "password") {
        input.attr("type", "text");
        $(this).find(".eye-icon").addClass('active');
        $(this).find(".eye-slash-icon").removeClass('active');
    } else {
        input.attr("type", "password");
        $(this).find(".eye-icon").removeClass('active');
        $(this).find(".eye-slash-icon").addClass('active');
    }
});

/** Custom function to reset form and validation */
function clearValidation(formElement){
    //Internal $.validator is exposed through $(form).validate()
    var validator = $(formElement).validate();
    //Iterate through named elements inside of the form, and mark them as error free
    $('[name]',formElement).each(function(){
        validator.successList.push(this);//mark as error free
        $(this).removeClass('help-block');
        validator.showErrors();//remove error messages if present
    });
    validator.resetForm();//remove error class on name elements and clear history
    validator.reset();//remove all error and success data
}

/** For smooth transaction between modals */
$('.modal').on('hidden.bs.modal', function (e) {
    if($('.modal').hasClass('show')){
        $("body").addClass("modal-open");
    }
    else{
        $("body").removeClass("modal-open");   
    }    
});

/** Common function for handle server side errors */
function validationFailure(jqXHR, textStatus, errorThrown){
    var responseText = JSON.parse(jqXHR.responseText);
    $("span.help-block").remove();
    $('.help-block').removeClass('help-block');
    
    if(responseText.errors)
    {
        $.each(responseText.errors,function(i,value){
            var error = '<span class="help-block">'+value+'</span>';
            $('input[name="'+i+'"]').after(error);
            $('input[name="'+i+'"]').addClass('help-block');
        });
    }else if(responseText.exception=="Exception"){
        $('#loginModal').modal('hide');
        $('#registerModal').modal('hide');
        var page = window.location.href;
        var exist_create_page = page.includes("create-document"); 
        if(exist_create_page){
            location.replace(myAccountPath);
        }else{
            showAlert(0,responseText.message);
        }
    }
}

/** function to selected tabs heading on signup page */
function setSignupTabHeading(){
    //get all nav link
    let tabs = $('.signup-page').find('.nav-link');

    // remove heading which was used for display none
    $('.headline').removeClass('signup-head-block');

    // remove prev class
    $('.prev').removeClass('prev');
    let findActive = false;
    $.each(tabs,function(index,element){
        if($(element).hasClass('active')){
            $('.'+$(element).attr('data-heading-class')).show();
            findActive = true;
        }else{
            $('.'+$(element).attr('data-heading-class')).hide();
        }

        if(!findActive){
            $(element).addClass('prev');
        }
    });
}

// load signup page addresses using custom lazy load

function loadAddressPage(page){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: getSignupAddress,
        dataType: "html",
        async: false,
        data: {page:page},
        success: function (response) {
            $('.lastPage').remove();
            $('.currentPage').remove();
            $('.addresses-parent-div').append(response).hide().fadeIn();
        },
        beforeSend: function () {           
            //disable the submit button
            
        }
    });
}

/** Ready start here*/

$(document).ready(function () {
    if($(location).attr('hash') != '' && ($(location).attr('hash') == '#howitworks' || $(location).attr('hash') == '#features' || $(location).attr('hash') == '#addresses')){
        $([document.documentElement, document.body]).animate({
            scrollTop: $($(location).attr('hash')).offset().top - $('header').innerHeight()
        }, 200);
    }
    
    // for signup page tabbing heading
    setSignupTabHeading();

    // hide signup billing info section error block
    $('.card-error-bloack').hide();

    // loading address for signup page
    loadAddressPage(1);

    // Hide register button when user is logged in
    if(isLogin){
        $('.hide-on-login').hide();
    }

    // For UI

    //headerSet();
    //headerFixAddRemove();
    //transformSet();
    //setBgHeight();
    //checkMedia1199();

    //For Tooltip
    $('[data-toggle="tooltip"]').tooltip({
        html:true
    });

    //data mask phone number
    $('[data-mask]').inputmask(); 
    $("#phone").inputmask("999-999-9999",{ "clearIncomplete": true }); 
    // $("#card").inputmask("9999-9999-9999-9999",{ "clearIncomplete": false }); 
    //get faq of categories
    $(document).on('click','.faq-category',function(e){
        $(".faq-category").removeClass("active");
        $(this).addClass('active');
        // Getting form objects
        var category_id = $(this).attr('id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: getCategoryFAQ,
            dataType: "json",
            data: {category_id:category_id},
            success: function (response) {
                console.log(response.content);
                $('#accordion-faqs').html(response.content);
            },
            beforeSend: function () {           
                //disable the submit button
                
            }
        });
    });

    //$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
    $(document).ajaxStart(function(){
        $.blockUI({
            message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
        });
    });


    $(document).ajaxStop(function(){
        $.unblockUI()
    });

    /*if(SetPassword){
        showAlert(1,'Your password has been updated successfully, please login to continue.');
        $('#loginModal').modal('show');
    }*/

    //showAlert(1,'Form submitted sucessfully');
    $(document).on('click','.alert-close-btn',function(){
        $(".alert-box").removeClass("alert-success-box");
        $(".alert-box").removeClass("alert-error-box");
        $(".alert-message").html('');
        $(".alert-box").css("display", "none");
    });

    jQuery.validator.addMethod("emailValidation", function (value, element, params) {  
        var idname = $(element).attr('id');  
        var emailText = $(element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailText);
    }, jQuery.validator.format("Email address not valid."));

    $('#contact-us-form').validate({
        debug: false,
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            email: {
                required: true,
                //email: true,
            },
            phone: {
                required: true,
            },
            subject: {
                required: true,
            },
            message: {
                required: true,
            }
        },
    });

    
    
    $(document).off('click','.contact-us-btn');
    $(document).on('click','.contact-us-btn',function(){
        
        //$(document).off('click','.contact-us-btn');
        event.preventDefault();
        if($('#contact-us-form').valid()){
            $('.contact-us-btn').text('Please wait...');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: CONTACT_US_PATH,
                dataType: "json",
                data: $('#contact-us-form').serialize(),
                success: function (response) {
                    if(response.status == true){
                        showAlert(1,'We’re thrilled to hear from you. Our inbox can’t wait to get your messages, so talk to us any time you like.');
                        $('.contact-us-btn').html('Send Message');
                        $(".contact-us-btn").attr("disabled", false);
                        $("#contact-us-form").trigger("reset");
                    }
                    else{
                        $("span.help-block").remove();
                        $('.help-block').removeClass('help-block');
                        if(response.errors)
                        {
                            $.each(response.errors,function(i,value){
                                var error = '<span class="help-block" id="email-error">'+value+'</span>';
                                $("#"+i).after(error);
                                $("#"+i).addClass('help-block');
                            });
                        }
                        $('.contact-us-btn').html('Send Message');
                        $('.contact-us-btn').attr("disabled", false);
                    }
                },
                beforeSend: function () {           
                    //disable the submit button
                    
                },
                error: function(jqXHR, textStatus, errorThrown)
                    {
                        var responseText = JSON.parse(jqXHR.responseText);
                        /*if(responseText.errors)
                        {
                            for(index in responseText.errors)
                            {
                                console.log(index);
                                $('.error[for="' + index + '"]').text(responseText.errors[index][0]);
                                //$('.help-block').text(responseText.errors[index][0]);
                            }
                        }*/

                        $("span.help-block").remove();
                        
                        if(responseText.message){
                            //showAlert(0,responseText.message);
                        }

                        if(responseText.errors)
                        {
                            $.each(responseText.errors,function(i,value){
                                var error = '<span class="help-block" id="email-error">'+value+'</span>';
                                $("#"+i).after(error);
                                $("#"+i).addClass('help-block');
                            });
                        }
                        $('.contact-us-btn').html('Send Message');
                        $('.contact-us-btn').attr("disabled", false);
                    }
            });
        }
    });

    /** change heading on click signup page */
    // $(document).on('click','.nav-link',function(){
    //     setSignupTabHeading();
    // });

     // login form validation
    
    $('.login-form-validate').validate({
        debug: false,
        ignore : [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
                email: {
                    required: true,
                    customEmailValidation:true
                },
                password: {
                    required: true,
                }
            },
            errorPlacement: validateErrorPositioning,
            highlight: function (element, errorClass, validClass)
            {
                $(element).parents('.col-field-wrapper').addClass('has-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.col-field-wrapper').removeClass('has-error');
            },
            invalidHandler: function(form, validator)
            {
                if (!validator.numberOfInvalids())
                    return;
    
                $('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);
            },
            messages: {
                email: {
                    maxlength: 'Entered characters should not be more than 255.',
                },
                password: {
                    equalTo: 'Confirm password and password must be same.'
                }
            }
    });

    $('.forgot-password-form').validate({
        debug: false,
        ignore : [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
                email: {
                    required: true,
                    customEmailValidation:true
                }
            },
            errorPlacement: validateErrorPositioning,
            highlight: function (element, errorClass, validClass)
            {
                $(element).parents('.col-field-wrapper').addClass('has-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.col-field-wrapper').removeClass('has-error');
            },
            invalidHandler: function(form, validator)
            {
                if (!validator.numberOfInvalids())
                    return;
    
                $('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);
            },
            messages: {
                email: {
                    maxlength: 'Entered characters should not be more than 255.',
                }                
            }
    });

    $.validator.addMethod('checkPassword', function (value) { 
        return /^(?=.*?[0-9])(?=.*?[a-z])(?=.*[A-Z])(?=.*[~!@#$%^&*()_+=-]).{8,32}$/.test(value); 
    }, 'Your password must have 8 to 32 characters containing 1 upper case letter, 1 lower case letter, 1 special character & 1 digit.');

    $('.reset-password-form').validate({
        debug: false,
        ignore : [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            password: {
                    required: true,
                    checkPassword: true
                },
            password_confirmation: {
                    required: true,
                    equalTo : '#password'
                }
            },
            errorPlacement: validateErrorPositioning,
            highlight: function (element, errorClass, validClass)
            {
                $(element).parents('.col-field-wrapper').addClass('has-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.col-field-wrapper').removeClass('has-error');
            },
            invalidHandler: function(form, validator)
            {
                if (!validator.numberOfInvalids())
                    return;
    
                $('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);
            },
            messages: {
                password_confirmation: {
                    equalTo: 'Confirm password and password must be same.'
                }
            }
    });

    // prevent space by using class
    $('.prevent-space').keyup(function() {
        this.value = this.value.replace(/\s/g,'');
    });

    // forgot password btn
    $(document).on('click','.forgot-password-btn',function(){
        if($('.forgot-password-form').valid()){
            $('.forgot-password-btn').prop('disabled',true);
            $('.forgot-password-form').submit();
        }
    });

    // reset password btn
    $(document).on('click','.reset-password-btn',function(){
        if($('.reset-password-form').valid()){
            $('.reset-password-btn').prop('disabled',true);
            $('.reset-password-form').submit();
        }
    });

    // Send SMS functionality
    $(document).on('click','.text-me-btn',function(){
        let element = $(this).parents('.mobile-landing-inputcover').find('.sms-number-cls');
        
        let input = document.querySelector('#'+$(element).attr('id'));
        let iti = window.intlTelInputGlobals.getInstance(input);
        let number = iti.getNumber();
        if(iti.isValidNumber()){
            // commenting code for future use
            // let number = iti.getNumber(intlTelInputUtils.numberFormat.E164);

            // get error block
            let errorBlock = $(this).parents('.mobile-landing-inputcover').next('.sms-error-block');
            // clear error block
            $(errorBlock).html('')

            // send data to server
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: sendSmsPath,
                dataType: "json",
                data: {number},
                success: function (response) {
                    if(response.status){
                        // clear input
                        $('.sms-number-cls').val('');
                        // show success alert
                        showAlert(1,response.message);
                    }else{
                        // show error message
                        showAlert(0,response.message);
                    }
                },
                beforeSend: function () {           
                    //disable the submit button
                }
            });

        }else{
            if(number != ''){
                let errorBlock = $(this).parents('.mobile-landing-inputcover').next('.sms-error-block');
                $(errorBlock).html('<span class="help-block">Please enter valid number.</span>')
            }else{
                
                let errorBlock = $(this).parents('.mobile-landing-inputcover').next('.sms-error-block');
                $(errorBlock).html('<span class="help-block">Please enter your number.</span>');
            }
        }
    });

    $(".headline-action .link-underline,.my-add-cust-sec .headline-btn").click(function(event) {
        event.preventDefault();
        history.pushState({}, '', $(this).attr("href"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $($(this).attr("href")).offset().top - $('header').innerHeight()
        }, 200);
        
        return false;
    });

    $(document).on('click','.blog-load-more',function(){
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: getPostListingPath,
            dataType: "json",
            data:{page: parseInt($(this).attr('data-page')) + 1 },
            success: function (response) {
                if(response.status){
                    $('.blog-list-row').append(response.data);
                    console.log(response.page.current_page, response.page.last_page)
                    // hide load more btn on last page
                    if(response.page.current_page >= response.page.last_page){
                        $('.blog-load-more').hide();
                    }
                    
                    // update page number 
                    $('.blog-load-more').attr('data-page',response.page.current_page);
                                        
                }
                $('.blog-loader').hide();
            },
            beforeSend: function () {           
                $('.blog-loader').show();
            },
            error: function(jqXHR, textStatus, errorThrown){
                $('.blog-loader').hide();
            }
        });
    });
});
/** Document ready ends here*/
