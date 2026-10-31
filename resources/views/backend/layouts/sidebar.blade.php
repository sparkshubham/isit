 <!-- Left side column. contains the sidebar -->
 <?php
 $image_link = Request::root().'/admin';
 ?>
 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="font-size:16px">
      <!-- sidebar menu: : style can be found in sidebar.less --> 
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>

        @if(!Auth::guard('admin_user')->user()->can('dashboard-no-access'))
        <li class="{{ (request()->segment(2) == 'dashboard') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/dashboard') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/dashboard.svg'?>"/><span>Dashboard</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('users-no-access'))
        <li class="{{ (request()->segment(2) == 'users') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/users') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/user-left.svg'?>"/><span>Users</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('cms-page-no-access'))
        <li class="{{ (request()->segment(2) == 'cms-page') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/cms-page') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/cms.svg'?>"/><span>CMS Pages</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'admin-post') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/admin-post') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/daily-devotional.svg'?>"/> <span>Daily Devotional</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'help-user') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/help-user') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/help-user.svg'?>"/> <span>Help Room</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'report-post') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/report-post') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/admin-report.svg'?>"/> <span>Reported Post</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'general-post') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/general-post') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/posts.svg'?>"/> <span>General Posts</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'anointed-post') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/anointed-post') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/posts.svg'?>"/> <span>Anointed Posts</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(2) == 'event') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/event') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/left_event.svg'?>"/> <span>Events</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(3) == 'add-version') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/app-version/add-version') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/version.svg'?>" /><span>App Version</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('category-no-access'))
        <li class="{{ (request()->segment(3) == 'admin-notification') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/admin-notification') }}"><i class="fa fa-bell" aria-hidden="true"></i><span>Sent Notifications </span></a>
        </li>
        @endif
        <!--@if(!Auth::guard('admin_user')->user()->can('cms-page-no-access'))
        <li class="{{ (request()->segment(3) == 'global-setting') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/app-version/global-setting') }}"><img style="margin-right:5px; margin-top:-2px;" src="<?=$image_link.'/settings.svg'?>" /><span>Global Setting</span></a>
        </li>
        @endif
        @if(!Auth::guard('admin_user')->user()->can('cms-page-no-access'))
        <li class="{{ (request()->segment(2) == 'promo-code') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/promo-code') }}"><i class="fa fa-tag"></i> <span>Promo Codes</span></a>
        </li>
        @endif -->


        <!-- @if(!Auth::guard('admin_user')->user()->can('email-template-no-access'))
        <li class="{{ (request()->segment(2) == 'email-templates') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/email-templates') }}"><i class="fa fa-envelope"></i> <span>Email Templates</span></a>
        </li>
        @endif -->

        <!-- @if(!Auth::guard('admin_user')->user()->can('global-setting-no-access'))
        <li class="{{ (request()->segment(2) == 'global-settings') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/global-settings') }}"><i class="fa fa-globe"></i> <span>Global Settings</span></a>
        </li>
        @endif -->

        <!-- @if(!Auth::guard('admin_user')->user()->can('roles-no-access'))
        <li class="{{ (request()->segment(2) == 'roles') ? 'active' : '' }}">
          <a href="{{ url('admin-panel/roles') }}"><i class="fa fa-id-badge"></i> <span>Roles</span></a>
        </li>
        @endif -->

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>