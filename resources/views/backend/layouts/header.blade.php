<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('admin-panel/dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">{{Html::image('admin/logo_icon.png','',['class'=>'img-responsive'])}}</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">{{Html::image('admin/logo-lg.svg','',['class'=>'img-responsive','style'=>'width: 110px;padding-top: 10px;'])}}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              @if(Auth::guard('admin_user')->user()->profile_picture && file_exists(config('constants.admin_image.display_path').Auth::guard('admin_user')->user()->profile_picture))
                  @php $imagePath = config('constants.admin_image.display_path');@endphp
                  @php $image = Auth::guard('admin_user')->user()->profile_picture; @endphp
                  {{ Html::image($imagePath.$image,'Image',['class'=>'user-image'])}}
                @else
                {{ Html::image('img/dummy_avatar_admin.jpg','Image',['class'=>'user-image'])}}
                @endif
              <span class="hidden-xs">{{Auth::guard('admin_user')->user()->first_name}}<i class="fa fa-caret-down" aria-hidden="true"></i></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                @if(Auth::guard('admin_user')->user()->profile_picture && file_exists(config('constants.admin_image.display_path').Auth::guard('admin_user')->user()->profile_picture))
                  @php $imagePath = config('constants.admin_image.display_path');@endphp
                  @php $image = Auth::guard('admin_user')->user()->profile_picture; @endphp
                  {{ Html::image($imagePath.$image,'Image',['class'=>'img-circle'])}}
                @else
                  {{ Html::image('img/dummy_avatar_admin.jpg','Image',['class'=>'img-circle'])}}
                @endif
                <p>{{Auth::guard('admin_user')->user()->first_name}}</p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="user-footer-links">
                  <a href="{{ url('admin-panel/change-profile/') }}" class="btn btn-primary btn-block">Profile</a>
                </div>
                <!-- <div class="user-footer-links">
                  <a href="{{ url('admin-panel/change-password') }}" class="btn btn-primary btn-block">Change Password</a>
                </div> -->
                <div class="user-footer-links">
                  <a href="{{url('admin-panel/logout')}}" class="btn btn-primary btn-block">Logout</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>