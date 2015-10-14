(function($){

    var userSelect = $('.manager-user-dropdown select');

    userSelect.change(function(){
        $('#manager-login-as').submit();
    });
})(jQuery);