$(document).ready(function () {
    
    loadShipmentTableData(1);
    
    // manage user paging
    $(document).on('click','.user-arrow-left-shipment',function(){
        loadShipmentTableData($(this).attr('data-page'));
    });

    $(document).on('click','.user-arrow-right-shipment',function(){
        loadShipmentTableData($(this).attr('data-page'));
    });

    $(document).on('click','.search-manage-shipment',function(){
        var searchKeyword = $('#filter-shipments').val();
        loadShipmentTableData(1,searchKeyword);
    });

    $('#filter-shipments').keyup(function(e){
        //if($.trim(this.value).length >= 3 || e.keyCode == 13) {
        if (((e.keyCode == 8 || event.keyCode == 46) && ($.trim(this.value).length == 0) || $.trim(this.value).length >= 3) || e.keyCode == 13) {
            var searchKeyword = $(this).val();
            loadShipmentTableData(1,searchKeyword); 
        }
    });

    
});

function loadShipmentTableData(page=1,searchKeyword=null){

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: loadShipmentsTable,
        dataType: "html",
        //async: false,
        data: {page:page,searchKeyword:searchKeyword},
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
                    $('.user-arrow-right-shipment').addClass('disable');
                }else{
                    $('.user-arrow-right-shipment').removeClass('disable');
                }
                if(response.paging.current_page==1){
                    $('.user-arrow-left-shipment').addClass('disable');
                }else{
                    $('.user-arrow-left-shipment').removeClass('disable');
                }
                
                $('.manage-shipment-table').empty();
                $('.manage-shipment-table').html(response.htmlData);
                //$('.manage-user-tooltip-cover').tooltip();
                if(response.paging.total_record > 0){
                    $('.manageshipment-page-number').html(response.paging.from+' - '+response.paging.to+' of '+response.paging.total_record);
                }else{
                    $('.manageshipment-page-number').html(response.paging.total_record+' - '+response.paging.total_record+' of '+response.paging.total_record);
                }
                $('.user-arrow-left-shipment').attr('data-page',previousPage)
                $('.user-arrow-right-shipment').attr('data-page',nextPage)
                
                if(response.paging.total_record > 0){
                    $('.manageshipment-page-pagination').show();
                }else{
                    $('.manageshipment-page-pagination').hide();
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
