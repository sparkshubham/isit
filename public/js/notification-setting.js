$(document).ready(function () {
    
    $("#mail_image_exterior").on('change', function() {
        if($(this).is(':checked')){
            if(!$("#new_mail_notification").is(':checked')){
                $("#new_mail_notification").trigger("click");
            }
        }else{
            
        }
    });

    $("#scan_image_exterior").on('change', function() {
        if($(this).is(':checked')){
            if(!$("#scan_request").is(':checked')){
                $("#scan_request").trigger("click");
            }
        }
    });

    $("#scan_attach_pdf").on('change', function() {
        if($(this).is(':checked')){
            if(!$("#scan_request").is(':checked')){
                $("#scan_request").trigger("click");
            }
        }
    });

    /*when off parent option then also off child options */
    $("#new_mail_notification").on('change', function() {
        if(!$(this).is(':checked')){
            if($("#mail_image_exterior").is(':checked')){
                $("#mail_image_exterior").trigger("click");
            }
        }
    });

    $("#scan_request").on('change', function() {
        if(!$(this).is(':checked')){
            if($("#scan_image_exterior").is(':checked')){
                $("#scan_image_exterior").trigger("click");
            }
            if($("#scan_attach_pdf").is(':checked')){
                $("#scan_attach_pdf").trigger("click");
            }
        }
    });

});
