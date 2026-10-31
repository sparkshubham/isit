@extends('backend.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$title}}</h1>
      <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">Blank page</li>
      </ol> -->
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tbl-users">
                <thead>
                  <th style="display: none;">id</th>
                  <th>{{__('language.s_no')}}</th>
                  <th>{{__('language.user_full_name')}}</th>
                  <th>{{__('language.user_username')}}</th>
                  <th>{{__('language.user_email')}}</th>
                  <th>{{__('language.user_phone_number')}}</th>
                  <th>{{__('language.user_is_star')}}</th>
                  <th>{{__('language.user_last_login')}}</th>
                  <th>{{__('language.user_status')}}</th>
                  <th>{{__('language.user_verified')}}</th>
                  <th>{{__('language.action')}}</th>
                </thead>
            </table>
          </div>
        </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
<!-- Modal start here -->
<div class="modal fade" id="autologin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content model-content-autologin">

    </div>
  </div>
</div>
<!-- Modal end here -->

<script type="text/javascript">
$(document).ready(function () {
  $.fn.dataTable.ext.errMode = () => alert('{{__("messages.datatable-error-message")}}');
  var table = $('#tbl-users').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url : '{{ url('/admin-panel/users/data') }}',
              data: function ( d ) {
                d.date_param = $('#datepickerSearch').val();
                d.time_param = getAdminTime();
                d.timezone_differ = getTimezoneOffset();
              }
            },
            "aaSorting": [[0, "DESC"]],
            // sort:false,
            pageLength: 10,
            columns: [
                {data: 'id', name: 'id',visible: false, searchable: false,orderable:true},
                {"render": function(data, type, full, meta,iDisplayIndex) {
                  return meta.row + 1; // adds id to serial no
                  },orderable:false
                },
                {data: 'name', name: 'name', searchable: true,orderable:true},
                {data: 'username', name: 'username', searchable: true,orderable:true},
                {data: 'email', name: 'email', searchable: true,orderable:true},
                {data: 'phone_number', name: 'phone_number', searchable: true,orderable:false},
                {data: 'is_star', name: 'is_star',searchable: false,orderable:true},
                {data: 'last_login', name: 'last_login',orderable:true},
                {data: 'status', name: 'status',searchable: false,orderable:true},
                {data: 'is_verified', name: 'is_verified',searchable: false,orderable:true},
                {data: 'action', name: 'action',searchable: false,orderable:false},
            ],
            buttons: [
              /* {
                text: 'Add New',
                className: 'btn btn-sm btn-primary add-new-btn',
                action: function ( e, dt, button, config ) {
                  window.location = '{{url("admin-panel/users/add")}}';
                }
              },
              {
                text: 'Reset',
                className: 'btn btn-sm btn-primary add-new-btn',
                action: function ( e, dt, button, config ) {
                  $('#datepickerSearch').datepicker('setDate', null);
                  $('#tbl-users').DataTable().search("").draw();
                  // $('#deposit-check-table').DataTable().ajax.reload();
                }
              } */
            ],
            dom: 'Blfrtip',
        });

      // Grab the datatables input box and alter how it is bound to events
      $(".dataTables_filter input")
          .unbind() // Unbind previous default bindings
          .bind("input", function(e) { // Bind our desired behavior
              // If the length is 3 or more characters, or the user pressed ENTER, search
              //console.log('val->'+$.trim(this.value)+'<-end');
              if($.trim(this.value).length >= 1 || e.keyCode == 13) {
                  // Call the API search function
                  table.search(this.value).draw();
              }
              // Ensure we clear the search if they backspace far enough
              if(this.value == "") {
                  table.search("").draw();
              }
              return;
      });

      /* $('#tbl-users_filter').append('<input type="text" class="form-control input-sm" id="datepickerSearch" readonly/ placeholder="MM/DD/YYYY">');
      $('#datepickerSearch').datepicker({
          autoclose: true,
          timePicker:false,
          format: "m/d/Y",
          //maxDate:0,
          changeMonth: true,
          changeYear: true,
      });

      $("#datepickerSearch").on("focusout", function(e) {
        setTimeout(() => {
          $('#tbl-users').DataTable().ajax.reload();
        }, 200);
      }); */

        $(document).on('change','.testimonial-check',function(){
          var status  = null;
          var id      = $(this).attr('data-id');
            if($(this).prop("checked") == true){
              status = "1";
            }
            else if($(this).prop("checked") == false){
              status = "0";
            }

            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'POST',
              url: '{{$changeStatusURL}}',
              dataType: "json",
              data: {
                id: id,
                status:status
              },
              success: function (response) {
                console.log(response);
              },
              beforeSend: function () {
              }
            });

        });
        $('#tbl-users').on('change','.star-check',function(){
          var status  = null;
          var id      = $(this).attr('data-id');
            if($(this).val() == 1){
              status = 1;
            }
            else if($(this).val() == 0){
              status = 0;
            }
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'POST',
              url: '{{$changeStatusStarURL}}',
              dataType: "json",
              data: {
                id: id,
                status:status
              },
              success: function (response) {
                console.log(response);
              },
              beforeSend: function () {
              }
            });

        });

        $('#tbl-users').on('change','.verify-check',function(){
          var status  = null;
          var id = $(this).attr('data-id');
          var thisObj = $(this);
            if($(this).val() == 1){
              status = 1;
            }
            else if($(this).val() == 0){
              status = 0;
            }
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'POST',
              url: '{{$changeVerifyURL}}',
              dataType: "json",
              data: {
                id: id,
                status:status
              },
              success: function (response) {
                thisObj.attr('disabled','disabled');
                console.log(response);
              },
              beforeSend: function () {
              }
            });

        });

        $(document).on('click','.delete-user-cls',function(){
          let url = $(this).attr('data-url');

          if(($(this).attr('data-card') && $(this).attr('data-type') == 1) || $(this).attr('data-type') == 0){
            $('<div>test</div>').appendTo('body')
            .html('<div><h4>Are you sure to delete the record ?</h4></div>')
            .dialog({
              modal: true,
              title: 'Delete message',
              zIndex: 10000,
              autoOpen: true,
              width: 'auto',
              resizable: false,
              buttons: {
                Yes: function() {
                  $(this).dialog("close");
                  window.location=url;
                },
                No: function() {
                  $(this).dialog("close");
                }
              },
              close: function(event, ui) {
                $(this).remove();
              }
            });
          }else{
            $('<div>test</div>').appendTo('body')
            .html('<div><h4>This user\'s subscription is handled by third party, you won\'t be able to delete this record until the user updates billing details here.</h4></div>')
            .dialog({
              modal: true,
              title: 'Alert',
              zIndex: 10000,
              autoOpen: true,
              width: 'auto',
              resizable: false,
              buttons: {
                Okay: function() {
                  $(this).dialog("close");
                },
              },
              close: function(event, ui) {
                $(this).remove();
              }
            });
          }
        });
    });

</script>
@endsection