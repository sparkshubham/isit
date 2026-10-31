@extends('backend.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 style="color:#000000">CMS Pages</h1>
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
              <span class="pull-left btn-back h4"><b>Edit CMS Page</b></span>
              <a href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a>
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
                  <label for="title" class="col-sm-2 control-label">Type</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::text('title',old('title',isset($pageData['title']) ? $pageData['title']: ''),['maxlength'=>255,'class'=>'form-control','disabled'=>'disabled','id'=>'title','placeholder'=>__('language.cms_table_title')])}}
                    @if ($errors->has('title'))
                      <span class="help-block">{{ $errors->first('title') }}</span>
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="description" class="col-sm-2 control-label">Content</label>
                  <div class="col-field-wrapper col-sm-6">
                    {{ Form::textarea('description',old('description',isset($pageData['description']) ? $pageData['description']: ''),['id'=>'editor','class'=>'ckeditor','row'=>'10','cols'=>'80','style'=>'visibility: hidden; display: none;'])}}
                    @if ($errors->has('description'))
                      <span class="help-block">{{ $errors->first('description') }}</span>
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