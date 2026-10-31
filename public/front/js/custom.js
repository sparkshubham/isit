/** Menu Navbar Js **/

// document.addEventListener("DOMContentLoaded", function(){
		
//     window.addEventListener('scroll', function() {
       
//         if (window.scrollY > 50) {
//             document.getElementById('navbar_top').classList.add('fixed-top');
//             // add padding top to show content behind navbar
//             navbar_height = document.querySelector('.navbar').offsetHeight;
//             document.body.style.paddingTop = navbar_height + 'px';
//         } else {
//              document.getElementById('navbar_top').classList.remove('fixed-top');
//              // remove padding top from body
//             document.body.style.paddingTop = '0';
//         } 
//     });
// }); 
// DOMContentLoaded  end


//////////// Hide and Show Password /////////

$(".eyeicon i").click(function() {

  $(this).toggleClass("uil-eye uil-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});


//////// Header Search Bar ////////////

function searchToggle(obj, evt){
  var container = $(obj).closest('.search-wrapper');
      if(!container.hasClass('active')){
          container.addClass('active');
         container.removeClass('closed');
          evt.preventDefault();
      }
      else if(container.hasClass('active') && $(obj).closest('.input-holder').length == 0){
          container.removeClass('active');
          container.addClass('closed');
          // clear input
          container.find('.search-input').val('');
      }
}


///////////////// Image Upload and Preview ///////////////

function readURL(input) {
  if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
          $('#imagePreview').css('background-image', 'url('+e.target.result +')');
          $('#imagePreview').hide();
          $('#imagePreview').fadeIn(650);
      }
      reader.readAsDataURL(input.files[0]);
  }
}
$("#imageUpload").change(function() {
  // readURL(this);
});



function readURL(input) {
  if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
          $('#imagePreview').css('background-image', 'url('+e.target.result +')');
          $('#imagePreview').hide();
          $('#imagePreview').fadeIn(650);
      }
      reader.readAsDataURL(input.files[0]);
  }
}
$("#event_image_upload").change(function() {
  readURL(this);
});


///////

// $(document).ready(function($) {
//   $('#owl-carousel-video').owlCarousel({
//   items:1,
//   merge:true,
//   loop:true,
//   margin:10,
//   video:true,
//   lazyLoad:true,
//   center:true,
//   responsive:{
//       480:{
//           items:2
//       },
//       600:{
//           items:2
//       }
//   }
// });
// });
// var tag = document.createElement('script');
// tag.src = "https://www.youtube.com/player_api";
// var firstScriptTag = document.getElementsByTagName('script')[0];
// firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// var players=[],
//     iframes = $(".carousel__iframe");

// function onYouTubePlayerAPIReady() {
//   for (var i = 0; i < iframes.length; i++) {
//       var iframeItem = iframes[i];
  
//       players[i] = new YT.Player(iframeItem, {
//         suggestedQuality: 'hd720'
//       });
//   }
// }

// $(function() {
//   $("#owl-carousel-video").owlCarousel({
//     items: 1,
//     singleItem: true,
//     autoPlay: true,
//     navigation: true,
//     navigationText: ["", ""],
//     afterAction: function() {

//       for (var i = 0; i < players.length; ++i) {
//         players[i].pauseVideo();
//       }

//       $(".backdrop").show();
//     }
//   });

//   $(".backdrop").click(function() {
//     var i = $(this).hide().index('.backdrop');
//     players[i].playVideo();
//   });
// });



// $('.owl-carousel').owlCarousel({
//   items: 1,
//   loop: true,
//   video: true,
//   lazyLoad: true
// }); 


// $(function() {
//   $(".owl-carousel").owlCarousel({
//     items: 1,
//     merge: true,
//     loop: true,
//     margin: 10,
//     video: true,
//     lazyLoad: true,
//     center: true,
//     responsive: {
//       320: {
//         items: 1
//       },
//       560: {
//         items: 1
//       },
//       992: {
//         items: 1
//       }
//     }
//   });
// });




$('.owl-carousel').on('initialized.owl.carousel changed.owl.carousel', function(e) {
  if (!e.namespace)  {
    return;
  }
  var carousel = e.relatedTarget;
  if(carousel.items().length>0)
  {
    $('.slider-counter').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
    $('.slider-counter').removeClass('hide')
  }
}).owlCarousel({
  items: 1,
      merge: true,
      loop: false,
      margin: 5,
      video: true,
      lazyLoad: true,
      center: true,
});

$(function() {
  $(".ad-slider").owlCarousel({
    items: 1,
    merge: true,
    loop: false,
    margin: 5,
    video: true,
    lazyLoad: true,
    center: true
  });
});


//// Scrollbar ////

jQuery(document).ready(function(){
  // jQuery('.scrollbar-outer').scrollbar();
});



//////// Home Sidebar Slider ////////

// jQuery(document).ready(function ($) {
// 	var owl = $(".home-sidebar-slider");
// 	owl.owlCarousel({
// 		autoplay: true,
// 		autoplayTimeout: 1000,
// 		autoplayHoverPause: true,
// 		items: 2,
// 		loop: false,
// 		center: false,
// 		rewind: false,
// 		mouseDrag: true,
// 		touchDrag: true,
// 		pullDrag: true,
// 		freeDrag: false,
// 		margin: 3,
// 		stagePadding: 0,
// 		merge: false,
// 		mergeFit: true,
// 		autoWidth: false,
// 		startPosition: 0,
// 		rtl: false,
// 		smartSpeed: 250,
// 		fluidSpeed: false,
// 		dragEndSpeed: false,
// 		responsiveRefreshRate: 200,
// 		responsiveBaseElement: window,
// 		fallbackEasing: "swing",
// 		info: false,
// 		nestedItemSelector: false,
// 		itemElement: "div",
// 		stageElement: "div",
// 		refreshClass: "owl-refresh",
// 		loadedClass: "owl-loaded",
// 		loadingClass: "owl-loading",
// 		rtlClass: "owl-rtl",
// 		responsiveClass: "owl-responsive",
// 		dragClass: "owl-drag",
// 		itemClass: "owl-item",
// 		stageClass: "owl-stage",
// 		stageOuterClass: "owl-stage-outer",
// 		grabClass: "owl-grab",
// 		autoHeight: false,
// 		lazyLoad: false
// 	});

// 	$(".next").click(function () {
// 		owl.trigger("owl.next");
// 	});
// 	$(".prev").click(function () {
// 		owl.trigger("owl.prev");
// 	});
// });


$(".pushme2").click(function(){
  $('.pushme2.active').toggleClass('active');
  $('.pushme2 span').text(function(i, v){
     return v === 'Like' ? 'Liked' : 'Like'
  });
});

$(document).ready(function() {
  $('.leftpannel-open').click(function() {
      $('.follow-users-section').addClass('active');
  });
});
$(document).ready(function() {
  $('.follow-users-section .btn-close').click(function() {
      $('.follow-users-section').removeClass('active');
  });
});



// functionality to copy text from inviteCode to clipboard
// trigger copy event on click
$('#copy').on('click', function(event) {
  console.log(event);
  copyToClipboard(event);
});

// event handler
function copyToClipboard(e) {
  // alert('this function was triggered');
  // find target element
  var
    t = e.target, 
    c = t.dataset.copytarget,
    inp = (c ? document.querySelector(c) : null);
  console.log(inp);
  // check if input element exist and if it's selectable
  if (inp && inp.select) {
    // select text
    inp.select();
    try {
      // copy text
      document.execCommand('copy');
      inp.blur();

      // copied animation
      t.classList.add('copied');
      setTimeout(function() {
        t.classList.remove('copied');
      }, 1500);
    } catch (err) {
      //fallback in case exexCommand doesnt work
      alert('please press Ctrl/Cmd+C to copy');
    }

  }

}





// $("input").keyup(function () {
//   if (this.value.length == this.maxLength) {
//     $(this).next('li input').focus();
//   }
// });


$(".searchusers .search-bar").focus(function() {
  // $('.following-users').addClass('active');       
  //return false;
});

$(".searchusers").focusout(function() {
  // $('.following-users.active').removeClass('active');       
  //return false;
});

$(".search-wrapper .search-input").focus(function() {
  // $('.following-users').addClass('active');       
  //return false;
});

$(".search-wrapper .search-input").focusout(function() {
  // $('.search-wrapper .following-users.active').removeClass('active');       
  //return false;
});



/************* Dollar Sign **************/
(function init() {
	// Add event in init for Calculate Your Needs JS
	$('#priceindollar').on('keyup blur', _formatInput);
}());

function _formatInput(e) {
	var $el = $(this);
	var dollars = $el.val();

	// kick out on arrow keys
	if ([37, 38, 39, 40].indexOf(e.which) > -1) {
		return;
	}
	
	// aaalife.utils.formatDollars
	dollars = formatDollars(dollars);
	
	if (e.type === 'blur') {
		dollars = dollars.split('.')[0];
	}
	$el.val(dollars);
}

function formatDollars(dollars, separator) {
	var parts = dollars.toString().split('.');
	var separator = separator || ',';
	
	parts[0] = parts[0]
				.toString() // make it a string
				.replace(/\D*/g, '') // replace non-numbers
				.replace(/\B(?=(\d{3})+(?!\d))/g, separator); // insert commas
	
	dollars = '$ ' + parts.join('.');
	
	return dollars;
}


/***** Messages Chat ********/
$(function(){
  $('.messages-part .sidebar-group .sidebar .list-group-item').on("click", function () {
    $('.messages-part .chat').addClass("active");
    $('.messages-part .sidebar-group').addClass("d-none");
    // setTimeout(RemoveClass, 1000);
  });
  $('.messages-part .chat-header-user .back-btn').on("click", function () {
      $('.messages-part .chat.active').removeClass("active");
      $('.messages-part .sidebar-group.d-none').removeClass("d-none");
      // setTimeout(RemoveClass, 1000);
    });
}); 



/********* Upload Profile and Preview ***********/

function readURL(input) {
  if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
          $('#imagePreview').css('background-image', 'url('+e.target.result +')');
          $('#imagePreview').hide();
          $('#imagePreview').fadeIn(650);
          $("#imagePreview").addClass("update");
      }
      reader.readAsDataURL(input.files[0]);
  }
}

$("#imageUpload").change(function() {
  // readURL(this);
});


