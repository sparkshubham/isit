// Get old selected address
let oldAddress = $('.signup-selected-address');
// clear selected address
$.each(oldAddress,function(){
    $(this).removeClass('signup-selected-address');
});
// Reset dynamic variable