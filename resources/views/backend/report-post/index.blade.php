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
                <th>{{__('language.report_post_category')}}</th>
                <th>{{__('language.report_post_description')}}</th>
                <th>{{__('language.report_post_username')}}</th>
                <th>{{__('language.report_post_ori_title')}}</th>
                <!-- <th>{{__('language.report_post_ori_description')}}</th> -->
                <th>{{__('language.report_post_type')}}</th>
                <th>{{__('language.report_post_status')}}</th>
                <th>{{__('language.report_post_created')}}</th>
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
            ajax: '{{ url('/admin-panel/report-post/data') }}',
            "aaSorting": [[0, "DESC"]],
            pageLength: 10,
            columns: [
                /*{data: 'key', name: 'key'},*/
                {data: 'id', name: 'id',visible: false,searchable: false},
                {"render": function(data, type, full, meta,iDisplayIndex) {
                  return meta.row + 1; // adds id to serial no
                  },orderable:false
                },
                {data: 'category', name: 'category'},
                {data: 'description', name: 'description',searchable: true,orderable:true},
                {data: 'username', name: 'username',searchable: true,orderable:true},
                {data: 'post_title', name: 'post_title',searchable: true,orderable:true},
                // {data: 'post_description', name: 'post_description',searchable: true,orderable:true},
                {data: 'post_type', name: 'post_type',searchable: true,orderable:true},
                {data: 'admin_status', name: 'admin_status',searchable: false,orderable:true},
                {data: 'created_at', name: 'created_at',searchable: false,orderable:true},
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
                  window.location = '{{url("admin-panel/report-post/add")}}';
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

        $(document).on('change','.testimonial-check',function(){
          var status  = null;
          var id      = $(this).attr('data-id');
          var post_id = $(this).attr('post-id');
            if($(this).prop("checked") == true){
              status = 1;
            }
            else if($(this).prop("checked") == false){
              status = 0;
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
                status:status,
                post_id:post_id
              },
              success: function (response) {
                console.log(response);
              },
              beforeSend: function () {
              }
            });

        });

    });
</script>
@endsection