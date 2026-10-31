$(document).ready(function () {

    if(typeof $("#account_address").val() !== "undefined"){
        var add_id = $("#account_address").val();//"{{ $id }}";
    }else{
        var add_id = null;
    }


    if(add_id != null && add_id != ''){
        $('.is_business_no').trigger('click');
        $("#uspsmodal1").modal("show");
    }

  $('.open-welcome-video').click(function(){
      $('#welcome_video').modal('show');

  });
  $('.youtube-video').click(function(){
      $('#welcome_video').modal('hide');
      $('object').attr('data', $('object').attr('data'));
  });

  $(document).on('click','.btn-step-1',function(){
      $('.tab-step-2').trigger('click');
      $('.tab-step-1').addClass('prev');
  });

  $(document).on('click','.btn-back-2',function(){
      $('.tab-step-1').trigger('click');
      $('.tab-step-1').removeClass('prev');

      $('.tab-step-2').removeClass('prev');
      $('.tab-step-3').removeClass('prev');
      $('.tab-step-4').removeClass('prev');
      $('.tab-step-5').removeClass('prev');
  });

  $(document).on('click','.btn-back-3',function(){
      $('.tab-step-2').trigger('click');
      $('.tab-step-2').removeClass('prev');

      //$('.tab-step-1').removeClass('prev');
      $('.tab-step-3').removeClass('prev');
      $('.tab-step-4').removeClass('prev');
      $('.tab-step-5').removeClass('prev');
  });

  $(document).on('click','.btn-back-4',function(){
      $('.tab-step-3').trigger('click');
      $('.tab-step-3').removeClass('prev');

      //$('.tab-step-2').removeClass('prev');
      //$('.tab-step-3').removeClass('prev');
      $('.tab-step-4').removeClass('prev');
      $('.tab-step-5').removeClass('prev');
  });

  $(document).on('click','.btn-back-5',function(){
      $('.tab-step-4').trigger('click');
      $('.tab-step-4').removeClass('prev');

      //$('.tab-step-2').removeClass('prev');
      //$('.tab-step-3').removeClass('prev');
      //$('.tab-step-4').removeClass('prev');
      $('.tab-step-5').removeClass('prev');
  });


  $('[data-mask]').inputmask();
  $("#phone").inputmask("999-999-9999",{ "clearIncomplete": true });
  $("#business_phone").inputmask("(999) 999-9999",{ "clearIncomplete": true });
  /**Signup basic info form */
    var uspsmodal_form_basic = $('.uspsmodal-form-basic').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            account_address: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            country: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            phone_number: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 10,
                minlength: 10,
            },
            address_line1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //checkPassword: true
                 minlength: 3,
                 maxlength: 100
            },
            address_line2: {
                //required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 100,
                //equalTo: '#password'
            },
            city: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 8,
                maxlength: 50,
                //equalTo: '#password'
            },
            state: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 8,
                //maxlength: 100,
                //equalTo: '#password'
            },
            postal_code: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 5,
                maxlength: 8,
                //equalTo: '#password'
            }
        },
        // errorPlacement: validateErrorPositioning,
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

            $('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            address_line1: {
                maxlength: 'Entered characters should not be more than 100.',
            },
            address_line2: {
                maxlength: 'Entered characters should not be more than 100.',
            },
            city: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            postal_code: {
                maxlength: 'Entered characters should not be more than 8.',
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

    $(document).on('click', '.btn-step-2', function () {
        //activate datetime picker
        //$('#business_registered_date').datetimepicker('destroy');
        $('#business_registered_date').datetimepicker({
          format: "M/D/Y",
          maxDate: 'now',

        });

        if ($('.uspsmodal-form-basic').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: uspsBasicInformation,
                dataType: "json",
                data: $('.uspsmodal-form-basic').serialize(),
                success: function (response) {
                    // console.log(response);
                    if (response.status) {
                        //next tab
                        $('.tab-step-3').trigger('click');
                        $('.tab-step-1').addClass('prev');
                        $('.tab-step-2').addClass('prev');

                        //$('.tab-step-1').removeClass('prev');
                        $('.tab-step-3').removeClass('prev');
                        $('.tab-step-4').removeClass('prev');
                        $('.tab-step-5').removeClass('prev');


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

    /* add member name whose mail is to be delivered*/
    $(".add-member-name").click(function(){
        var newRow = '<div class="row parent-row"><div class="col-md-12"><div class="form-group row mb-2 flex-column flex-md-row "><div class="col-12 col-md-12"><div class="usermodal3-small-input-cover"><input type="text" class="form-control" id="inputEmail" placeholder="" name="member_name[]"></div><a href="#" class="user-modal-plusicon remove-member-name" onclick="removeMember(this);"><img src="'+minus_icon_img+'" width="20"></a></div></div></div></div>';
        $(".add-member-div").append(newRow);
    })

    /* add member name whose mail is to be delivered*/
    $(".add-officer-name").click(function(){
        var newRow = '<div class="row parent-row"><div class="col-md-12"><div class="form-group row mb-2 flex-column flex-md-row "><div class="col-12 col-md-12"><div class="usermodal3-small-input-cover"><input type="text" class="form-control" id="inputEmail" placeholder="" name="officer_name[]"></div><a href="#" class="user-modal-plusicon remove-member-name" onclick="removeOfficer(this);"><img src="'+minus_icon_img+'" width="20"></a></div></div></div></div>';
        $(".add-officer-div").append(newRow);
    })

    /**Signup business info form */
    $.validator.addMethod("member_name_required", function (value, element) {
        var flag = true;

      $("[name^=member_name]").each(function (i, j) {
        $(this).parent('div').find('span.help-block').remove();
        //$(this).parent('p').find('label.error').remove();
        if ($.trim($(this).val()) == '') {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">This field is required.</span>');
        }else if(($.trim($(this).val()).length) > 50) {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">Entered characters should not be more than 50.</span>');
        }
      });
      return flag;
    }, "");
    $.validator.addMethod("officer_name_required", function (value, element) {
        var flag = true;

      $("[name^=officer_name]").each(function (i, j) {
        $(this).parent('div').find('span.help-block').remove();
        //$(this).parent('p').find('label.error').remove();
        if ($.trim($(this).val()) == '') {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">This field is required.</span>');
        }else if(($.trim($(this).val()).length) > 100) {
            flag = false;
            $(this).parent('div').append('<span id="inputEmail-error" class="help-block" style="">Entered characters should not be more than 100.</span>');
        }

      });
      return flag;
    }, "");
    var uspsmodal_form_business = $('.uspsmodal-form-business').validate({
        debug: false,
        //ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        ignore: ":hidden",
        rules: {
            firm_name: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 100
            },
            business_type: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            business_registered_country: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            business_phone_number: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 10,
                minlength: 10,
            },
            business_address: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //checkPassword: true
                 minlength: 3,
                 maxlength: 100
            },
            business_city: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 2,
                maxlength: 50,
                //equalTo: '#password'
            },
            business_state: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 2,
                //maxlength: 50,
                //equalTo: '#password'
            },
            business_registered_state: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 2,
                //maxlength: 50,
                //equalTo: '#password'
            },
            business_registered_date: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //minlength: 8,
                //maxlength: 100,
                //equalTo: '#password'
            },
            business_postal_code: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 5,
                maxlength: 8,
                //equalTo: '#password'
            },
            "member_name[]":{member_name_required:true},
            "officer_name[]":{officer_name_required:true},
        },
        // errorPlacement: validateErrorPositioning,
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

            $('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            firm_name: {
                maxlength: 'Entered characters should not be more than 100.',
            },
            business_city: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            business_state: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            business_address: {
                maxlength: 'Entered characters should not be more than 100.',
            },
            business_registered_state: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            business_postal_code: {
                maxlength: 'Entered characters should not be more than 8.',
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

    $(document).on('click', '.btn-step-3', function () {

        if ($('.uspsmodal-form-business').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: uspsBusinessInformation,
                dataType: "json",
                data: $('.uspsmodal-form-business').serialize(),
                success: function (response) {
                    if (response.status) {
                        //next tab
                        $('.tab-step-4').trigger('click');
                        $('.tab-step-1').addClass('prev');
                        $('.tab-step-2').addClass('prev');
                        $('.tab-step-3').addClass('prev');

                        //$('.tab-step-1').removeClass('prev');
                        $('.tab-step-4').removeClass('prev');
                        $('.tab-step-5').removeClass('prev');
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


    /**USPS document info form */
    $.validator.addMethod("noequalTo", function (value, element, param) {
        return this.optional(element) || value != $(param).val();
    }, "This value already selected in first document");

    var uspsmodal_form_document = $('.uspsmodal-form-document').validate({
        debug: false,
        ignore: [],
        errorClass: 'help-block',
        errorElement: "span",
        rules: {
            document_type_1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
            },
            document_type_2: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                noequalTo:"#document_type_1"
            },
            issued_by_1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30,
            },
            issued_by_2: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                //checkPassword: true
                 minlength: 3,
                 maxlength: 30
            },
            name_on_card_1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30
            },
            name_on_card_2: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 30,
                //equalTo: '#password'
            },
            id_number_1: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 50,
                //equalTo: '#password'
            },
            id_number_2: {
                required: true,
                normalizer: function( value ) {
                    return $.trim( value );
                },
                minlength: 3,
                maxlength: 50,
                //equalTo: '#password'
            }
        },
        // errorPlacement: validateErrorPositioning,
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

            $('html, body').animate({
                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
            }, 500);
        },
        messages: {
            issued_by_1: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            issued_by_2: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            name_on_card_1: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            name_on_card_2: {
                maxlength: 'Entered characters should not be more than 30.',
            },
            id_number_1: {
                maxlength: 'Entered characters should not be more than 50.',
            },
            id_number_2: {
                maxlength: 'Entered characters should not be more than 50.',
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

    $(document).on('click', '.btn-step-4', function () {
        $('#account_address_full_span').html($('#account_address_full').val());

        if ($('.uspsmodal-form-document').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: uspsDocumentInformation,
                dataType: "json",
                data: $('.uspsmodal-form-document').serialize(),
                success: function (response) {
                    if (response.status) {
                        //next tab
                        $('.tab-step-5').trigger('click');
                        $('.tab-step-1').addClass('prev');
                        $('.tab-step-2').addClass('prev');
                        $('.tab-step-3').addClass('prev');
                        $('.tab-step-4').addClass('prev');
                        $('.tab-step-5').removeClass('prev');
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

    /*save final usps info*/
    $(document).on('click', '.save-usps-info', function () {

        var notaryType = 0;
        var stateCode = $("#hidden_state").val();
        var postalCode = $("#postal_code").val();
        var searchPage = 'https://www.google.com/search?hl=en&biw=1375&bih=802&ei=cVeZXrqGN6SY4-EP9uO6oA8&q=Notary+in';
        searchPage = searchPage+"+"+stateCode+"+"+postalCode;

        // creating form object
        var form = $(".uspsmodal-form-basic");
        var MForm = new FormData(form[0]);

        if($(this).hasClass('btn-online-notary')){
            notaryType = 1;
            searchPage = "https://notarylive.com";
        }

        // append notary live in form
        MForm.append('notary_type', notaryType);

        if ($('.uspsmodal-form-basic').valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: storeUspsInformation,
                dataType: "json",
                processData: false,
                contentType: false,
                data: MForm,
                success: function (response) {

                    if (response.status) {
                        //REMOVE SUBSCRIBED ADDRESS AFTER FILL THE USPS
                        var account_address_seleted = $("#account_address_seleted").val();
                        var selectedElement = $("#account_address_seleted");
                        selectedElement.find('option[value='+account_address_seleted+']').remove();
                        //hide popup reset forms and back to step 1
                        $('#uspsmodal-form-basic').trigger("reset");
                        $('#uspsmodal-form-business').trigger("reset");
                        $('#uspsmodal-form-document').trigger("reset");
                        $("#uspsmodal1").modal("hide");
                        $(".btn-back-2").trigger('click');

                        //Download PDF
                        /*var filename = response.filename;
                        window.open(filename);*/
                        var current_path = response.filename.split('/').pop();
                        if(response.type == 0){
                            download_file(response.filename,current_path);
                        }

                        var win = '';
                        var popAllowedMessage = "You have successfully created the USPS form, You can upload this USPS PDF in the subscribed addresses section after notarization.";
                        var popNotAllowedMessage = "Please allow popups for this website";
                        if(response.type == 1){
                            win = window.open(response.redirection_url, '_blank');
                            popAllowedMessage = "You have successfully created the USPS form, Please complete the online notary step on NotaryLive to complete your online notary.";
                            popNotAllowedMessage = `Please allow popups for this website or <a href="${response.redirection_url}" target="_blank">click here</a> to complete your online notary process.`;
                        }else{
                            win = window.open(searchPage, '_blank');
                        }

                        if (win) {
                            //Browser has allowed it to be opened
                            win.focus();

                            //Show success alert with message
                            bootbox.alert({
                                title: "Alert!!",
                                message: popAllowedMessage,
                                callback: function (result) {

                                    //if comes from manage address list then redirect back to addrss list
                                    if(add_id != null && add_id != ''){
                                        window.location = manageAddresses;
                                    }
                                },
                            });
                        } else {
                            //Browser has blocked it
                            // alert('Please allow popups for this website');
                            //Show success alert with message
                            bootbox.alert({
                                title: "Alert!!",
                                message: popNotAllowedMessage,
                                callback: function (result) {
                                    //if comes from manage address list then redirect back to addrss list
                                    if(add_id != null && add_id != ''){
                                        window.location = manageAddresses;
                                    }
                                },
                            });
                        }

                    }
                },
                beforeSend: function () {
                    //disable the submit button

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });

    $('.account-address').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#account_address_full').val(text);
    });

    $('#state').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_state').val(text);
    });

    $('#business_state').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_business_state').val(text);
    });

    $('#business_registered_state').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_registered_state').val(text);
    });

    $('#business_type').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_business_type').val(text);
    });

    $('#document_type_1').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_document_type_1').val(text);
    });

    $('#document_type_2').on('change',function(){
        var text = $(this).find("option:selected").text();
        $('#hidden_document_type_2').val(text);
    });

    $(document).on('click', '.cancel-usps-wizard', function () {
        $('#uspsmodal-form-basic').trigger("reset");
        $('#uspsmodal-form-business').trigger("reset");
        $('#uspsmodal-form-document').trigger("reset");
        uspsmodal_form_basic.resetForm();
        uspsmodal_form_business.resetForm();
        uspsmodal_form_document.resetForm();
        //$("#uspsmodal1").modal("hide");
        //showhidebusiness(0);
        $('.is_business_no').trigger('click');
        $(".btn-back-2").trigger('click');
    });

    //open usps wizard
    $(document).on('click', '.uspsmodal1-usps-open', function () {
        $('.is_business_no').trigger('click');
        $('#uspsmodal1').modal('show');
    });
});


function showhidebusiness(value){
  if(value==1){
    $('.is_business').show();
    //$('.uspsmodal-form-business input').show();
  }else{
    $('.is_business').hide();
    //$('.uspsmodal-form-business input').hide();
  }
}

function removeMember(element){
  //console.log($(element).closest('.parent-row'));
  $(element).closest('.parent-row').remove();
}

function removeOfficer(element){
  $(element).closest('.parent-row').remove();
}

