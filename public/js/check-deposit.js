$(document).ready(function () {
    
    loadCheckDepositTableData(1);
    window['startDate'] = '';
    window['endDate'] = '';
    // manage check deposit paging
    $(document).on('click','.user-arrow-left-check-deposit',function(){
        loadCheckDepositTableData($(this).attr('data-page'),window['startDate'],window['endDate']);
    });

    $(document).on('click','.user-arrow-right-check-deposit',function(){
        loadCheckDepositTableData($(this).attr('data-page'),window['startDate'],window['endDate']);
    });

       
    $(document).on('click','.check-preview',function(){
        var preview = $(this).attr('data-preview');
        $("#check_preview_img").attr('src',$(this).attr('src'));
        $('#checkPreview').modal('show');
    });

    $("#datepicker1").datetimepicker({
      format: "M/D/Y",
      //maxDate: 'now',
      ignoreReadonly: true
        });
        
    $("#datepicker2").datetimepicker({
      format: "M/D/Y",
      //maxDate: 'now',
      ignoreReadonly: true
    });

    /*on click on the icon show calendar*/
    $(document).on('click','.calendar-icon',function(){
        $(this).prev().focus();
    });

    /*clear serch filter and reload data in table*/
    $(document).on('click','.clear',function(){
        $("#datepicker1").val('');
        $('#datepicker2').val('');
        location.reload();
        /*window['startDate'] = '';
        window['endDate'] = '';
        loadCheckDepositTableData(1);*/
    });

    /*filter table data between start date and end date*/
    $(document).on('click','.date-search-filter',function(){
        var startDate = $("#datepicker1").val();
        var endDate = $("#datepicker2").val();
        if(startDate != '' && endDate != ''){
            if(Date.parse(startDate)>Date.parse(endDate)){
                bootbox.alert({
                title: "Alert!!",
                message: "From date can not be greater than to date.",
                callback: function (result) {
                    
                }
            });
            }else{
                window['startDate'] = startDate;
                window['endDate'] = endDate;
                loadCheckDepositTableData(1,startDate,endDate);
            }
        }else{
            bootbox.alert({
                title: "Alert!!",
                message: "Please select from date and to date to search between date.",
                callback: function (result) {
                    
                }
            });
        }
    });
});

function loadCheckDepositTableData(page=1,startDate=null,endDate=null){

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: loadCheckDepositTable,
        dataType: "html",
        //async: false,
        data: {page:page,startDate:startDate,endDate:endDate},
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
                    $('.user-arrow-right-check-deposit').addClass('disable');
                }else{
                    $('.user-arrow-right-check-deposit').removeClass('disable');
                }
                if(response.paging.current_page==1){
                    $('.user-arrow-left-check-deposit').addClass('disable');
                }else{
                    $('.user-arrow-left-check-deposit').removeClass('disable');
                }
                
                $('.manage-check-deposit-table').empty();
                $('.manage-check-deposit-table').html(response.htmlData);
                //$('.manage-user-tooltip-cover').tooltip();
                if(response.paging.total_record > 0){
                    $('.check-deposit-page-number').html(response.paging.from+' - '+response.paging.to+' of '+response.paging.total_record);
                }else{
                    $('.check-deposit-page-number').html(response.paging.total_record+' - '+response.paging.total_record+' of '+response.paging.total_record);
                }
                $('.user-arrow-left-check-deposit').attr('data-page',previousPage)
                $('.user-arrow-right-check-deposit').attr('data-page',nextPage)
                
                if(response.paging.total_record > 0){
                    //$('.check-deposit-page-pagination').show();
                    $('.check-deposit-page-pagination').attr('style','flex !important');
                }else{
                    //$('.check-deposit-page-pagination').hide();
                    $('.check-deposit-page-pagination').attr('style','display:none !important');
                }

                
            }
        },
        beforeSend: function () {
            //disable the submit button

        },
        error: function (jqXHR, textStatus, errorThrown) {
            if(errorThrown=='Unauthorized'){
                location.reload();
            }
        }
    });
    
}
