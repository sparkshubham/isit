@extends('backend.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Profile</h1>
    </section>
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
      <div class="col-md-12">

      <div class="box">
            <div class="box-header">
              <!-- <a href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a> -->
              <span class="pull-left btn-back h4"><b>Change Password</b></span>
            </div>
            <!-- /.box-header -->
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
            <!-- form start -->
            {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
            {{ Form::hidden('id',old('id',isset($pageData['id']) ? $pageData['id']: ''),['id'=>'scret_id'])}}
              <div class="box-body">
              <div class="form-group">
                  <label for="category" class="col-sm-2 control-label">{{__('language.change_current_password')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::password('current-password',['maxlength'=>32,'autocomplete'=>'off','class'=>'form-control','id'=>'current-password','placeholder'=>__('language.change_current_password')])}}
                    @if ($errors->has('current-password'))
                      <span class="help-block">{{ $errors->first('current-password') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="title" class="col-sm-2 control-label">{{__('language.change_new_password')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::password('new-password',['maxlength'=>32,'autocomplete'=>'off','class'=>'form-control','id'=>'new-password','placeholder'=>__('language.change_new_password')])}}
                    @if ($errors->has('new-password'))
                      <span class="help-block">{{ $errors->first('new-password') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="description" class="col-sm-2 control-label">{{__('language.change_confirm_password')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::password('new-password-confirm',['maxlength'=>32,'autocomplete'=>'off','class'=>'form-control','id'=>'new-password-confirm','placeholder'=>__('language.change_confirm_password')])}}
                    @if ($errors->has('new-password-confirm'))
                      <span class="help-block">{{ $errors->first('new-password-confirm') }}</span>
                    @endif
                  </div>
                </div>

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="form-group">
                  <div class="col-sm-2">&nbsp;</div>
                  <div class="col-sm-10">
                    {{ Form::submit('Update',['class'=>'btn btn-primary'])}}
                    <!-- {{ Form::reset('Cancel',['class'=>'btn btn-default'])}} -->
                    <a href="{{$backBtnLink}}" class="btn btn-default">Cancel</a>
                  </div>
                </div>
              </div>
              <!-- /.box-footer -->
            {{ Form::close()}}
          </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->

<script type="text/javascript">
$.validator.addMethod('checkPassword', function (value) {
        return /^(?=.*?[0-9])(?=.*?[a-z])(?=.*[A-Z])(?=.*[~!@#$%^&*()_+=-]).{8,15}$/.test(value);
    }, 'Your password must have 8 to 15 characters containing 1 upper case letter, 1 lower case letter, 1 special character & 1 digit.');
$(document).ready(function () {
  $('.form-validate').validate({
      debug: false,
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          'current-password': {
              required: true
          },
          'new-password': {
              required: true,
              minlength:8,
              maxlength:32,
              checkPassword:true
          },
          'new-password-confirm': {
              required: true,
              equalTo: "#new-password"
          }
      },
       messages: {
        'new-password-confirm': {
          equalTo: "The new password and confirm password must match.",
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