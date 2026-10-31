@extends('backend.layouts.app')

@section('content')
{{ Html::style('adminLTE/plugins/bootstrap-rating-star/star-rating.min.css') }}
{{ Html::style('adminLTE/plugins/image-preview/UnoDropZone.css') }}
{{ Html::script('adminLTE/plugins/bootstrap-rating-star/star-rating.min.js') }}
{{ Html::script('adminLTE/plugins/image-preview/UnoDropZone.js') }}
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
      <div class="row">
      <div class="col-md-12">

      <div class="box">
            <div class="box-header">
              <span class="pull-left btn-back h4"><b>User Detail</b></span>
              <a style="margin-top:11px;" href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a>
            </div>
            <!-- /.box-header -->
            <!-- box-body -->
            <div class="box-body boxBodyTopSection">
              <div class="row">
                <div class="col-md-3 col-lg-2">
                
                  <div class="boxBodyTopProfile">
                    @if($pageData->profile_picture)
                    <img src="{{ asset($pageData['image_path'])}}" alt="Profile Image" class="img-responsive">
                    @else
                    <img src="{{ asset('img/dummy_avatar_admin.jpg')}}" alt="Profile Image" class="img-responsive">
                    @endif                  
                  </div>
                </div>
                <div class="col-md-9 col-lg-10">
                  <div class="row2">
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_full_name')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ ($pageData->first_name!=''||$pageData->last_name!='')?($pageData->first_name.' '.$pageData->last_name):'N/A'}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_username')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ ($pageData->username!=NULL)?($pageData->username):'-'}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_email')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ $pageData->email}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_phone_number')}}</div>          
                          <div class="boxBodyTopGroupValue">{{ $pageData->phone_number!='' ? ($pageData->country_code!='') ? $pageData->country_code.'-'.$pageData->phone_number : $pageData->phone_number :'N/A'}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_status')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ $pageData->status==1?'Enable':'Disable'}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_last_login')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ ($pageData->last_login!=NULL) ? date('d/m/Y',strtotime($pageData->last_login)) : '-'}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_bio_text')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ ($pageData->bio_text == NULL || $pageData->bio_text == '') ? '-' : $pageData->bio_text}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-10">
                        <div class="boxBodyTopGroup">
                          <div class="boxBodyTopGroupTitle">{{__('language.user_public_visibility')}}</div>                          
                          <div class="boxBodyTopGroupValue">{{ ($pageData->is_public_visibility == 1 ? 'ON' : 'OFF')}}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
        <!-- User Notes -->
      </div>
      <!-- /.Document Detail section -->
      </div>
    </section>
    <!-- /.content -->

<script type="text/javascript">
</script>
@endsection