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
            <table class="table table-bordered table-hover" id="tbl-career">
              <thead>
                <!-- <th>{{__('language.cms_page_name')}}</th> -->
                <th style="display: none;">id</th>
                <th>{{__('language.s_no')}}</th>
                <th>{{__('language.event_name')}}</th>
                <th>{{__('language.event_username')}}</th>
                <th>{{__('language.event_date')}}</th>
                <th>{{__('language.event_start_time')}}</th>
                <th>{{__('language.event_end_time')}}</th>
                <th>{{__('language.event_timezone')}}</th>
                <th>Action</th>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->

<script type="text/javascript">
$(document).ready(function () {
  $.fn.dataTable.ext.errMode = () => alert('{{__("messages.datatable-error-message")}}');
        var table = $('#tbl-career').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('/admin-panel/event/data') }}',
            "aaSorting": [[0, "DESC"]],
            pageLength: 10,
            columns: [
                /*{data: 'key', name: 'key'},*/
                {data: 'id', name: 'id',visible: false,searchable: false},
                {"render": function(data, type, full, meta,iDisplayIndex) {
                  return meta.row + 1; // adds id to serial no
                  },orderable:false
                },
                {data: 'name', name: 'name',searchable: true,orderable:true},
                {data: 'username', name: 'username',searchable: true,orderable:true},
                {data: 'event_date', name: 'event_date',searchable: false,orderable:true},
                {data: 'start_time', name: 'start_time',searchable: false,orderable:true},
                {data: 'end_time', name: 'end_time',searchable: false,orderable:true},
                {data: 'timezone', name: 'timezone',searchable: true,orderable:true},
                {data: 'action', name: 'action',searchable: false,orderable:false},
            ],
            columnDefs: [
              { "width": "40%", "targets": 0 },
            ],
            buttons: [
              {
                text: 'Add New',
                className: 'btn btn-sm btn-primary add-new-btn',
                action: function ( e, dt, button, config ) {
                  window.location = '{{url("admin-panel/event/add")}}';
                }
              }
            ],
            dom: 'lfrtip',
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

        $(document).on('click','.testimonial-check',function(){
          var status  = null;
          var id = $(this).attr('data-id');
          var statusValue = $(this).attr('admin-status');
          var thisAttr = $(this);
          // console.log(thisAttr.parent().html('sdfsfsdf'));
          var str = "Accept";
          if(statusValue == 2){
            str = "Decline";
          }
          if(confirm("Are you sure you want to "+str+" this event?")){
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'POST',
              url: '{{$changeStatusURL}}',
              dataType: "json",
              data: {
                id: id,
                status:statusValue
              },
              success: function (response) {
                if(response.status == 2){
                  alert("This Event can't accept due to same time of accepted other event.");
                  return false
                }else if(response.status == 1){
                  if(statusValue == 1){
                    thisAttr.parent().html('<button class="dt-button btn btn-sm accept-btn block" tabindex="0" aria-controls="tbl-career" type="button"><span>Accepted</span></button>');
                  }else{
                    thisAttr.parent().html('<button class="dt-button btn btn-sm decline-btn block" tabindex="0" aria-controls="tbl-career" type="button"><span>Declined</span></button>');
                  }
                }else{
                  alert("Something went wrong.");
                  return false
                }
                console.log(response);
              },
              beforeSend: function () {
              }
            });
          }else{
            return false;
          }

        });

    });
</script>
@endsection