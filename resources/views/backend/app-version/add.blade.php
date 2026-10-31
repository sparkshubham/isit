@extends('backend.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>App Version</h1>
    </section>
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
          <div class="box" style="overflow:hidden;">
              <!-- /.box-header -->
              <!-- form start -->
              {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
              {{ Form::hidden('id',old('id',isset($pageData['id']) ? $pageData['id']: ''),['id'=>'scret_id'])}}
              <div class="row">
                <!-- Android start -->
                <div class="col-md-6">
                      <div class="box-body">
                        <div style="text-align:center;font-size: 16px;font-weight: bold;color: #E85922;margin-top: 10px;margin-bottom: 15px;">Android App</div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_current')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::text('current_version[android]',old('current_version[android]',isset($pageData['current_version']['android']) ? $pageData['current_version']['android']: ''),['maxlength'=>255,'class'=>'form-control','id'=>'and_curr_ver','placeholder'=>__('language.app_version_current')])}}
                            @if ($errors->has('current_version.android'))
                              <span class="help-block">{{ $errors->first('current_version.android') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_min_ver')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                          {{ Form::text('min_version[android]',old('min_version[android]',isset($pageData['min_version']['android']) ? $pageData['min_version']['android']: ''),['maxlength'=>255,'class'=>'form-control','id'=>'and_curr_ver','placeholder'=>__('language.app_version_min_ver')])}}
                            @if ($errors->has('min_version.android'))
                              <span class="help-block">{{ $errors->first('min_version.android') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_force_update')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            @if ($pageData['is_force_update']['android'] == 1)
                            <div class="material-switch pull-center">
                              <input  class="testimonial-check" id="is_force_update[android]" name="is_force_update[android]" type="checkbox" checked />
                              <label for="is_force_update[android]" class="label-success status-lable"></label>
                            </div>
                            @else
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_force_update[android]" name="is_force_update[android]" type="checkbox" />
                              <label for="is_force_update[android]" class="label-success status-lable"></label>
                            </div>
                            @endif
                          </div>
                        </div>
                        <!-- <div class="form-group">
                          <label for="description" class="col-sm-3 control-label">{{__('language.app_version_force_msg')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::textarea('msg_force_update[android]',old('msg_force_update[android]',isset($pageData['msg_force_update']['android']) ? $pageData['msg_force_update']['android']: ''),["maxlength"=>5000,'class'=>'form-control','id'=>'description','placeholder'=>__('language.app_version_force_msg')])}}
                            @if ($errors->has('msg_force_update.android'))
                              <span class="help-block">{{ $errors->first('msg_force_update.android') }}</span>
                            @endif
                          </div>
                        </div> -->
                        <div class="form-group">
                          <label for="description" class="col-sm-3 control-label">{{__('language.app_version_normal_msg')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::textarea('msg_normal_update[android]',old('msg_normal_update[android]',isset($pageData['msg_normal_update']['android']) ? $pageData['msg_normal_update']['android']: ''),["maxlength"=>5000,'class'=>'form-control','id'=>'description','placeholder'=>__('language.app_version_normal_msg')])}}
                            @if ($errors->has('msg_normal_update.android'))
                              <span class="help-block">{{ $errors->first('msg_normal_update.android') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_prompt_update')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            @if ($pageData['is_prompt_update']['android'] == 1)
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_prompt_update[android]" name="is_prompt_update[android]" type="checkbox" checked />
                              <label for="is_prompt_update[android]" class="label-success status-lable"></label>
                            </div>
                            @else
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_prompt_update[android]" name="is_prompt_update[android]" type="checkbox" />
                              <label for="is_prompt_update[android]" class="label-success status-lable"></label>
                            </div>
                            @endif
                          </div>
                        </div>
                      <!-- /.box-body -->
                      <!-- /.box-footer -->
                  </div>
                </div>
                <!-- Android End -->
                <!-- iOS Start -->
                <div class="col-md-6">
                      <div class="box-body">
                        <div style="text-align:center;font-size: 16px;font-weight: bold;color: #E85922;margin-top: 10px;margin-bottom: 15px;">iOS App</div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_current')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::text('current_version[ios]',old('current_version[ios]',isset($pageData['current_version']['ios']) ? $pageData['current_version']['ios']: ''),['maxlength'=>255,'class'=>'form-control','id'=>'and_curr_ver','placeholder'=>__('language.app_version_current')])}}
                            @if ($errors->has('current_version.ios'))
                              <span class="help-block">{{ $errors->first('current_version.ios') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_min_ver')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                          {{ Form::text('min_version[ios]',old('min_version[ios]',isset($pageData['min_version']['ios']) ? $pageData['min_version']['ios']: ''),['maxlength'=>255,'class'=>'form-control','id'=>'and_curr_ver','placeholder'=>__('language.app_version_min_ver')])}}
                            @if ($errors->has('min_version.ios'))
                              <span class="help-block">{{ $errors->first('min_version.ios') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_force_update')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            @if ($pageData['is_force_update']['ios'] == 1)
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_force_update[ios]" name="is_force_update[ios]" type="checkbox" checked />
                              <label for="is_force_update[ios]" class="label-success status-lable"></label>
                            </div>
                            @else
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_force_update[ios]" name="is_force_update[ios]" type="checkbox" />
                              <label for="is_force_update[ios]" class="label-success status-lable"></label>
                            </div>
                            @endif
                          </div>
                        </div>
                        <!-- <div class="form-group">
                          <label for="description" class="col-sm-3 control-label">{{__('language.app_version_force_msg')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::textarea('msg_force_update[ios]',old('msg_force_update[ios]',isset($pageData['msg_force_update']['ios']) ? $pageData['msg_force_update']['ios']: ''),["maxlength"=>5000,'class'=>'form-control','id'=>'description','placeholder'=>__('language.app_version_force_msg')])}}
                            @if ($errors->has('msg_force_update.ios'))
                              <span class="help-block">{{ $errors->first('msg_force_update.ios') }}</span>
                            @endif
                          </div>
                        </div> -->
                        <div class="form-group">
                          <label for="description" class="col-sm-3 control-label">{{__('language.app_version_normal_msg')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            {{ Form::textarea('msg_normal_update[ios]',old('msg_normal_update[ios]',isset($pageData['msg_normal_update']['ios']) ? $pageData['msg_normal_update']['ios']: ''),["maxlength"=>5000,'class'=>'form-control','id'=>'description','placeholder'=>__('language.app_version_normal_msg')])}}
                            @if ($errors->has('msg_normal_update.ios'))
                              <span class="help-block">{{ $errors->first('msg_normal_update.ios') }}</span>
                            @endif
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="title" class="col-sm-3 control-label">{{__('language.app_version_prompt_update')}}</label>
                          <div class="col-field-wrapper col-sm-8">
                            @if ($pageData['is_prompt_update']['ios'] == 1)
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_prompt_update[ios]" name="is_prompt_update[ios]" type="checkbox" checked />
                              <label for="is_prompt_update[ios]" class="label-success status-lable"></label>
                            </div>
                            @else
                            <div class="material-switch pull-center">
                              <input class="testimonial-check" id="is_prompt_update[ios]" name="is_prompt_update[ios]" type="checkbox" />
                              <label for="is_prompt_update[ios]" class="label-success status-lable"></label>
                            </div>
                            @endif
                          </div>
                        </div>
                      <!-- /.box-body -->
                      <!-- /.box-footer -->
                  </div>
                </div>
                <!-- iOS End -->
                <div class="box-footer">
                        <div class="form-group">
                          <div class="col-sm-12" style="text-align:center;">
                          <hr style="border:1px solid #686868" />
                            {{ Form::submit($saveBtn,['class'=>'btn btn-primary'])}}
                            <!-- {{ Form::reset('Cancel',['class'=>'btn btn-default'])}} -->
                            <a href="{{$backBtnLink}}" class="btn btn-default">Cancel</a>
                          </div>
                        </div>
                      </div>
                {{ Form::close()}}
            </div>
        </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->

<script type="text/javascript">
$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
      }
  });
// Create a classic editor using inline configuration
var uploadUrl = '{{ url("admin-panel/cms-page/upload-image") }}';

var config = {
    customConfig : '',
    // Add the required plugin
    //extraPlugins : 'simpleuploads',
    // Required config to tell CKEditor what's the script that will process uploads
    filebrowserUploadUrl : uploadUrl, //+ '&type=Files',
    //filebrowserImageUploadUrl : uploadUrl + '&type=Images',
    toolbar : // Sample toolbar
    [
        { name: 'document',    items : [ 'Source' ] },
        { name: 'clipboard',   items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
        { name: 'basicstyles', items : [ 'Bold','Italic' ] },
        { name: 'insert',      items : [ 'Link', 'Image', 'addFile', 'addImage' ] },
        { name: 'tools',       items : [ 'Maximize' ] }
    ],
    allowedContent: true,
    isEmptyInlineRemoveable:false,
    // Define the file extensions (whitelist) that are allowed to upload.
    // This is a client side check. You must do the same validation on your server.
    // By default CKEditor doesn't implement this kind of check, so users can waste time and bandwith uploading files only to get an error message later
    // So setting this correctly can help your users greatly
    simpleuploads_acceptedExtensions : '7z|avi|csv|doc|docx|flv|gif|gz|gzip|jpeg|jpg|mov|mp3|mp4|mpc|mpeg|mpg|ods|odt|pdf|png|ppt|pxd|rar|rtf|tar|tgz|txt|vsd|wav|wma|wmv|xls|xml|zip'
};



jQuery.validator.addMethod("ckrequired", function (value, element, params) {

  var idname = $(element).attr('id');
  var editor = CKEDITOR.instances[idname];
  var ckValue = GetTextFromHtml(editor.getData()).replace(/<[^>]*>/gi, '').trim();
  if (ckValue.length === 0) {
  //if empty or trimmed value then remove extra spacing to current control
        $(element).val(ckValue);
    } else {
  //If not empty then leave the value as it is
        $(element).val(editor.getData());
    }
    return $(element).val().length > 0;
}, jQuery.validator.format("This field is required"));
@if(isset($pageData['description']))
CKEDITOR.replace( 'description', config );
@endif
$.each(CKEDITOR.dtd.$removeEmpty, function (i, value) {
  CKEDITOR.dtd.$removeEmpty[i] = false;
  CKEDITOR.dtd.a.button = 1;
});

$(document).ready(function () {
    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          title: {
              required: true,
              normalizer: function( value ) {
                  return $.trim( value );
              },
          },
          description: {
              required: true,
              normalizer: function( value ) {
                  return $.trim( value );
              },
          },
      },
      errorPlacement: function(error,element){
        if($(element).prop('nodeName') == 'TEXTAREA' && $(element).hasClass('ckeditor')){
          error.insertAfter($(element).next('div'));
        }
        else{
          error.insertAfter($(element));
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