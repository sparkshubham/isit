$(document).ready(function () {

    jQuery.validator.addMethod("customEmailValidation", function (value, element, params) {
        var idname = $(element).attr('id');
        var emailText = $(element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailText);
    }, jQuery.validator.format("Please enter a valid email address."));
    /** personal preferences form */
    $('.personal-preferences').validate({
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
                            return $('.personal-preferences :input[name="email"]').val();
                        },
                        id: function()
                        {
                            return $('#scret_id').val();
                        },
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
                //minlength: 10,
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
        },
        submitHandler : function(form,e) {
            //do something here
            e.preventDefault();
            if($(form).valid()){
                form.submit();
            }
        }
    });


    /** change password form */
    $.validator.addMethod("noSpaceCheck", function(value, element) {
        //alert(value.indexOf(" "));
      //return value.indexOf(" ") < 0 && value != "";
      console.log(value.indexOf(" "));
      /*if((value.indexOf(" ") > -1))
        return false;
      else return true;*/
      return this.optional(element) || /^\S+$/i.test(value);
    }, "Space not allowed.");
    $.validator.addMethod('checkPassword', function (value) {
        return /^(?=.*?[0-9])(?=.*?[a-z])(?=.*[A-Z])(?=.*[~!@#$%^&*()_+=-]).{8,32}$/.test(value);
    }, 'Your password must have 8 to 32 characters containing 1 upper case letter, 1 lower case letter, 1 special character & 1 digit.');
    $('.change-password').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            current_password: {
                required: true,
                //noSpace: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                checkPassword: true,

                // minlength: 8,
                // maxlength: 32
            },
            password: {
                required: true,
                //noSpaceCheck: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                checkPassword: true

                // minlength: 8,
                // maxlength: 32
            },
            password_confirmation: {
                required: true,
                //noSpace: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 8,
                maxlength: 32,
                equalTo: '#password',

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

    $("#password").on("keypress", function(e) {
        if (e.which === 32)
            e.preventDefault();
    });
    $("#current_password").on("keypress", function(e) {
        if (e.which === 32)
            e.preventDefault();
    });
    $("#password_confirmation").on("keypress", function(e) {
        if (e.which === 32)
            e.preventDefault();
    });
});
