@extends('backend.layouts.app')

@section('content')
{{ Html::style('adminLTE/plugins/bootstrap-rating-star/star-rating.min.css') }}
{{ Html::style('adminLTE/plugins/image-preview/UnoDropZone.css') }}
{{ Html::script('adminLTE/plugins/bootstrap-rating-star/star-rating.min.js') }}
{{ Html::script('adminLTE/plugins/image-preview/UnoDropZone.js') }}
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Profile</h1>
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
              <!-- <a href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a> -->
              <span class="pull-left btn-back h4"><b>Edit Profile</b></span>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
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
            {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
            {{ Form::hidden('required',old('id',isset($pageData['profile_picture']) ? $pageData['profile_picture']: ''),['id'=>'image_required'])}}
              <input type="hidden" name="remove_img" value="0" id="remove_img">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">{{__('language.change_profile_image')}}</label>
                  <div class="file-upload-col col-field-wrapper col-sm-4">
                    <div class="custom-profile-main">
                      <div class="image-file" >
                        {{ Form::file('profile_picture',['class'=>'form-control','id'=>'img-file-upload'])}}
                        <label class="image-file-label" for="img-file-upload" style="border-radius:15px;">
                          {{Html::image('admin/upload-default-img.jpg','',['class'=>'file-img-default'])}}
                          @if(isset($pageData['image_path']) && file_exists($pageData['image_path_exist']))
                            <span class="image-container"><img src="{{ $pageData['image_path'] }}"></span>
                            <span class="image-file-upload-again">Change Photo</span>
                          @endif
                        </label>
                      </div>
                      <div title="Remove Image" class="image-file-remove">&times;</div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="first_name" class="col-sm-2 control-label">{{__('language.change_profile_first_name')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::text('first_name',old('first_name',isset($pageData['first_name']) ? $pageData['first_name']: ''),['maxlength'=>50,'class'=>'form-control','id'=>'name','placeholder'=>__('language.change_profile_first_name')])}}
                    @if ($errors->has('first_name'))
                      <span class="help-block">{{ $errors->first('first_name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">{{__('language.change_profile_email')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::email('email',old('email',isset($pageData['email']) ? $pageData['email']: ''),['class'=>'form-control','id'=>'profile_email','placeholder'=>__('language.change_profile_email'),'autocomplete'=>'off','disabled'])}}
                    @if ($errors->has('email'))
                      <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">&nbsp;</label>
                  <div class="col-field-wrapper col-sm-4">
                  <a href="change-password" style="font-size: 16px;padding-top: 10px !important;" class="action-control chnage-password" title="Change Password">Change Password</a>
                  </div>
                </div>
              </div>
              <div class="box-footer">
                <div class="form-group">
                  <div class="col-sm-2">&nbsp;</div>
                  <div class="col-sm-10">
                    {{ Form::submit($saveBtn,['class'=>'btn btn-primary'])}}
                    <!-- {{ Form::reset('Cancel',['class'=>'btn btn-default'])}} -->
                    <a href="{{$backBtnLink}}" class="btn btn-default">Cancel</a>
                  </div>
                </div>
              </div>
              <!-- /.box-footer -->
        </div>
      </div>
    </div>
    <!-- /.box-body -->
    {{ Form::close()}}
    </div>
  </div>
  <!-- /.box -->
</section>
    <!-- /.content -->

<script type="text/javascript">
$(document).ready(function () {
  $('[data-mask]').inputmask();
  if(!$("#image_required").val().length > 0){
    $('.image-file-remove').hide();
  }
    $('.image-file-remove').on('click',function(){
        $('#remove_img').val(1);
    });

    jQuery.validator.addMethod("emailExt", function(value, element, param) {
    return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
},'The email must be a valid email address.');

    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          first_name: {
              required: true,
          },
          last_name: {
              required: true,
          },
          phone_number: {
            required: true,
          },
          email:{
            required: true,
            emailExt: true,
            //accept:"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}"
          },
          profile_picture: {
            required: function (element) {
              return false;
              /*if($("#scret_id").val().length > 0){
                return false;
              } else {
                  return true;
              }*/
            },
            accept:'image/jpg,image/png,image/jpeg,image/gif'
          }
      },
      messages: {
        profile_picture: {
          accept: "Please select valid image file",
        }
      },
      highlight: function (element, errorClass, validClass)
      {
        $(element).parents('.col-field-wrapper').addClass('has-error');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).parents('.col-field-wrapper').removeClass('has-error');
      },
      invalidHandler: function(form, validator)
      {
        if (!validator.numberOfInvalids())
          return;

        $('html, body').animate({
          scrollTop: ($(validator.errorList[0].element).offset().top - 100)
        }, 500);
      }
    });


});
</script>
@endsection