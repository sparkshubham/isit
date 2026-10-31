$(document).ready(function () {

  // Prevent Space
  $(".prevent-space").keypress(function (evt) {
    var keycode = evt.charCode || evt.keyCode;
    if (keycode  == 32) { 
      return false;
    }
  });

  // Auto hide alert (flase) messages
  setTimeout(function() {
    // $('.').delay(4000).slideUp(300);
    $('.alert').delay(4000).slideUp("normal", function() { $('.field_message_alert').remove(); } );

  }, 5000);

  /*$(document).ajaxStart(function(){
    $.blockUI({
        message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
    });
  });


  $(document).ajaxStop(function(){
      $.unblockUI()
  });*/

/*Dropup jQuery*/
$(document).on('click','.dropdown-toggle-click',function(){
  var optional_text = $(this).data('optional_text');
  if($('#showeppo').attr('active') == 1){
    $('#showeppo').hide();
    $('#showeppo').attr('active',0);
  }else{
    $('#showeppo').show();
    $('#showeppo').attr('active',1);
    var document_id = $(this).attr('document_id');
    
    $('#showeppo').find('ul').find('li').each(function(i)
    {
      var newUrl = '';
      if($(this).find('a').attr('data-url')){
        newUrl = $(this).find('a').attr('data-url')+'/'+document_id;
        newUrl = newUrl.replace(/^#/, '');
        $(this).find('a').attr('href',newUrl);
      }
    });

  }  
  var x = $(this).offset().left;
  var y = $(this).offset().top;
  
  $("#dropDownPos" ).css({
    'left': x,
    'top': y
  });
});

/* lightbox fallery */
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox({
      alwaysShowClose: true  
    });
});


$('body').click(function(e) {
  if (!$(event.target).closest('.dropdown-toggle-click').length) {
    $('#showeppo').hide();
    $('#showeppo').attr('active',0);
  }
});

$('.nospace').keydown(function(e) {
  if (e.keyCode == 32) {
      return false;
  }
});

$('.image-file-remove').on('click',function(){
  $('#img-file-upload').val('');
  $('.image-file-upload-again').remove();
  $('#image_required').val('');
  $('.image-file-remove').hide();
  $('.image-container').find('img').attr('src', defaultImageForAdmin);
});

    $('input:file').each(function(index, element)
	  {
		  $(document).on('change', $(element), function(event)
		  {
			  if(typeof $(event.target).attr('prevue') === 'undefined')
			  {
				  readURL(event.target);
			  }
		  });
    });

    $('.help-block').each(function(index, element){
      $(element).parents('.col-field-wrapper').addClass('has-error');
    });
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('editor1', {
      // Define the toolbar groups as it is a more accessible solution.
      toolbarGroups: [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert','table' ] },
        '/',
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
      ],
      // Remove the redundant buttons from toolbar groups defined above.
	  removeButtons: 'ShowBlocks,TextColor,BGColor,Save,NewPage,Preview,Print,Templates,PasteText,PasteFromWord,Cut,Copy,Paste,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,Strike,RemoveFormat,CopyFormatting,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize',
	  allowedContent: true,
    isEmptyInlineRemoveable:false
    })
});
$.each(CKEDITOR.dtd.$removeEmpty, function (i, value) {
  CKEDITOR.dtd.$removeEmpty[i] = false;
});
var fileTypes = ['jpg', 'jpeg', 'png', 'gif','mp4','ogx','oga','ogv','ogg','webm','mov'];  //acceptable file types

function readURL(input) 
{
	if (input.files && input.files[0]) 
	{
    var extension = input.files[0].name.split('.').pop().toLowerCase(), isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
    // alert(extension);
    var videoImgSrc = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARMAAAC3CAMAAAAGjUrGAAAAQlBMVEXu7vSUlZnz8/mNjpKlpqrm5uyRkpanqKyWl5v09Prr6/GOkJPCwsfi4uejo6igoaXZ2d6vsLW5ub7Q0dbKys+bm6Dr7/6fAAABv0lEQVR4nO3cW0/qQBSAUToj5SZCAf//Xz3c9BR3Y4xxSpuu9dgHduZLnXDpOJsBAAAAAAAAAAAAAAAAAADwh9JsXsL5dUdr3WxyCfVhtFHWy7oqY7MYaZR0KJWkqvLx2av7nbQslqSqm3HeKKncbVLVI/3j0STSJOq/yfA79d0kHV+3Q6/Sb5PV8T3Xef+Ulf5cn03S9i13phqY/pqkeZOvw+o3TW5zZq/5PkqT+5hT9TlIk4vV/n3TuqzJeWt9yQ+XJ98krZv8OGPqTVpbqya3JqvW1qrJ1W6XO65Ou0n391WaaKKJJppookmLJpEmkSaRJpEmkSaRJpEmkSaRJpEmkSaRJpEmkSaRJpEmUdkmnS8+7SZvjd8Bvy5+sdp3/Do68SZplo7LzdfLU29yeZBt4/mTxyaXp3IOD8+gaHIdsl20thVNbtqbrSafg04f71Y0+T/pY7PVpD3r9gjk4I/E9Xsu47LZ5uX8KSv9ud7P72yHflLF2bcumkSaRJpEaVewyVj/i8Op62ufv5G3z17cL6Xzh9Yy8mmkt8k5yr55KWBxGPw54u+kMp69LAAAAAAAAAAAAAAAAAAAAODuHzS8LKbEVPo+AAAAAElFTkSuQmCC";
    if(isSuccess){
    	$('.image-file-label').next('.help-block').html('');
      var reader = new FileReader();
      reader.onload = function(e)
      {
        
        if(typeof $(input).data('image-file-label') === 'undefined')
        {
          var inputContainer = $(input).parent();
          if(inputContainer.siblings('.image-file-label').length == 0)
          {
            // alert('sdfsdfsf');
            /*get image width and height*/
            var image = new Image();
            image.src = e.target.result;
            image.onload = function() {
              imageWidth = this.width;
              imageHeight = this.height;
              $(input).data('width', imageWidth);
              $(input).data('height', imageHeight);
            }
            
            // $('<span class="image-container"><img src="' + e.target.result + '"></span>').insertAfter(inputContainer);
            /*$('.image-file-label')*/
            if(extension == 'mp4' || extension == 'ogx' || extension == 'oga' || extension == 'ogv' || extension == 'ogg' || extension == 'webm' || extension == 'mov'){
              $(inputContainer).children(".image-file-label").html('<span class="image-container"><img src="'+videoImgSrc+'" style="border-radius: 14px;"></span><span class="image-file-upload-again">Change Video</span>');
            }else{
              $(inputContainer).children(".image-file-label").html('<span class="image-container"><img src="' + e.target.result + '"></span><span class="image-file-upload-again">Change Photo</span>');
            }
            
            $(inputContainer).children('.image-file-remove').show();
          }
          else
          {
            if(extension == 'mp4' || extension == 'ogx' || extension == 'oga' || extension == 'ogv' || extension == 'ogg' || extension == 'webm' || extension == 'mov'){
              inputContainer.siblings('.image-file-label').find('img').attr('src', videoImgSrc);
            }else{
              inputContainer.siblings('.image-file-label').find('img').attr('src', e.target.result);
            }
          }
        }
        else
        {
          if(extension == 'mp4'){
            inputContainer.siblings('.image-file-label').find('img').attr('src', videoImgSrc);
          }else{
            inputContainer.siblings('.image-file-label').find('img').attr('src', e.target.result);
          }

          // $($(input).data('image-file-label')).find('img').attr('src', e.target.result);
        }
      }
      reader.readAsDataURL(input.files[0]);  
    }else{
      // console.log($(input).attr('id'));
      var element_id = $(input).attr('id');
      var inputContainer = $(input);
      if(element_id=='img-file-upload2'){
        $('.image-file-upload-again2_removable').remove();
        $('#image_required2').val('');
        $('.img-remove2').hide();
        //$('.image-container').find('img').attr('src', defaultImageForAdmin);
        inputContainer.siblings().find('img').attr('src', defaultImageForAdmin);
      }else{
        // $('.image-file-label').find('img').removeAttr('src');
        $('.image-file-upload-again').remove();
        $('#image_required').val('');
        $('.image-file-remove').hide();
        //$('.image-container').find('img').attr('src', defaultImageForAdmin);
        inputContainer.siblings().find('img').attr('src', defaultImageForAdmin);
      }
    }
    
	}
}

/*Alert popup for confirmation*/
function ConfirmDialog(url,$isMail=null) {
  if($isMail==1)
    var message = "Are you sure to delete the record ?<br/>By clicking on yes below this mail will be removed permanently and will not be shown to the front end user.";
  else    
    var message = "Are you sure to delete the record ?";
  $('<div>test</div>').appendTo('body')
    .html('<div><h4>' + message + '</h4></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
          $(this).dialog("close");
          window.location=url;
        },
        No: function() {
          $(this).dialog("close");
        }
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};

function ConfirmDialogWithCustomMessage(url,message) {
  $('<div>test</div>').appendTo('body')
    .html('<div><h4>' + message + '</h4></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
          $(this).dialog("close");
          window.location=url;
        },
        No: function() {
          $(this).dialog("close");
        }
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};

/*Alert popup for confirmation*/
function alertDialog() {
  var message = "You can only add 10 checks maximum.";
  $('<div>test</div>').appendTo('body')
    .html('<div><h4>' + message + '</h4></div>')
    .dialog({
      modal: true,
      title: 'Alert!!',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Ok: function() {
          $(this).dialog("close");
          
        },
        
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};

/*Alert popup for confirmation*/
function alertPermissionDialog(message) {
  $('<div>test</div>').appendTo('body')
    .html('<div><h4>' + message + '</h4></div>')
    .dialog({
      modal: true,
      title: 'Alert!!',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Ok: function() {
          $(this).dialog("close");
          
        },
        
      },
      close: function(event, ui) {
        $(this).remove();
      }
    });
};

//Add Outer DIV for Data Table Responsive
$(document).ready(function(){
  dataTblOuterDiv();
});
function dataTblOuterDiv() {
  $(document).ready(function(){
    $('table.dataTable').wrap('<div class="table-responsive-main"><div class="table-responsive">')
  });
};

/** Custom function to reset form and validation */
function clearValidation(formElement){
  //Internal $.validator is exposed through $(form).validate()
  var validator = $(formElement).validate();
  //Iterate through named elements inside of the form, and mark them as error free
  $('[name]',formElement).each(function(){
      validator.successList.push(this);//mark as error free
      $(this).removeClass('help-block');
      validator.showErrors();//remove error messages if present
  });
  validator.resetForm();//remove error class on name elements and clear history
  validator.reset();//remove all error and success data
}

function GetTextFromHtml(html) {  
    var dv = document.createElement("DIV");  
    dv.innerHTML = html;  
    return dv.textContent || dv.innerText || "";  
}




var ckeditPDFConfigToolbar = [
  { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
  { name: 'editing',    items : [ 'Scayt' ] },
  { name: 'links',    items : [ 'Link','Unlink','Anchor' ] },
  { name: 'insert',      items : [ 'Image', 'addFile', 'addImage', 'Table','HorizontalRule','SpecialChar' ] },
  { name: 'tools',       items : [ 'Maximize' ] },
  { name: 'document',    items : [ 'Source' ] },
  { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat','Font','FontSize' ] },
  { name: 'paragraph',    items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','JustifyLeft','JustifyRight','JustifyCenter','JustifyBlock','PageBreak' ] },
  { name: 'styles',       items : [ 'Styles', 'Format','uinfield','uinpdf','signaturedialog' ] },
  { name: 'colors',   items: [ 'TextColor', 'BGColor' ] },
];

function blockAjaxUI(){
  $(document).ajaxStart(function(){
    $.blockUI({
        message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
    });
  });


  $(document).ajaxStop(function(){
      $.unblockUI()
  });
}

/* Helper function to download file */
function download_file(fileURL, fileName) {
    // for non-IE
    if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.target = '_blank';
        var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
        save.download = fileName || filename;
           if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                document.location = save.href; 
// window event not working here
            }else{
                var evt = new MouseEvent('click', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': false
                });
                save.dispatchEvent(evt);
                (window.URL || window.webkitURL).revokeObjectURL(save.href);
            }   
    }

    // for IE < 11
    else if ( !! window.ActiveXObject && document.execCommand)     {
        var _window = window.open(fileURL, '_blank');
        _window.document.close();
        _window.document.execCommand('SaveAs', true, fileName || fileURL)
        _window.close();
    }
    return true;
}
document.onreadystatechange = function() { 
  if (document.readyState !== "complete") { 
    $.blockUI({
      message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
    });
  } else { 
    $.unblockUI();
  } 
};

$(document).on('submit', 'form', function (e) {
  $.blockUI({
    message: '<div class="dp-refresh-icon">'+loader+'</div><div class="dp-refresh-text">Please wait...</div>'
  });
  $(this).find('button[type="submit"]').attr('disabled', 'disabled');
  $(this).find('input[type="submit"]').attr('disabled', 'disabled');
});



