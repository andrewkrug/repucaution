jQuery(function($){

  var $twitterPinCodeButton = $('.twitter-btn.enter-pin');

  $twitterPinCodeButton.click(function(e){
    e.preventDefault();

    $('#twitter-pin-code-modal').modal('show');

  });

});