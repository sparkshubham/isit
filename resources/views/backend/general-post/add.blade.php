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
            <span class="pull-left btn-back h4"><b>{{$subTitle}}</b></span>
              <a style="margin-top:11px;" href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {{ Form::open(['url'=>$save,'class'=>'form-horizontal form-validate','files'=>true]) }}
            {{ Form::hidden('id',old('id',isset($pageData['id']) ? $pageData['id']: ''),['id'=>'scret_id'])}}
            {{ Form::hidden('post_id',old('id',isset($pageData['id']) ? $pageData['id']: ''),['post_id'=>'post_id'])}}
            {{ Form::hidden('file_required',old('media_file',isset($pageData['file']) ? $pageData['file']: ''),['id'=>'file_required'])}}
            <input type="hidden" name="remove_img" value="0" id="remove_img">
              <div class="box-body">
                <div class="form-group">
                  <label for="description" class="col-sm-2 control-label">{{__('language.daily_devotional_description')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::textarea('description',old('description',isset($pageData['description']) ? $pageData['description']: ''),["maxlength"=>5000,'class'=>'form-control','id'=>'description','placeholder'=>__('language.daily_devotional_description')])}}
                    @if ($errors->has('description'))
                      <span class="help-block">{{ $errors->first('description') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="media_type" class="col-sm-2 control-label">{{__('language.daily_devotional_media_type')}}</label>
                  <div class="col-field-wrapper col-sm-4">
                    {{ Form::select('media_type',$mediaTypeArr,old('media_type',isset($pageData['media_type']) ? $pageData['media_type']: ''),['class'=>'form-control','id'=>'media-type'])}}
                    @if ($errors->has('media_type'))
                      <span class="help-block">{{ $errors->first('media_type') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="image" class="col-sm-2 control-label">{{__('language.daily_devotional_image')}}</label>

                  <div class="file-upload-col2 col-field-wrapper col-sm-6">
                    <div class="custom-profile-main">

                        <div class="image-file">
                        {{ Form::file('file',['class'=>'form-control','id'=>'img-file-upload'])}}
                        <label class="image-file-label" for="img-file-upload">
                          {{Html::image('admin/upload-default-img.jpg','',['class'=>'file-img-default'])}}
                          @if(isset($pageData->getMedia[0]->file_name))
                            <span class="image-container"><img id="image-to-upload" src="{{ $pageData->image_path }}"></span>
                            <span class="image-file-upload-again">Change Media</span>
                          @endif
                        </label>
                        <div title="Remove Image" class="image-file-remove">&times;</div>
                        </div>
                        <div id="file-name" style="max-width:min-content">
                          @if(isset($pageData->getMedia[0]->file_name))
                            <a href="{{$pageData['image_path']}}" target=”_blank”>{{$pageData->getMedia[0]->file_name}}</a>
                          @endif
                        </div>
                        @if ($errors->has('file'))
                        <div class="custom-profile-errors">
                          <span class="help-block" style="clear:both !important;">{{ $errors->first('file') }}</span>
                        </div>
                        @endif
                      </div>
                  </div>

                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">{{__('language.status')}}</label>
                    <div class="col-field-wrapper col-sm-4">
                    @php $enable = true;$disable = false;@endphp
                    @if(isset($pageData['admin_status']))
                      @if($pageData['admin_status'] == 1)
                        @php $enable = true;$disable = false;@endphp
                      @elseif($pageData['admin_status'] == 0)
                      @php $enable = false;$disable = true;@endphp
                      @endif
                    @endif
                      <label class="radio-inline">
                        {{ Form::radio('admin_status','1',$enable,['id'=>'enable'])}}Enable
                      </label>
                      <label class="radio-inline">
                      {{ Form::radio('admin_status','0',$disable,['id'=>'disable'])}}Disable
                      </label>
                    </div>
                    @if ($errors->has('admin_status'))
                      <span class="help-block">{{ $errors->first('admin_status') }}</span>
                    @endif
                </div>

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="form-group">
                  <div class="col-sm-2">&nbsp;</div>
                  <div class="col-sm-10">
                    {{ Form::submit('Submit',['class'=>'btn btn-primary'])}}
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

$('#rating').rating({'showCaption': false,'stars': '5','min': '0','max': '5','step': '1','size': 'md','starCaptions': false,'showClear':false});
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


$('#img-file-upload').on('change', function(event){
    if(event.target.files[0] != undefined && event.target.files[0].name != undefined){
      $('#file-name').html(event.target.files[0].name);
    }else{
      $('#file-name').html('');
    }
    // alert(event.target.files[0].name);
});

function GetTextFromHtml(html) {
    var dv = document.createElement("DIV");
    dv.innerHTML = html;
    return dv.textContent || dv.innerText || "";
}
$(document).ready(function () {
  var oldSrc = '';
  // Input mask for phone number
    if(!$("#file_required").val().length > 0){
      $('.image-file-remove').hide();
    }
    $('.image-file-remove').on('click',function(){
        $('#remove_img').val(1);
        $('#file_required').val('');
        $('#file-name').html('');
    });
    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          description: {
              required: function(element) {
              if($("#file_required").val() == '' && $("#remove_img").val() == 1)
                return true;
              else
                return false;
            },
            minlength: function(element) {
              if($('#descriptipon').val().length !=0){
                return 10;
              }
            }
          },
          media_type : {
              required: true,
              maxlength: 30,
          },
          file: {
            required: function(element) {
              if($("#description").val() == '')
                return true;
              else
                return false;
            },    
            accept:function(element) {
              if($("#media-type").val() == 1)
                return 'image/jpg,image/png,image/jpeg';
              else if($("#media-type").val() == 2)
                return 'video/avi,video/mpeg,video/quicktime,video/mp4,application/octet-stream';
            }
          },
      },
      messages: {
        
        file: {
          accept: "Please select valid file",
        },
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