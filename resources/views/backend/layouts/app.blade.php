<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ISIT | {{ $title }}</title>

    <!-- Favicons -->
    <link rel="shortcut icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">


       <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
  <!-- Bootstrap 3.3.7 -->
  {{ Html::style('adminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css') }}
  <!-- Font Awesome -->
  {{ Html::style('adminLTE/bower_components/font-awesome/css/font-awesome.min.css') }}
  <!-- Ionicons -->
  {{ Html::style('adminLTE/bower_components/Ionicons/css/ionicons.min.css') }}
    <!-- Select2 -->
  {{ Html::style('adminLTE/bower_components/select2/dist/css/select2.min.css') }}
    <!-- Theme style -->
  {{ Html::style('adminLTE/dist/css/AdminLTE.min.css') }}
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  {{ Html::style('adminLTE/dist/css/skins/_all-skins.min.css') }}
  <!-- DataTables -->
  {{ Html::style('adminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}
  <!-- DataTable Reorder -->
  {{ Html::style('adminLTE/bower_components/datatables.net-bs/css/rowReorder.dataTables.min.css') }}
  <!-- timepicker -->
  {{ Html::style('css/datetimepicker/new/jquery.datetimepicker.css') }}
  {{ Html::style('adminLTE/plugins/timepicker/bootstrap-timepicker.min.css') }}
  <!-- for dialog -->
  {{ Html::style('css/jquery-ui.css') }}

  <!-- for lightbox -->
  {{ Html::style('adminLTE/plugins/lightbox/ekko-lightbox.css') }}

  <!-- Custom css for admin -->
  {{ Html::style('css/admin.css') }}
  <!-- Custom responsive css for admin -->
  {{ Html::style('css/admin-responsive.css') }}



  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- jQuery 3 -->
   {{ Html::script('adminLTE/bower_components/jquery/dist/jquery.min.js') }}
  <!-- Jquery Validator -->
  {{ Html::script('adminLTE/bower_components/jquery/dist/jquery.validate.min.js') }}
  {{ Html::script('adminLTE/bower_components/jquery/dist/additional-methods.min.js') }}
  <!-- Bootstrap 3.3.7 -->
   {{ Html::script('adminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js') }}
  <!-- DataTables -->
   {{ Html::script('adminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/jquery.dataTables.buttons.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/jszip.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/pdfmake.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/vfs_fonts.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/dataTables.rowReorder.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/dataTables.editor.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/dataTables.colReorder.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/buttons.html5.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/dataTables.select.min.js') }}
   {{ Html::script('adminLTE/bower_components/datatables.net/js/buttons.print.min.js') }}
  <!-- SlimScroll -->
   {{ Html::script('adminLTE/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}
  <!-- FastClick -->
   {{ Html::script('adminLTE/bower_components/fastclick/lib/fastclick.js') }}
  <!-- AdminLTE App -->
   {{ Html::script('adminLTE/dist/js/adminlte.min.js') }}
   {{ Html::script('adminLTE/plugins/iCheck/icheck.min.js') }}
  <!-- CK edit -->
  {{ Html::script('adminLTE/bower_components/ckeditor/ckeditor.js') }}
  {{ Html::script('js/common.js') }}
  {{ Html::script('js/jquery.form.min.js') }}

  <!-- timepicker -->
  {{ Html::script('js/datetimepicker/new/php-date-formatter.min.js') }}
  {{ Html::script('js/datetimepicker/new/jquery.mousewheel.js') }}
  {{ Html::script('js/datetimepicker/new/jquery.datetimepicker.js') }}

  <!-- {{ Html::script('js/datetimepicker/bootstrap-datetimepicker.min.js') }} -->
  <!-- timepicker -->
  {{ Html::script('adminLTE/plugins/timepicker/bootstrap-timepicker.min.js') }}

  {{ Html::script('adminLTE/plugins/input-mask/jquery.inputmask.js') }}
  {{ Html::script('adminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js') }}
  {{ Html::script('adminLTE/plugins/input-mask/jquery.inputmask.extensions.js') }}
  {{ Html::script('js/blockui.js') }}
  {{ Html::script('js/jquery-ui.min.js') }}
  <!-- Select2 -->
  {{ Html::script('adminLTE/bower_components/select2/dist/js/select2.full.min.js') }}

  <!-- lodash -->
  {{ Html::script('js/lodash.min.js') }}
  <!-- for lightbox -->
  {{ Html::script('adminLTE/plugins/lightbox/ekko-lightbox.js') }}

<script>
var loader = '<img class="img-fluid" src="{{asset('img/refresh-loader.gif')}}">';
var defaultImageForAdmin =  "{{ asset('admin/upload-default-img.jpg') }}";
  $(document).ready(function () {
    $('.sidebar-menu').tree();
    $('.select2').select2();
    //var x = document.cookie;
    //console.log(x);
    var d = new Date();
    var date_different_minutes = d.getTimezoneOffset();
    document.cookie = "date_different_minutes="+date_different_minutes;

    /*block UI ajax*/
    /*$(document).ajaxStart(function(){
          $.blockUI({
              message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
          });
      });


      $(document).ajaxStop(function(){
          $.unblockUI()
      });*/
  });

function getAdminTime(){
    var d = new Date();
    var h = d.getHours();
    var m = d.getMinutes();
    var s = d.getSeconds();

    if (h < 10) h = '0' + h;
    if (m < 10) m = '0' + m;
    if (s < 10) s = '0' + s;

    return dateString = h + ':' + m + ':' + s;
  }
  function getTimezoneOffset(){
    var d = new Date();

    return d.getTimezoneOffset();
  }
</script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
    @include('backend.layouts.header')
    <!-- Left side column. contains the sidebar -->
    @include('backend.layouts.sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    @yield('content')
    </div>
    @include('backend.layouts.footer')
    </div>
</body>
</html>
