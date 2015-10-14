/**
 * Created by beer on 12.10.15.
 */
(function ($) {
    var submited = false;
    $('#register-form').on('submit', function() {
        submited= true;
    });
    window.addEventListener("beforeunload", function (e) {

        if(submited) return undefined;
        var confirmationMessage = lang('leave_register_message');

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
})();