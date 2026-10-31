
  function darken_screen(yesno){
      if( yesno == true ){
        document.querySelector('.screen-darken').classList.add('active');
      }
      else if(yesno == false){
        document.querySelector('.screen-darken').classList.remove('active');
      }
  }
  
  function close_offcanvas(){
      darken_screen(false);
      document.querySelector('.mobile-offcanvas.show').classList.remove('show');
      document.body.classList.remove('offcanvas-active');
  }
  
  function show_offcanvas(offcanvas_id){
      darken_screen(true);
      document.getElementById(offcanvas_id).classList.add('show');
      document.body.classList.add('offcanvas-active');
  }
  
  document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('[data-trigger]').forEach(function(everyelement){
      
      let offcanvas_id = everyelement.getAttribute('data-trigger');
      
      everyelement.addEventListener('click', function (e) {
          e.preventDefault();
              show_offcanvas(offcanvas_id);
            
        });
    });
    
  
    document.querySelectorAll('.btn-close').forEach(function(everybutton){
      
        everybutton.addEventListener('click', function (e) {
          e.preventDefault();
              close_offcanvas();
          });
    });
  
   
    }); 


  ///shubham js here start

function callajax(url,methodType,data)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    // e.preventDefault();
    var formData = data;
    var type = methodType;
    var ajaxurl = url;
    $.ajax({
        type: type,
        url: ajaxurl,
        data: formData,
        dataType: 'json',
        success: function (data) {
          console.log(data.message);
            // toastr.success(data.message);
            // location.reload();
            return data;
        },
        error: function (data) {
            toastr.error(data.responseJSON.message);
            return data;

        }
    });
}

    function getExtension(filename) {
      var parts = filename.split('.');
      return parts[parts.length - 1];
    }
    

  function isImage(filename) {
      var ext = getExtension(filename);
      switch (ext.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'bmp':
        case 'png':
          //etc
          return true;
      }
      return false;
    }


    
    function isVideo(filename) {
      var ext = getExtension(filename);
      switch (ext.toLowerCase()) {
        // case 'm4v':
        // case 'avi':
        case 'ogg':
        case 'mp4':
        case 'webm':
          // etc
        return true;
      }
      return false;
    }

    
    


  function failValidation(msg) {
    alert(msg); // just an alert for now but you can spice this up later
    return false;
  }
  $("#save_draft").click(function(){
      $("#draft_status").val(1);
      $('#generalpost-form').submit()
  });

  $("#draft_btn").click(function(){
    $("#draft_status_anointed").val(1);
    $('#anointedPost-form').submit()
});




$("#my-posts-tab").click(function(){
  $("#my_save_post_flag").val(0);
});


$("#saved-posts-tab").click(function(){
  $("#my_save_post_flag").val(1);

});




// $(function() {
//   var charLimit = 1;
//   $(".inputs").keydown(function(e) {

//       var keys = [8, 9, /*16, 17, 18,*/ 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 144, 145];

//       if (e.which == 8 && this.value.length == 0) {
//           // $(this).prev('.inputs').focus();
//       } else if ($.inArray(e.which, keys) >= 0) {
//           return true;
//       } else if (this.value.length >= charLimit) {
//           $(this).next('.inputs').focus();
//           return false;
//       } else if (e.shiftKey || e.which <= 48 || e.which >= 58) {
//           return false;
//       }
//   }).keyup (function () {
//       if (this.value.length >= charLimit) {
//           $(this).next('.inputs').focus();
//           return false;
//       }
//   });
// });


$('.digit-group').find('input').each(function() {
  $(this).attr('maxlength', 1);
  $(this).on('keyup', function(e) {
    var parent = $($(this).parent());

    if (e.keyCode === 8 || e.keyCode === 37) {
      var prev = parent.find('input#' + $(this).data('previous'));

      if (prev.length) {
        $(prev).select();
      }
    } else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
      var next = parent.find('input#' + $(this).data('next'));

      if (next.length) {
        $(next).select();
      } else {
        if (parent.data('autosubmit')) {
          parent.submit();
        }
      }
    }
  });
});


// console.log(timeAgo('2021-08-09T15:29:01+0000'));

function timeAgo(input) {
  const date = (input instanceof Date) ? input : new Date(input);
  const formatter = new Intl.RelativeTimeFormat('en');
  const ranges = {
    years: 3600 * 24 * 365,
    months: 3600 * 24 * 30,
    weeks: 3600 * 24 * 7,
    days: 3600 * 24,
    hours: 3600,
    minutes: 60,
    seconds: 1
  };
  const secondsElapsed = (date.getTime() - Date.now()) / 1000;
  for (let key in ranges) {
    if (ranges[key] < Math.abs(secondsElapsed)) {
      const delta = secondsElapsed / ranges[key];
      return formatter.format(Math.round(delta), key);
    }
  }
}

$(document).ready(function() {
  $(".comments_val_class").prop('disabled',true);
});



$('textarea').keyup(function (event) {
        if (event.keyCode == 13 && event.shiftKey) {
            var content = this.value;
            var caret = getCaret(this);
            this.value = content.substring(0,caret)+"\n"+content.substring(carent,content.length-1);
            event.stopPropagation();
            
      }else if(event.keyCode == 13)
      {
          $('.comment-form').submit();
      }
  });

  function getCaret(el) { 
      if (el.selectionStart) { 
              return el.selectionStart; 
          } else if (document.selection) { 
          el.focus(); 

          var r = document.selection.createRange(); 
          if (r == null) { 
                  return 0; 
          } 

          var re = el.createTextRange(), 
          rc = re.duplicate(); 
          re.moveToBookmark(r.getBookmark()); 
          rc.setEndPoint('EndToStart', re); 

          return rc.text.length; 
      }  
      return 0; 
  }

  
function enableBtncmt(value)
{

      $("textarea").each(function () {
        this.setAttribute("style", "height:40px;");
      }).on("input", function () {
        this.style.height = "auto";
        this.style.height = "40px";
      });
      $("textarea").css('max-height','80px');
      $("textarea").css('overflow-y','auto!important;');

      if(value)
      {
        $('.comments_val_class').prop('disabled',false);
        $('.comments_val_class').removeClass('disable-comment')
      }
      else{
        $('.comments_val_class').prop('disabled',true);
        $('.comments_val_class').addClass('disable-comment')
      }

}


function enterKeyPressed(event) {

  if (event.keyCode == 13 && event.shiftKey) {
    var content = this.value;
    var caret = getCaret(this);
    this.value = content.substring(0,caret)+"\n"+content.substring(carent,content.length-1);
    event.stopPropagation();
    
}else if(event.keyCode == 13)
{
  
  var id=  event.target.id
  var postid= $('#'+id).attr('post-id')
  var authid= $('#'+id).attr('auth-id')
  var parent_id= $('#'+id).attr('parent-coment-id')
  replycommentOnpost(postid,parent_id,authid)

  
} else {
      $("textarea").each(function () {
        this.setAttribute("style", "height:40px;");
      }).on("input", function () {
        this.style.height = "auto";
        this.style.height = "40px";
      });
      $("textarea").css('max-height','80px');
      $("textarea").css('overflow-y','auto!important;');

      if(event.target.value)
      {
        $('.comments_val_class').prop('disabled',false);
        $('.comments_val_class').removeClass('disable-comment')
      }
      else{
        $('.comments_val_class').prop('disabled',true);
        $('.comments_val_class').addClass('disable-comment')
      }
  }
}







