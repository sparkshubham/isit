@extends('backend.layouts.app')
<?php
 $image_link = Request::root().'/admin';
 ?>
@section('content')
    <!-- Content Header (Page header) -->
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
      @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
            @endif
      <!-- /.box -->
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
          <div class="small-box bg-green" style="background-color:#F17E52 !important;border-radius:10px;">
            <div class="inner">
              <p>Total Users</p>
              <h3>{{$getUser}}</h3>
            </div>
            <div class="icon">
            <img style="margin-top:-28px" src="<?=$image_link.'/user.svg'?>"/>
            </div>
            <!-- <a href="{{URL('admin-panel/users')}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a> -->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
            <!-- small box -->
            <div class="small-box bg-green" style="background-color:#807D7D !important; border-radius:10px;">
              <div class="inner">
                <p>Total Events</p>
                <h3>{{$getEvent}}</h3>
                <!-- <h3>{{$getUser}}</h3> -->
              </div>
              <div class="icon" style="margin-top: 10px;">
                <img style="margin-top:-45px;" src="<?=$image_link.'/event.svg'?>"/>
              </div>
              <!-- <a href="{{URL('admin-panel/order')}}" class="small-box-footer">
                  More info <i class="fa fa-arrow-circle-right"></i>
              </a> -->
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
            <!-- small box -->
            <div class="small-box bg-green" style="background-color:#429CD8 !important;border-radius:10px;">
              <div class="inner">
                <p>Total Anointed</p>
                <h3 id="gbp" style="display:block">{{$getAnnoPost}}</h3>
                <!-- <h3>{{$getUser}}</h3> -->
              </div>
              <div class="icon" style="margin-top: 10px;">
                <img style="margin-top:-45px;" src="<?=$image_link.'/church.svg'?>"/>
              </div>
              <!-- <a href="{{URL('admin-panel/order')}}" class="small-box-footer">
                  More info <i class="fa fa-arrow-circle-right"></i>
                </a> -->
            </div>
          </div>
        </div>
        <!-- ./col -->
      </div>
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
<script type="text/javascript">
</script>
@endsection