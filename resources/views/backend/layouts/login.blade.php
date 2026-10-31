<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ISIT') }}</title>

        <!-- Favicons -->
        <link rel="shortcut icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">
        <link rel="icon" href="{{asset('img/favicon.ico?r=rand()')}}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
        <!-- Styles -->

        {{ Html::style('adminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css') }}
        {{ Html::style('adminLTE/bower_components/font-awesome/css/font-awesome.min.css') }}
        {{ Html::style('adminLTE/bower_components/Ionicons/css/ionicons.min.css') }}
        {{ Html::style('adminLTE/dist/css/AdminLTE.min.css') }}
        {{ Html::style('adminLTE/plugins/iCheck/square/blue.css') }}


          <!-- Custom css for admin -->
          {{ Html::style('css/admin.css') }}
          <!-- Custom responsive css for admin -->
          {{ Html::style('css/admin-responsive.css') }}
          {{ Html::style('adminLTE/dist/css/mystyle.css') }}
        {{ Html::script('adminLTE/bower_components/jquery/dist/jquery.min.js') }}
        <!-- Jquery Validator -->
        {{ Html::script('adminLTE/bower_components/jquery/dist/jquery.validate.min.js') }}

        {{ Html::script('adminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js') }}
        {{ Html::script('adminLTE/plugins/iCheck/icheck.min.js') }}
        <script>
          $(function () {
            $('input').iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%' /* optional */
            });
          });

        $(document).ready(function () {
            setTimeout(function() {
                //$('.alert').remove();
                $('.alert').delay(3000).slideUp(300);
            }, 5000);
        });

        </script>
        <!--begin::Web font -->
        <script>
        </script>

        <!--end::Web font -->
    </head>
    <body class="hold-transition login-page body">
             @yield('content')
    </body>
</html>