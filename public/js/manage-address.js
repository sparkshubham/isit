$(document).ready(function () {
    window['address'] = [];
    window['add_edit'] = '';
    window['subscribed_address_id'] = '';
    window['extra_fee'] = '';
    window['old_address_id']='';
    //loadAddressPage(1);
    window['address_delete_id'] = 0;
    // load more option for custom lazy load 
    $(document).on('click', '.btn-load-more', function () {
        loadAddressPage(parseInt($('.currentPage').val()) + 1,window['old_address_id']);
        if ($('.currentPage').val() == $('.lastPage').val()) {
            $('.load-more').hide();
        }
    });

    // continue button on address tab
    $(document).on('click','.btn-continue',function(){
        if(window['address'].length > 0) {
            //console.log(window['address']);
            //console.log(window['subscribed_address_id']);
            var additional_address_fee_per_month = $("#additional_address_fee_per_month").val();

            var fee = parseFloat(additional_address_fee_per_month).toFixed(2);
            window['extra_fee'] = fee;
            $(".additional-address-fee").html('$'+fee);
            //if(window['subscribed_address_id']==""){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: checkAddressExtraFee,
                    dataType: "json",
                    async: false,
                    data: {id:window['subscribed_address_id']},
                    success: function (data) {
                        if(data.status){
                            $("#confirmModal").modal("show");            
                        }else{
                            window['extra_fee']=null;
                            addUpdateSubscribedAddress();
                        }
                    },
                    error: function (e) {
                        console.log("ERROR : ", e);
                    }
                });
                
            /*}else{
                addUpdateSubscribedAddress();
            }*/
            //save address id in the subscribed addresses

            /*$('html, body').animate({
                scrollTop: $('#myTabContent').offset().top - 200
            }, 'fast');*/

        } else {

            bootbox.alert({
                title: "Alert!!",
                message: "Please select address first",
                callback: function (result) {
                    setTimeout(function(){ $('body').addClass('modal-open'); }, 500);
                },
            });
            
            // $('.btn-continue').prop('disabled', true);
        }
    });

    /* add/edit address */
    $(document).on('click', '.add-edit-address-btn', function () {
        addUpdateSubscribedAddress();        
    });

    /*close modal and reload the page*/
    $(document).on('click', '.add-edit-address-cancel-btn', function () {
        $("#confirmModal").modal("hide");
        location.reload();
    });

    /**selected address */
    $(document).on('click', '.addresses-container .card', function () {
        let addressCount = 1;//window['plan_address'];
        if (typeof addressCount !== 'undefined') {
            if(!$(this).hasClass('disabled')){
                let address = '<div class="card h frm" id="card' + $(this).attr('data-number') + '"><div class="card-icon"><i class="icon-point" style="font-size: 40px;"></i></div><div class="card-body"><p class="card-text">' + $(this).find('.card-text').html() + '</p></div></div>';

                let addressArr = window['address'];
                let removeItem = $(this).attr('data-id');

                // select and de-select the address
                if ($(this).hasClass('signup-selected-address')) {
                    $(this).removeClass('signup-selected-address');
                    $(this).removeClass('address-highlighted-class');
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
                    $(this).removeClass('address-highlighted-class');
                    $('#card' + $(this).attr('data-number')).remove();

                    // Remove address from array
                    addressArr = jQuery.grep(addressArr, function (value) {
                        return value != removeItem;
                    });
                    window['address'] = addressArr;

                    bootbox.alert({
                        title: "Alert!!",
                        message: "You can not select more than " + addressCount + " address(es)",
                        callback: function (result) {
                            setTimeout(function(){ $('body').addClass('modal-open'); }, 500);
                        }
                    });
                }
            }else{
                bootbox.alert({
                        title: "Alert!!",
                        message: "You can not select this address as you have already added this address",
                        callback: function (result) {
                            setTimeout(function(){ $('body').addClass('modal-open'); }, 500);
                        }
                    });
            }
        } else {
            $(this).removeClass('signup-selected-address');
            $(this).removeClass('address-highlighted-class');
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

    $(".add-address-btn").click(function(){
        if(isPaymentAvailable){
            if($(this).attr('data-subscription')==1){
                loadAddressPage(1);
                window['add_edit'] = 1;
                window['subscribed_address_id']='';
                window['address'] = [];
            }else{
                bootbox.alert({
                    title: "Alert!!",
                    message: "You do not have any plan, Please purchase a subscription plan to proceed",
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
                        className: 'bootbox-alert-ok-btn'
                    }
                },
                callback: function (result) {
                    if(result){
                        location.replace(subScriptionPagePath);
                    }
                }
            });
        }
    })

    $(".edit-address-btn").click(function(){
        window['add_edit'] = 2;    
        window['subscribed_address_id'] = $(this).attr('data-id');
        window['address'] = [];
        window['old_address_id'] = $(this).attr('data-address-id');
        loadAddressPage(1,$(this).attr('data-address-id'));
    });

    //Delete address
    $(document).on('click', '.delete-address', function () {
        window['address_delete_id'] = $(this).attr('data-id');;
        $("#deleteConfirmModal").modal('show');
    });
    $(document).on('click', '.delete-confirmation-btn', function () {
        $("#deleteConfirmModal").modal('hide');
        var id = window['address_delete_id'];//$(".delete-address").attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: removeSubscribedAddress,
            dataType: "json",
            data: {id:id},
            success: function (response) {
                if (response.status) {
                    
                    var message = "Address removed successfully !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            location.reload();
                        }
                    });
                    //location.reload(); 
                }
            },
            beforeSend: function () {
                //disable the submit button

            },
            error: function (jqXHR, textStatus, errorThrown) {
                var message = "Spmething went wrong !";
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            //location.reload();
                        }
                    });
            }
        });
    });

    /*upload CMRA file*/
    $(document).on('click', '.cmra-action-upload', function () {
        var data_id = $(this).attr('data-id');
        $("#scret_id").val(data_id);
        $(".file-upload").trigger('click');
    });
    $(document).on('change', '.file-upload', function (e) {
        var fileName = e.target.files[0].name;
        extension = fileName.split('.').pop();
        //alert(fileName+' and '+extension);
        //return false;
        if(extension=='pdf'){

            // Get form
            var form = $('#upload_cmra_form')[0];

            // Create an FormData object 
            var data = new FormData(form);

            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: uploadCmraForm,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {

                    //console.log(data.status);
                    if(data.status){
                        var message = "CMRA form uploaded successfully";            
                    }else{
                        var message = "CMRA form uploaded failed, Please try again";            
                    }
                    bootbox.alert({
                        title: "Alert!!",
                        message: message,
                        callback: function (result) {
                            location.reload();
                        }
                    });
                },
                error: function (e) {
                    console.log("ERROR : ", e);
                }
            });
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select pdf file",
                callback: function (result) {
                    
                }
            });
        }
    });

    //local notary
    $(document).on('click', '.notary-class', function () {
        //download PDF
        
        var filename = $(this).attr("filepath");
        /*window.open(filename);*/
        var onlineNotaryStatus = $(this).attr('online-notary-status');

        var searchKeyword = $(this).attr("search-keyword");
        var searchPage = 'https://www.google.com/search?hl=en&biw=1375&bih=802&ei=cVeZXrqGN6SY4-EP9uO6oA8&q=Notary+in';
        searchPage = searchPage+"+"+searchKeyword;
        if($(this).hasClass('online-notary')){
            var id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: getOnlineNotaryLinkPath,
                dataType: "json",
                data: {id},
                success: function (response){
                    if(response.status){
                        var win = window.open(response.redirection_url, '_blank');
                        if (win) {
                            //Browser has allowed it to be opened
                            win.focus();
                        } else {
                            //Browser has blocked it
                            // alert('Please allow popups for this website');

                            bootbox.alert({
                                title: "Alert!!",
                                message: `Please allow popups for this website or <a href="${response.redirection_url}" target="_blank">click here</a> to complete your online notary process.`,
                                callback: function (result) {
                                    location.reload();
                                }
                            });
                        }
                    }
                },
            });
        }else{

            if(onlineNotaryStatus != ''){
                bootbox.confirm({
                    title: "Alert",
                    message: 'An online notary request is under process for the current address, you can continue that online notary by clicking on "Online Notary" option or if you choose the "Local Notary" option then you have to upload the notarized cmra document.',
                    buttons: {
                        cancel: {
                            className: 'bootbox-alert-hide-btn'
                        }
                        ,
                        confirm: {
                            label: 'Choose local notary',
                            className: ' bootbox-alert-ok-btn'
                        }
                    },
                    callback: function (result) {
                        if(result){
                            var current_path = filename.split('/').pop();
                            download_file(filename,current_path);

                            var win = window.open(searchPage, '_blank');
                            if (win) {
                                //Browser has allowed it to be opened
                                win.focus();
                                location.reload();
                            } else {
                                //Browser has blocked it
                                console.log('Please allow popups for this website');
                            
                            }
                        }
                    }
                });
            }else{
                var current_path = filename.split('/').pop();
                download_file(filename,current_path);

                var win = window.open(searchPage, '_blank');
                if (win) {
                    //Browser has allowed it to be opened
                    win.focus();
                    location.reload();
                } else {
                    //Browser has blocked it
                    console.log('Please allow popups for this website');
                            
                }
            }
        }
    });
});

//save the address
function addUpdateSubscribedAddress(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: addEditAddress,
        dataType: "json",
        data: {address:window['address'],id:window['subscribed_address_id'],fee:window['extra_fee']},
        success: function (response) {
            if (response.status) {
                $("#confirmModal").modal("hide");
                $("#uspsmodal1").modal("hide");
                var message = "You are now subscribed to your new address. Please complete the CMRA form to receive all of the benefits of your new address.";
                if(window['subscribed_address_id'] != "")
                    message = "Address updated successfully, You have to fill CMRA form again";
                bootbox.alert({
                    title: "Success",
                    message: message,
                    callback: function (result) {
                        location.reload();      
                        //alert(321321);
                    }
                });
                //location.reload(); 
            }
        },
        beforeSend: function () {
            //disable the submit button

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

// load signup page addresses using custom lazy load

function loadAddressPage(page,addressId=null){
    /*if(page==1){
        window['address'] = [];
        window['add_edit'] = '';
        window['subscribed_address_id'] = '';
        window['extra_fee'] = '';
    }*/
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: getSignupAddress,
        dataType: "html",
        async: false,
        data: {page:page,addressId:addressId},
        success: function (response) {
            $("#uspsmodal1").modal('show');
            //add already selected class
            //var selectedAddress = window['subscribed_address_id'];
            //$( 'div[ data-id="' + selectedAddress + '"]' ).addClass( 'signup-selected-address' );
            //console.log(selectedAddress);
            $('.lastPage').remove();
            $('.currentPage').remove();
            if(page==1){
                $('.load-more').show();
                $('.addresses-parent-div').html(response).hide().fadeIn();
            }else{
                $('.addresses-parent-div').append(response).hide().fadeIn();
            }
            //addressArr.push($(this).attr('data-id'));
            //window['address'] = $('.signup-selected-address').attr('data-id');
            $(".address-highlighted-class").trigger('click');
        },
        beforeSend: function () {           
            //disable the submit button
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var responseText = JSON.parse(jqXHR.responseText);
            bootbox.alert({
                title: "Alert!!",
                message: responseText.message,
                callback: function (result) {
                    location.reload();
                },
            });
        }
    });
}