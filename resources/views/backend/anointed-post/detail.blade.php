@extends('backend.layouts.app')

@section('content')
{{ Html::style('adminLTE/plugins/bootstrap-rating-star/star-rating.min.css') }}
{{ Html::style('adminLTE/plugins/image-preview/UnoDropZone.css') }}
{{ Html::script('adminLTE/plugins/bootstrap-rating-star/star-rating.min.js') }}
{{ Html::script('adminLTE/plugins/image-preview/UnoDropZone.js') }}
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Posts - Anointed</h1>
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
              <span class="pull-left btn-back h4"><b>View Anointed Post</b></span>
              <a style="margin-top:11px;" href="{{$backBtnLink}}" class="pull-right btn-back"><i class="glyphicon glyphicon-menu-left"></i> <span class="h4">Back</span></a>
            </div>
            <!-- /.box-header -->
            <!-- box-body -->
            <div class="box-body boxBodyTopSection">
              <div class="row">
                <div class="col-md-9 col-lg-10">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_username')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->getUser->first_name.' '.$pageData->getUser->last_name}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_created')}}</div>
                        <div class="boxBodyTopGroupValue">{{ date('d/m/Y',$pageData->created_at);}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_category')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->Category->name}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_country')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->Country->name}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_city')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->city}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_zip_code')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->zip_code}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_description')}}</div>
                        <div class="boxBodyTopGroupValue text-left" style="width:80%; text-align:left !important;">{!! $pageData->description !!}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.status')}}</div>
                        <div class="boxBodyTopGroupValue">{{ $pageData->admin_status==1?'Enable':'Disable'}}</div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="boxBodyTopGroup">
                        <div class="boxBodyTopGroupTitle">{{__('language.anointed_media_file')}}</div>
                        <div class="boxBodyTopGroupValue text-left" style="width:80%; text-align:left !important;">
                        @if(isset($pageData->getMedia) && $pageData->getMedia->count() != 0)
                          @foreach($pageData->getMedia as $images)
                            @if($images->media_type == 1)
                              <a href="{{$pageData->displayImagePath.$images->file_name}}" target=”_blank” title="Image">
                                <img style="box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);border: 2px solid #F17E52;padding: 5px 5px 5px 5px;" width="175" title="Image" class="bd-placeholder-img"src="{{$pageData->displayImagePath.$images->file_name}}">
                              </a>
                            @else
                              <a href="{{$pageData->displayImagePath.$images->file_name}}" target=”_blank” title="Video">
                                <img style="box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);border: 2px solid #F17E52;padding: 5px 5px 5px 5px;" width="175" title="Video" class="bd-placeholder-img"src="{{$pageData->displayImagePath.$images->thumb_image_name}}">
                            </a>
                            @endif
                            @endforeach
                        @else
                          <div>No media Uploaded yet.</div>
                        @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
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

function GetTextFromHtml(html) {
    var dv = document.createElement("DIV");
    dv.innerHTML = html;
    return dv.textContent || dv.innerText || "";
}
$(document).ready(function () {
    $('.form-validate').validate({
      debug: false,
      ignore : [],
      errorClass: 'help-block',
      errorElement: "span",
      rules: {
          name: {
              required: true,
              maxlength: 50
          },
          testimonial: {
            required: true,
            maxlength: 500
          },
          image: {
            required: function (element) {
              if($("#scret_id").val().length > 0){
                return false;
              } else {
                  return true;
              }
            },
            accept:'image/jpg,image/png,image/jpeg,image/gif'
          },
          rating: {
              required: true
          }
      },
      messages: {
        name: {
          maxlength: "Name should not be more than 50 characters",
        },
        image: {
          accept: "Please select valid image file",
        },
        testimonial: {
          maxlength: "Testimonial should not be more than 500 characters",
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