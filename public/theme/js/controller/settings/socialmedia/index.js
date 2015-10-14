jQuery(function($){

    masonryBlock();

    /**
     * On this button we click to save user timezone
     *
     * @type {*|jQuery|HTMLElement}
     */
    var $save_button = $('#save-timezone');

    /**
     * Select contains list of timezones
     *
     * @type {*|jQuery|HTMLElement}
     */
    var $timezone_select = $('#name-timezone');

    /**
     * Container of timezones section
     *
     * @type {*}
     */
    var $save_button_div = $('#timezone-section');

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div class="col-xs-12 text-center p-tb10"><img src="/public/theme/images/loading/loading.gif" alt=""></div>');


    /**
     * Send ajax request to server
     * This request save user timezone
     * And return alert-status-message
     */
    $save_button.on('click', function() {

        $save_button_div.prepend($loading);
        $save_button.parents('.section').find('.alert').remove();

        $.ajax({
            url: g_settings.base_url + 'settings/socialmedia/save_timezone',
            type: 'POST',
            data: 'timezone=' + $timezone_select.val(),
            dataType: 'JSON',
            success: function(response) {
                $loading.replaceWith(response.message);
            }
        });

        return false;
    });

    $("a > .social-btn").on('click', function() {
        location.href = $(this).closest("a").attr("href");
    });

});