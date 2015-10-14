jQuery(function($){

    /**
     * Select contains list of timezones
     *
     * @type {*|jQuery|HTMLElement}
     */
    var $timezone_select = $('#name-timezone');

    /**
     * Send ajax request to server
     * This request save user timezone
     * And return alert-status-message
     */
    $timezone_select.on('change', function() {

       wait();

        $.ajax({
            url: g_settings.base_url + 'settings/personal/save_timezone',
            type: 'POST',
            data: 'timezone=' + $timezone_select.val(),
            dataType: 'JSON',
            success: function(response) {
                if(response.success) {
                    showFlashSuccess(response.message);
                } else {
                    showFlashErrors(response.message);
                }
            },
            complete: function() {
                stopWait();
            }
        });

        return false;
    });

    $("a > .social-btn").on('click', function() {
        location.href = $(this).closest("a").attr("href");
    });

});