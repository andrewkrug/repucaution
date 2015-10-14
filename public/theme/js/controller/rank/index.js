(function($) {

    var $select = $('.select_block');
    var $tbody = {
        hidden : $('.main_block .hidden-phone table tbody'),
        visible : $('.main_block .visible-phone table tbody')
    };

    var period_id = 0;

    var hidden_loading_source = $tbody.hidden.html();
    var visible_loading_source = $tbody.visible.html();

    var alert_source = $('#alert-template').html();
    var tbody_hidden_phone_source = $('#tbody-hidden-phone-template').html();
    var tbody_visible_phone_source = $('#tbody-visible-phone-template').html();

    /*$select.ddslick({
        onSelected: function() {
            request();   
        }
    });*/

    function request(force) {
        wait();
        if (period_id  == $('[name="period"]').val()) {
            if ( force === undefined || ! force) {
                return false;
            }
        }
        $select.addClass('disabled');
        period_id = $('[name="period"]').val();
        
        $tbody.hidden.html(hidden_loading_source);
        $tbody.visible.html(visible_loading_source);

        $.post(
            g_settings.google_rank.filter_url,
            { period_id: period_id },
            function (response, xhr, status) {
                if (response) {
                    if (response.success) {
                        
                        var hidden_phone_template = Handlebars.compile(tbody_hidden_phone_source);
                        //var visible_phone_template = Handlebars.compile(tbody_visible_phone_source);

                        var hidden_phone_html = hidden_phone_template(response);
                        //var visible_phone_html = visible_phone_template(response);

                        $('.main_block .hidden-phone table tbody').html(hidden_phone_html);
                        //$('.main_block .visible-phone table tbody').html(visible_phone_html);

                    } else {
                        _draw_error(response.error, $tbody);
                    }
                } else {
                    _draw_error('Invalid request.', $tbody);
                }
            },
            'json'
        ).always(function(response){
            if (response.period_id !== undefined) {
                // $select.ddslick('select', {index: parseInt(response.period_id, 10) + 1});
            }
                stopWait();
        });
    }
    $(document).ready(function(){
        request(true);
    });


    function _draw_error(error, $con) {
        var data_template = Handlebars.compile(alert_source);
        var data_context = {
            type: 'error',
            message: error
        };
        var data_html = data_template(data_context);
        if ($.isPlainObject($con)) {
            for(item in $con) {
                $con[item].removeClass('loading').html(data_html);
            }   
        } else {
            $con.removeClass('loading').html(data_html);
        }
    }

})(jQuery);