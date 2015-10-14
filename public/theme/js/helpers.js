var wait = function(){
    $('html').addClass('shadow-lock');
    var message = '<div class="notification notify_wait"><div class="container"><p class="notify_text">'+lang('please_wait')+'<i class="fa fa-remove close_block"></i></p></div></div>';
    var shadow = '<div class="shadow"><img src="'+g_settings.base_url+'public/theme/images/loading/loading.gif" class="shadow_picture" alt=""></div>';
    $(message).insertBefore('.page-wrapper');
    $('.main').append(shadow);
};

var stopWait = function(){
    $('html').removeClass('shadow-lock');
    $('.notification.notify_wait').remove();
    $('.shadow').remove();
};

var showFormErrors = function(errors){
    var $main_block = $('.main_block');
    for (var error in errors) {
        if(error == 'post_to_groups[]') {
            showFlashErrors(errors[error]);
            continue;
        }
        var errorHtml = '<p class="error_text"><i class="fa fa-exclamation-circle"></i>'+errors[error].replace(/<[^>]+>/gi, '')+'</p>';
        var input = $main_block.find('input[name="' + error + '"]');
        $(errorHtml).insertAfter(input);
        input.parent().addClass('has-error');
        var textarea = $main_block.find('textarea[name="' + error + '"]');
        $(errorHtml).insertAfter(textarea);
        textarea.parent().addClass('has-error');
    }
};

var clearErrors = function(){
    $('.error_text').remove();
    $('.has-error').removeClass('has-error');
};

var showFlashErrors = function(errorText){
    $('.notification').remove();
    var str = '<div class="notification notify_bad">' +
    '<div class="container"><p class="notify_text">' +
    errorText +
    '<i class="fa fa-remove close_block"></i></p></div>' +
    '</div>';
    var html = $.parseHTML( str );
    $(html).insertAfter('.wrapper');
    closeBlock($('.close_block'), '.notification');
};

var showFlashSuccess = function(text){
    $('.notification').remove();
    var str = '<div class="notification notify_good">' +
        '<div class="container"><p class="notify_text">' +
        text +
        '<i class="fa fa-remove close_block"></i></p></div>' +
        '</div>';
    var html = $.parseHTML( str );
    $(html).insertAfter('.wrapper');
    closeBlock($('.close_block'), '.notification');
};

function arrays_equal(a,b) { return !!a && !!b && !(a<b || b<a); }