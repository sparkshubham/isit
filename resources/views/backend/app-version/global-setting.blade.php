@extends('backend.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$title}}</h1>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="row">
      <div class="col-md-12">
      <div class="box">    
            <!-- form start -->
            {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
            @foreach($globalDataIds as $key=>$hField)
              {{ Form::hidden($key,old($key,$hField),['class'=>'scret_id'])}}
            @endforeach
              <div class="box-body">
                <div class="form-group">
                  <label for="shipping_usd" class="col-sm-2 control-label" style="margin-right: 15px;">{{__('language.app_version_global_setting_email')}}</label>
                  <div class="col-field-wrapper col-sm-4 input-group">
                    <!-- <span class="input-group-addon" style="background-color: #F5F5F5;font-weight: bold;">USD</span> -->
                    {{ Form::text('email',old('email',isset($pageData['email']) ? $pageData['email']: ''),["maxlength"=>'255','class'=>'form-control','id'=>'email','placeholder'=>__('language.app_version_global_setting_email')])}}
                    @if ($errors->has('email'))
                      <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
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
            {{ Form::close()}}
          </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->

<script type="text/javascript">
  $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          email: {
              required: true,
              email: true,
              accept:true
          },
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
</script>
@endsection