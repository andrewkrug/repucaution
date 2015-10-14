(function($) {

    var accounts_source = $("#accounts-template").html();
    var alert_source = $('#alert-template').html();
    var $loading = $('.loading');
    var $save = $('#save');
    var $form = $save.closest('form');

    $save.on('click', function() {
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });

    $form.on('change', 'input[type="radio"]', function() {
        var $self = $(this);
        var data = $self.data();
        for(var key in data) {
            $form.find('[name="' + key + '_name"]').val(data[key]);
        }
    });

    $.get(
        g_settings.analytics.get_accounts_url,
        function(response, xhr, status) {
            if (response) {
                if (response.success) {
                    var template = Handlebars.compile(accounts_source);
                    var context = {
                        accounts: response.result,
                        current: response.current
                    };
                    var html = template(context);

                    $loading.removeClass('loading').html(html);

                } else {

                    var template = Handlebars.compile(alert_source);
                    var context = {
                        type: 'error',
                        message: response.error
                    };
                    var html = template(context);

                    $loading.removeClass('loading').html(html);

                    $save.val('Back');
                }
                $save.removeClass('disabled');
            }
        },
        'json'
    );

})(jQuery);