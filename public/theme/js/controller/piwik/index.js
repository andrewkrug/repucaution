(function($) {

    var dates = {
        from: $('[name="daterangepicker_start"]').val(),
        to: $('[name="daterangepicker_end"]').val()
    };

    var web_source = $('#web-template').html();
    var data_source = $('#data-template').html();

    // dates filter "Apply" button
    var $filter = $('.show-calendar');

    var $dates = $('.data');

    // data container
    var $container = $('.ga_data');

    // filter "Apply" buton click
    $('.main').on('click', '.applyBtn', function() {
        request();
    });

    // request on page load
    request();

    function request() {

        var web_template = Handlebars.compile(web_source);
        var web_html = web_template();
        $container.html(web_html);

        var $databox = $('#content');
        var $chartbox = $('#chartbox');
        var dates = {
            from: $('[name="daterangepicker_start"]').val(),
            to: $('[name="daterangepicker_end"]').val()
        };

        $.ajax({
            url: g_settings.analytics.request_url,
            type: 'POST',
            data: dates,
            beforeSend: function() {
                wait();
            },
            success: function(response) {
                response = JSON.parse(response);
                if(response.success) {

                    var data_template = Handlebars.compile(data_source);
                    var data_html = data_template(response);
                    $databox.html(data_html);
                    pieChart();

                    $chartbox.height(300);
                    _chart(response.visits_chart);
                } else {
                    showFlashErrors(lang('analytics_request_error'));
                }
            },
            complete: function() {
                stopWait();
            }
        });

    }

    function _request_web_data() {
        //var $databox = $('#content');
        //var dates = {
        //    from: $('[name="daterangepicker_start"]').val(),
        //    to: $('[name="daterangepicker_end"]').val()
        //};
        //var xhr_req = $.post(
        //    g_settings.analytics.request_url,
        //    dates,
        //    function(response, status, xhr) {
        //        if (response) {
        //            if (response.success) {
        //
        //                if (response.result !== null && response.result[0] !== undefined && response.result[0] !== null) {
        //
        //                    response.result = response.result[0];
        //
        //                    var data_context = {};
        //                    var index = 0;
        //                    for(item in response.result) {
        //                        if(response.result.hasOwnProperty(item)) {
        //                            data_context[ response.headers[index] ] = parseValue(index, response.result[item], response.values);
        //                            index += 1;
        //                        }
        //                    }
        //
        //                    var data_template = Handlebars.compile(data_source);
        //                    var data_html = data_template(data_context);
        //
        //                    $databox.html(data_html);
        //                    pieChart();
        //
        //                } else {
        //                    showFlashErrors(lang('analytics_no_results'));
        //                }
        //
        //            } else {
        //                showFlashErrors(response.error);
        //            }
        //        } else {
        //            showFlashErrors(lang('analytics_request_error'));
        //        }
        //    },
        //    'json'
        //);
        //
        //return xhr_req;
    }


   // draw chart for web data visits
    function _chart(response) {

        var data = [];

        // create highcharts-friendly array
        for(item in response) {
            if(response.hasOwnProperty(item)) {
                var date = item.split('-');
                var value = response[item];
                data.push([
                    Date.UTC(parseInt(date[0], 10),parseInt(date[1], 10) - 1,parseInt(date[2], 10)),
                    value
                ]);
            }
        }
        draw_chart(data, 'chartbox', lang('visits'))
    }

})(jQuery);