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
    @include('backend.layouts.alert')
      <!-- Default box -->
      <div class="row">
      <div class="col-md-12">

      <div class="box">
            <div class="box-header">
              <a href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
            {{ Form::hidden('id',old('id',isset($pageData['id']) ? $pageData['id']: ''),['id'=>'scret_id'])}}
            {{ Form::hidden('required',old('id',isset($pageData['profile_picture']) ? $pageData['profile_picture']: ''),['id'=>'image_required'])}}
            <input type="hidden" name="remove_img" value="0" id="remove_img">
              <div class="box-body">
                
                <div class="form-group">
                  <label for="first_name" class="col-sm-2 control-label">{{__('language.sub_admin_first_name')}}</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::text('first_name',old('name',isset($pageData['first_name']) ? $pageData['first_name']: ''),["maxlength"=>255,'class'=>'form-control','id'=>'first_name','placeholder'=>__('language.sub_admin_first_name')])}}
                    @if ($errors->has('first_name'))
                      <span class="help-block">{{ $errors->first('first_name') }}</span>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">{{__('language.sub_admin_last_name')}}</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::text('last_name',old('name',isset($pageData['last_name']) ? $pageData['last_name']: ''),["maxlength"=>255,'class'=>'form-control','id'=>'last_name','placeholder'=>__('language.sub_admin_last_name')])}}
                    @if ($errors->has('last_name'))
                      <span class="help-block">{{ $errors->first('last_name') }}</span>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">{{__('language.sub_admin_email')}}</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::text('email',old('name',isset($pageData['email']) ? $pageData['email']: ''),['class'=>'form-control nospace','id'=>'email','placeholder'=>__('language.sub_admin_email')])}}
                    @if ($errors->has('email'))
                      <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label for="phone_number" class="col-sm-2 control-label">{{__('language.sub_admin_phone_number')}}</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::text('phone_number',old('name',isset($pageData['phone_number']) ? $pageData['phone_number']: ''),['data-inputmask-clearincomplete'=>"true","minlength"=>10,'class'=>'form-control','id'=>'phone_number','placeholder'=>__('language.sub_admin_phone_number'),'data-inputmask'=>'"mask": "999-999-9999"','data-mask'=>'','autocomplete'=>'off'])}}
                    @if ($errors->has('phone_number'))
                      <span class="help-block">{{ $errors->first('phone_number') }}</span>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">{{__('language.status')}}</label>
                    <div class="col-field-wrapper col-sm-6">  
                    @php $enable = true;$disable = false;@endphp
                    @if(isset($pageData['status']))
                      @if($pageData['status'] == 1)
                        @php $enable = true;$disable = false;@endphp
                      @elseif($pageData['status'] == 0)
                      @php $enable = false;$disable = true;@endphp
                      @endif  
                    @endif
                      <label for="enable" class="radio-inline admin-custom-radio">
                        {{ Form::radio('status','1',$enable,['id'=>'enable'])}}
                        <span class="ac-radio-text">Enable</span>
                        <span class="ac-radio-checkmark"></span>
                      </label>
                      <label for="disable" class="radio-inline admin-custom-radio">
                        {{ Form::radio('status','0',$disable,['id'=>'disable'])}}
                        <span class="ac-radio-text">Disable</span>
                        <span class="ac-radio-checkmark"></span>
                      </label>
                    </div>
                    @if ($errors->has('status'))
                      <span class="help-block">{{ $errors->first('status') }}</span>
                    @endif
                </div>

                <div class="form-group">
                  <label for="image" class="col-sm-2 control-label">{{__('language.testimonial_upload_image')}}</label>

                  <div class="file-upload-col2 col-field-wrapper col-sm-6">
                    <div class="custom-profile-main">
                      
                        <div class="image-file">
                        {{ Form::file('image',['class'=>'form-control','id'=>'img-file-upload'])}}
                        <label class="image-file-label" for="img-file-upload">
                          {{Html::image('admin/upload-default-img.jpg','',['class'=>'file-img-default'])}}
                          @if(isset($pageData->profile_picture))
                            <span class="image-container"><img src="{{ $pageData->image_path }}"></span> 
                            <span class="image-file-upload-again">Change Photo</span>
                          @endif
                        </label>
                        <div title="Remove Image" class="image-file-remove">&times;</div>
                        </div>                        
                        @if ($errors->has('image'))
                        <div class="custom-profile-errors">
                          <span class="help-block">{{ $errors->first('image') }}</span>
                        </div>
                        @endif
                      </div>
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="form-group">
                  <div class="col-sm-2">&nbsp;</div>
                  <div class="col-sm-10">
                    {{ Form::submit($saveBtn,['class'=>'btn btn-primary'])}}
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
$(document).ready(function () {
    //$('.btn-primary').prop("disabled", false);
    $('form').submit(function() {
      $('.btn-primary').prop("disabled", true);
      //$(this).find("button[type='submit']").prop('disabled',true);
    });

    $('.image-file-remove').on('click',function(){
        $('#remove_img').val(1);
    });
  // Input mask for phone number
    $('[data-mask]').inputmask();
    $("#phone_number").inputmask("999-999-9999",{ "clearIncomplete": true });
    if(!$("#image_required").val().length > 0){
      $('.image-file-remove').hide(); 
    }
    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          first_name: {
              normalizer: function( value ) {
                return $.trim( value );
              },
              required: true,
              maxlength: 50,
          },
          last_name: {
              normalizer: function( value ) {
                return $.trim( value );
              },
              required: true,
              maxlength: 50,
          },
          email: {
              required: true,
              email: true,
              remote :
              {
                  url: '{{ url('admin-panel/users/validate-email')}}',
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  type: "post",
                  data:
                  {
                      emails: function()
                      {
                          return $('.form-validate :input[name="email"]').val();
                      },
                      id: function(){
                        return $('.form-validate :input[name="id"]').val();
                      }
                  }
              }
          },
          image: {
            accept:'image/jpg,image/png,image/jpeg'
          }          
      },
      messages: {
        image: {
          accept: "Please select valid image file",
        },
        email: {
          remote: "Email address already exist, please try with another email address",
        },
        first_name: {
          maxlength: 'Entered characters should not be more than 30.',
        },
        last_name: {
          maxlength: 'Entered characters should not be more than 30.',
        }
      },
      errorPlacement: function(error,element){
        if($(element).prop('nodeName') == 'SELECT'){
          error.insertAfter($(element).next('span'));
        }
        else if($(element).prop('type') == 'file'){
          error.insertAfter($(element).parent().parent());
        }
        else{
          error.insertAfter($(element));
        }
      },
      highlight: function (element, errorClass, validClass)
			{
				$(element).parents('.col-field-wrapper').addClass('has-error');
        $('.btn-primary').prop("disabled", false);
			},
			unhighlight: function (element, errorClass, validClass) {
				$(element).parents('.col-field-wrapper').removeClass('has-error');
			},
			invalidHandler: function(form, validator)
			{
				if (!validator.numberOfInvalids())
					return;

				/*$('html, body').animate({
					scrollTop: ($(validator.errorList[0].element).offset().top - 100)
				}, 500);*/
			},
      success: function(label){
       // console.log(label);
          //$('.btn-primary').click(function() {
                //$('.btn-primary').attr('disabled','disabled');
          //});
      },
    });

    
});
</script>
@endsection