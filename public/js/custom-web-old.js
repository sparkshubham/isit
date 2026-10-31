function headerFixAddRemove() {

    //Heder Fixed Removed on Landscape mode
    if ($(window).height() < 480 || $('section').hasClass('defaultHeaderWhite') ) {
        if(document.documentElement.scrollTop >= 0) {
            if(!$('.top-header').hasClass('top-header-white')) {
                $('.top-header').addClass('top-header-white');
            }
        }
        $(window).scroll(function(e) {
            var scroll = $(window).scrollTop();
            if (scroll >= 0) {
                $('.top-header').addClass("top-header-white");
            }

        });
    }else{
        //Menu On Scroll White Header
        if(document.documentElement.scrollTop >= 1) {
                if(!$('.top-header').hasClass('top-header-white')) {
                    $('.top-header').addClass('top-header-white');
                }
            }
        $(window).scroll(function(e) {
            var scroll = $(window).scrollTop();
            if (scroll >= 1) {
                $('.top-header').addClass("top-header-white");
            } else {
                $('.top-header').removeClass("top-header-white");
            }

        });
    }
}

//Header/Footer Style 2
if($('section').hasClass('changeHeaderFooter')) {
    $('#footerMain').addClass('footer-style-2');
    $('.top-header').addClass('header-style-2');
}
if($('section').hasClass('ifShowHeaderTitle')) {
    $('.top-header').addClass('show-header-title');
}
if($('section').hasClass('changeFooterOnly')) {
    $('#footerMain').addClass('footer-style-2');
}
//Header Search button Show/Hide (Only For test)
$('.right-menu-links .btn').click(function(){
    $('.header-search-form').toggleClass('active');
    setTimeout(function(){
        $('.right-menu-links').toggleClass('active-search');
    }, 500);
});

//Menu Open in Mobile disabled the body content
$('.top-header-white.top-header .navbar-toggler').click(function(){
    $("body").toggleClass('navbar-toggler-active');
});

// To style all selects
$(function () {
    $('select').selectpicker({
        dropupAuto:false
    });
});

// To style all Toggle
$(".onToggle").click(function(){
    $(this).toggleClass('active');
    $(".toggleOpen").toggle();
});

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
    headerSet();
    headerFixAddRemove();
    transformSet();
    setBgHeight();
    checkMedia1199();
});

function headerSet() {
    var headerTopHeight = $('.top-header .navbar').height();
    $('.navbar-menu-list li a[href*="#"]:not([href="#"]), .obox-right-btns a[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
        var topHeaderHeight = ($)
        if (target.length) {
          $('html,body').animate({
            scrollTop: target.offset().top - headerTopHeight

          }, 1000);
          //console.log("Height is" + headerTopHeight );
          return false;
        }
      }
    });
}

function transformSet(currParent) {
    /*var sectionSelectors = $('.doc-order-list');
    var contents = $('.obox-form-body');
    var itemWidth = $('.doc-order-list li').outerWidth(true);
    var itemCompletedWidth = $('.doc-order-list li.completed').length;
    sectionSelectors.css('transform', 'translateX(calc(50% - ' + ((itemCompletedWidth*itemWidth) + (itemWidth/2)) + 'px))');*/

    var sectionSelectors = $('.doc-order-list');
    var contents = $('.obox-form-body');
    var itemWidth = $('.doc-order-list li').outerWidth(true);
    //var itemCompletedWidth = $('.doc-order-list li.completed').length;
    var itemCompletedWidth = $(currParent).prevAll('li.section-form-show').length;
    sectionSelectors.css('transform', 'translateX(calc(50% - ' + ((itemCompletedWidth*itemWidth) + (itemWidth/2)) + 'px))');
}

/*Sidebar Left Controls and UI management */
function setBgHeight() {
    var getMenuBgHeight = $(".onboarding-banner").outerHeight();
    var headerTopHeight = $('.top-header .navbar').height();

    if($(".onboarding-content").hasClass('onboarding-content-sidebar1')){
        var finalHeight = getMenuBgHeight + (headerTopHeight*2);
    }
    else{
        var finalHeight = getMenuBgHeight + headerTopHeight;
    }

    if($(".information-box").is(':visible')){
        var infoBoxOnDucument = $(".information-box").outerHeight();
        finalHeight = finalHeight + infoBoxOnDucument;
    }
    $(".onboarding-bg").css('height', finalHeight);
}

function checkMedia1199(){

    if($(window).outerWidth(true) <= 1199)
    {
        $(".onboarding-content").removeClass('onboarding-content-sidebar');
    }else{
        //$(".onboarding-content").addClass('onboarding-content-sidebar');
    }
}

// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
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
        alert('No response from server.');
    });
}

function careerFilterUrl(){
    var url = careerPath;

    var location = getSelectedLocation();
    if(location){
        url += '/'+location;
    }

    var category = getSelectedCategory('category-careers');
    if(category){
       url += '/'+category;
    }
    return url;
}

function getSelectedCategory(className){
    var category = '';
    $('.'+className).each(function(){
    if($(this).hasClass('active')){
      category = $(this).data('slug');
    }
    });
    return category;
}

function getSelectedLocation(){
    var location = $(".career-location").find('option:selected').val();
    return location;
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

/** to show forgot password pop up */
$(document).on('click','.forgot-password-popup',function(){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);

    $('#loginModal').modal('hide');
    $('#registerModal').modal('hide');
    $('#resetModal').modal('show');
});

/** to show forgot password sing in */
$(document).on('click','.sign-in-popup-model',function(e){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);
    $('.txt-timezone').val(userTimeZone);
    $('#resetModal').modal('hide');
    $('#registerModal').modal('hide');
    $('#loginModal').modal('show');
});

/** to show sing in from header's option*/
$(document).on('click','.sign-in-popup-model-header',function(e){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);
    $('.txt-timezone').val(userTimeZone);
    $('#resetModal').modal('hide');
    $('#registerModal').modal('hide');
    $('#loginModal').modal('show');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: destroySessionVariablePath,
        dataType: "json",
        success: function (response) {

        },
        beforeSend: function () {
            //disable the submit button

        }
    });
});

/** to show sign up */
$(document).on('click','.sign-up-popup-model',function(){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);
    $('.txt-timezone').val(userTimeZone);
    $('#resetModal').modal('hide');
    $('#loginModal').modal('hide');
    $('#registerModal').modal('show');
});

/** to show sign up from header's option */
$(document).on('click','.sign-up-popup-model-header',function(){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);
    $('.txt-timezone').val(userTimeZone);
    $('#resetModal').modal('hide');
    $('#loginModal').modal('hide');
    $('#registerModal').modal('show');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: destroySessionVariablePath,
        dataType: "json",
        success: function (response) {

        },
        beforeSend: function () {
            //disable the submit button

        }
    });
});

$(document).on('click','.close-all-model',function(){

    // Getting form objects
    var clearLoginForm = document.getElementById("login_form");
    var clearRegisterForm = document.getElementById("register_form");
    var clearResetForm = document.getElementById("forgot_password_form");
    // Call clear validation function
    clearValidation(clearLoginForm);
    clearValidation(clearRegisterForm);
    clearValidation(clearResetForm);

    $('#resetModal').modal('hide');
    $('#loginModal').modal('hide');
    $('#registerModal').modal('hide');
});

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

function saveSubscriptionResponse(responseText, statusText, xhr, $form){
    // Hide Model
    $('#registerModal').modal('hide');
    // Reload or replace loction
    if(responseText.url != 0){
        location.replace(responseText.url);
    }else{
        location.reload();
    }
}


function ajaxSubmitForm(){
    if($('.form-validate').valid()){
        $('#register_form').ajaxForm({
            success: saveSubscriptionResponse,
            error: validationFailure
        });
    }
}

function saveSubscriptionResponseLogin(responseText, statusText, xhr, $form){
    $('#loginModal').modal('hide');
    // console.log(responseText);
    // Reload or replace loction
    if(responseText.url != 0){
        location.replace(responseText.url);
    }else{
        location.reload();
    }
}

var loginPopup=false;
function ajaxSubmitLoginForm(){
    loginPopup=true;
    if($('.form-validate_login').valid()){
        $('#login_form').ajaxForm({
            success: saveSubscriptionResponseLogin,
            error: validationFailure
        });
    }
}

function ajaxSubmitLoginForm2(){
    loginPopup=false;
    if($('.form-validate2').valid()){
        $('#login_form2').ajaxForm({
            success: saveSubscriptionResponseLogin,
            error: validationFailure
        });
    }
}

/*submit secure payment form*/
function ajaxSubmitPaymentForm(){
    if($('#payment_form').valid()){
        $('#payment_form').ajaxForm({
            success: saveSecurePaymentDetail,
            error: validationFailure
        });
    }
}

function saveSubscriptionResponseForgot(responseText, statusText, xhr, $form){
    // console.log('responseText');
    $('#resetModal').modal('hide');
    showAlert(1,responseText.message);
    //location.reload();
}


function ajaxSubmitForgotForm(){
    if($('.form-validate_forgot').valid()){
        $('#forgot_password_form').ajaxForm({
            success: saveSubscriptionResponseForgot,
            error: validationFailure
        });
    }
}

/*setting form*/
function saveSubscriptionResponseProfile(responseText, statusText, xhr, $form){
    $("span.help-block").remove();
    $('.help-block').removeClass('help-block');
    if (responseText.error == 0) {
        $("#profile_error").html("");
        showAlert(1,responseText.message);
    }else{
        // show error message
        $("#profile_error").html(responseText.message);
    }
}
function ajaxSubmitSettingForm(){

    $('.form-validate-profile').validate({
        debug: false,
        ignore : [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            first_name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            last_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
            },
            email: {
                    required: true,
                    email: true
            },
            phone: {
                    required: true,
                    number: true
            },
            oldpassword: {
                required: function (element) {
                if($("#accountNewPass").val().length > 0){
                    return true;
                } else {
                    return false;
                }
                },
                minlength:8,
                maxlength:32
            },
            password: {
                required: function (element) {
                if($("#accountNewPass").val().length > 0){
                    return true;
                } else {
                    return false;
                }
                },
                minlength:8,
                maxlength:32
            },
            password_confirmation: {
                required: function (element) {
                if($("#accountNewPass").val().length > 0){
                    return true;
                } else {
                    return false;
                }
                },
                minlength:8,
                maxlength:32
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
                first_name: {
                    maxlength: 'Entered characters should not be more than 255.',
                },
                last_name: {
                    maxlength: 'Entered characters should not be more than 255.',
                },
                email: {
                    maxlength: 'Entered characters should not be more than 255.',
                },
            }
    });
    if($('.form-validate-profile').valid()){
        $('#setting_form').ajaxForm({
            success: saveSubscriptionResponseProfile,
            error: validationFailure
        });
    }
}

/** Document ready start here*/

$(document).ready(function () {

    // For UI

    headerSet();
    headerFixAddRemove();
    transformSet();
    setBgHeight();
    checkMedia1199();

    //For Tooltip
    $('[data-toggle="tooltip"]').tooltip({
        html:true
    });

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

    //** validation additional methods
    jQuery.validator.addMethod("careerextension",function(a,b,c){
    return c="string"==typeof c?c.replace(/,/g,"|"):"pdf|doc?x",this.optional(b)||a.match(new RegExp("\\.("+c+")$","i"))
    },jQuery.validator.format("File format must be PDF or Doc/Docx."));

    jQuery.validator.addMethod('filesize', function (value, element, arg) {
        if(element.files[0] !== undefined){
            if(element.files[0].size<=arg){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
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
                email: true
            },
            phone: {
                required: true,
                number: true
            },
            subject: {
                required: true,
            },
            subject: {
                required: true,
            }
        },
    });

    $('#set-password-form').validate({
        debug: false,
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            password: {
                required: true,
                minlength: 8,
                maxlength: 32
            },
            password_confirmation: {
                required: true,
                minlength: 8,
                maxlength: 32,
                equalTo : '#password'
            }
        },
        messages: {
            password: {
                minlength: "Password should be between 8 to 32 characters",
                maxlength: "password should be between 8 to 32 characters",
            },
            password_confirmation: {
                minlength: "Password should be between 8 to 32 characters",
                maxlength: "Password should be between 8 to 32 characters",
                equalTo : 'Password and confirm password must be same'
            }
        }
    });

    //$(document).off('click','.contact-us-btn');
    $(document).on('click','.contact-us-btn',function(){

        $(document).off('click','.contact-us-btn');
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
                    $('.contact-us-btn').html('Send Message <small><i class="icon-arrow-right"></i></small>');
                    $("contact-us-btn").attr("disabled", false);
                    $("#contact-us-form").trigger("reset");
                }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function(jqXHR, textStatus, errorThrown)
                    {
                        var responseText = JSON.parse(jqXHR.responseText);
                        if(responseText.errors)
                        {
                            for(index in responseText.errors)
                            {
                                $('.error[for="' + index + '"]').text(responseText.errors[index][0]);
                            }
                        }
                    }
            });
        }
    });

    $(".resume-links a").click(function(){
        var type = $(this).data('type');
        $(".resume-links a").removeClass('career-active');
        $(".resume-container").show();
        $(".resume-container").find('div').hide();
        $(".resume-container .resume-"+type).show();
        $(this).addClass('career-active');
        $("#resume_option").val($(this).data('option'));
    });

    $(".cover-links a").click(function(){
        var type = $(this).data('type');
        $(".cover-links a").removeClass('career-active');
        $(".cover-container").show();
        $(".cover-container").find('div').hide();
        $(".cover-container .cover-"+type).show();
        $(this).addClass('career-active');
        $("#cover_letter_option").val($(this).data('option'));
    });

    // For home page auto complete
    var temp = JSON.parse(availableTags);
    $.typeahead({
    input: '.js-typeahead-country_v2',
    order: "desc",
    source: {
        data: temp
    },
    callback: {
        onInit: function (node) {
            // console.log('Typeahead Initiated on ' + node.selector);
        },
        onClickAfter: function (node, a, item, event) {
            // console.log($(node).closest('.typeahead__field').next('#typeahead__result'));
            event.preventDefault();
            $(node).closest('.typeahead__field').next('.typeahead__result').hide();
            $(node).closest('.typeahead__field').next('#typeahead__result').text('');

        },
        onSubmit: function (node, form, item, event) {
            event.preventDefault();

            if(item != null){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: getDocuemntSlugPath,
                    dataType: "json",
                    data: {slug:documentSlug,document_name:item.display},
                    success: function (response) {
                        window.location.replace(documentPath+'/'+response.slug);
                    },
                    beforeSend: function () {
                        //disable the submit button

                    },
                    error: function(jqXHR, textStatus, errorThrown){

                    }
                });

            }else{

                if(node.val()){
                    window.location.replace(getDocuemntSearchPath+'/'+node.val());
                }
            }
            $(node).closest('.typeahead__field').next('.typeahead__result').hide();
        },
        onSearch: function (node, form, item, event) {
            if(!form){
                $(node).closest('.typeahead__field').next('.typeahead__result').hide();
            }else{
                $(node).closest('.typeahead__field').next('.typeahead__result').show();
            }
        }
    }
    });


$('.form-validate_forgot').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          email: {
              required: true,
              email:true
          },
      },
      errorPlacement: validateErrorPositioning,
      highlight: function (element, errorClass, validClass)
            {
                $(element).parents('.col-field-wrapper').addClass('has-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.col-field-wrapper').removeClass('has-error');
            },
            /*invalidHandler: function(form, validator)
            {
                if (!validator.numberOfInvalids())
                    return;

                $('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);
            }*/
    });

    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          email: {
              required: true,
              email:true
          },
          password: {
            required: true,
            minlength:8,
            maxlength:32
          },
          password_confirmation: {
            required: true,
            minlength:8,
            maxlength:32
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

                /*$('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);*/
            }
    });


    /*Login validation*/
    $('.form-validate2').validate({
      debug: false,
      ignore : [],
      errorClass: 'error-message',
      errorElement: "span",
      rules: {
          email: {
              required: true,
          },
          password: {
            required: true,
            minlength:8,
            maxlength:32
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
            }
    });
    /*Login popup validation*/
    $('.form-validate_login').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          email: {
              required: true,
          },
          password: {
            required: true,
            minlength:8,
            maxlength:32
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

                /*$('html, body').animate({
                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                }, 500);*/
            }
    });

    /* For secure payment detail validation*/
    $('#payment_form123').validate({
      debug: false,
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          paySecurityCardNumber: {
              required: true
          },
          paySecurityMonth: {
              required: true
          },
          paySecurityYear: {
              required: true
          },
          paySecurityCode: {
              required: true
          },
          payzipcode: {
              required: true
          },
      },
      errorPlacement: function(error,element){
        if($(element).prop('nodeName') == 'SELECT'){
          error.insertAfter($(element).parent().parent());
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
        /*if (!validator.numberOfInvalids())
          return;

        $('html, body').animate({
          scrollTop: ($(validator.errorList[0].element).offset().top - 100)
        }, 500);*/
      }
    });

    // For Document category page
    $(document).on('click','.content-block',function(){
        $('html, body').animate({
            scrollTop: ($('#content-block').offset().top - 50)
          }, 500);
    });

    $(document).on('click','.banner-arrow-document',function(){
        var slug = $(this).attr('data-slug');
        if(slug){
            loadDocumentCategory(slug)
        }
    });

    // Document search page
    if(typeof(documentSearchDocs) != "undefined" && documentSearchDocs !== null){
        documentSearchDocuemnts = JSON.parse(documentSearchDocs);
    }else{
        documentSearchDocuemnts = [];
    }

    // Get document slug

    var documentSlug = $('.document_search_slug').val();

    $.typeahead({
        input: '.js-typeahead-document-search',
        order: "desc",
        source: {
            data: documentSearchDocuemnts
        },
        callback: {
            onInit: function (node) {
                // console.log('Typeahead Initiated on ' + node.selector);
            },
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                $('.document-search-container').find('.typeahead__result').hide();
                $('.document-search-container').find('#result-container').text('');
            },
            onSubmit: function (node, form, item, event) {

                event.preventDefault();
                if(item != null){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'GET',
                        url: getDocuemntSlugPath,
                        dataType: "json",
                        data: {slug:documentSlug,document_name:item.display},
                        success: function (response) {
                            window.location.replace(documentPath+'/'+response.slug);
                        },
                        beforeSend: function () {
                            //disable the submit button

                        },
                        error: function(jqXHR, textStatus, errorThrown){

                        }
                    });

                }else{
                    if($('.js-typeahead-document-search').val()){
                        window.location.replace(getDocuemntSearchPath+'/'+$('.js-typeahead-document-search').val()+'/'+documentSlug);
                    }
                }
                $('.document-search-container').find('.typeahead__result').hide();

            },
            onSearch: function (node, form, item, event) {
                if(!form){
                    $('.document-search-container').find('.typeahead__result').hide();
                    $('.document-search-container').find('.typeahead__result').html('');
                }else{
                    $('.document-search-container').find('.typeahead__result').show();
                }
            }
        }
        });


      $(document).on('click','.account-payment-sub-btn',function(){
        event.preventDefault();
        if($('.payment-method-form').valid()){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: accountPaymentMethod,
                dataType: "json",
                data: $('.payment-method-form').serialize(),
                success: function (response) {
                    if(response.status == true){
                        // showAlert(1,'We’re thrilled to hear from you. Our inbox can’t wait to get your messages, so talk to us any time you like.');
                        $('.account-payment-method-error').html('<div class="alert alert-success alert-dismissible mt-4"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+response.message+'</div>');
                        $('.payment-method-form').trigger("reset");
                        $('.saved-card-username').html(response.card_details.name);
                        $('.saved-card-type').html(response.card_details.brand);
                        $('.saved-card-number').html(response.card_details.number);
                        $('.saved-card-expiry').html(response.card_details.expiration_date);
                        $('.saved-card-details').show();
		                $('.add-card-details').hide();
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
                                $('input[name="'+i+'"]').after(error);
                                $('input[name="'+i+'"]').addClass('help-block');
                            });
                        }
                    }
            });
        }
      });

    $(document).on('click','.payment-method-edit',function(){
        $('.saved-card-details').hide();
        $('.add-card-details').show();
        $('.account-payment-cancel-btn').show();
        $('.account-payment-method-error').hide();
    });
    $(document).on('click','.account-payment-cancel-btn',function(){
        $('.saved-card-details').show();
        $('.add-card-details').hide();
        $('.payment-method-form').trigger("reset");
        $("span.help-block").remove();
        $('.help-block').removeClass('help-block');
        $('.account-payment-method-error').hide();
    });

    $(document).on('click','.cancel-plan-btn',function(){
        var path = $(this).attr('data-href');

        bootbox.confirm({
            title: "Cancel Plan",
            message: "Are you sure? You won't able to create, edit and download document anymore.",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if(result){
                    location.replace(path);
                }
            }
        });
    });

    $(document).on('click','.home-create-document-btn',function(){
        event.preventDefault();

        var slug = $('.create_document_dropdown').find('option:selected').val();
        if(slug){
            window.location.replace(documentPath+'/'+slug);
        }
    });

    $(document).on('click','.my-account-tabbing',function(){
        var id = $(this).attr('data-id');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: myAccountSection,
            dataType: "HTML",
            data: {id:id},
            success: function (response) {
                if(id == 1){
                    $('#myDocuments').html(response);
                }else if(id == 2){
                    $('#billingHistory').html(response);
                    $("#billingHistory").tooltip({ selector: '[data-toggle=tooltip]',html:true });
                }else if(id == 3){
                    $('#paymentMethods').html(response);
                    $('select').selectpicker({
                        dropupAuto:false
                    });
                    $("#paymentMethods").tooltip({ selector: '[data-toggle=tooltip]',html:true });

                    /*Account Payment Method Form*/
                    $('.payment-method-form').validate({
                        debug: false,
                        ignore : [],
                        errorClass: 'help-block',
                        errorElement: "span",
                        rules: {
                            card_number: {
                                required: true,
                                number: true,
                                minlength: 14,
                                maxlength: 16
                            },
                            card_name: {
                                required: true,
                            },
                            security_code: {
                                required: true,
                                number: true,
                                minlength: 3,
                                maxlength: 4
                            },
                            card_month: {
                                required: true,
                            },
                            card_year: {
                                required: true,
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

                                /*$('html, body').animate({
                                    scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                                }, 500);*/
                            }
                    });

                }else if(id == 4){
                    $('#signInSeetings').html(response);
                }
                $(".mCustomScrollbar").mCustomScrollbar({
                  scrollButtons:{enable:true},
                  theme: 'dark-thick'
                });
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function(jqXHR, textStatus, errorThrown){

            }
        });

    });
});
/** Document ready ends here*/
