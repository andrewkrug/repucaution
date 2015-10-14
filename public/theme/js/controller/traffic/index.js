(function($) {

    var dates = {
        from: $('[name="daterangepicker_start"]').val(),
        to: $('[name="daterangepicker_end"]').val()
    };

    var table_source = $('#table-template').html();
    var table_hidden_phone_source = $('#table-hidden-phone-template').html();
    var web_source = $('#web-template').html();
    var data_source = $('#data-template').html();
    var alert_source = $('#alert-template').html();

    // dates filter "Apply" button
    var $filter = $('.show-calendar');

    var $dates = $('.data');

    // data container
    var $container = $('.ga_data');

    // traffic type menu
    var $menu = $('.nav-tabs');

    // store current request
    var xhr;

    // get hash on load
    var global_hash = window.location.hash.replace('#', '');

    // menu item click
    $menu.find('a').on('click', function(event) {
        var $self = $(this);

        global_hash = window.location.hash.replace('#', '');
        var _hash = event.target.hash.replace('#', '');

        if (_hash == global_hash) {
            return false;
        }

        global_hash = _hash;

        request();
    });

    // filter "Apply" buton click
    $('.main').on('click', '.applyBtn', function() {
        request();
    });

    // request on page load
    request();

    function request() {
        wait();
        // if hash is empty or type not valid - open the default tab
        rehash();

        // change menu selected item
        $menu.children().removeClass('active');
        $menu.find('a[href$="#' + global_hash + '"]').closest('li').addClass('active');

        
        $filter.addClass('disabled');

        if (xhr !== undefined && xhr !== null) {
            for (req in xhr) {
                xhr[req].abort()
            }
        }

        switch(global_hash) {
            // load chart and common data
            case 'web':
                xhr = _request_web();
                break;
            // otherwise - table
            default:
                xhr = _request_table();
        }

        // for one page multiple ajax requests
        // update date and enable filter button when all requests done
        $.when.apply(null, xhr).done(function() {

            $filter.removeClass('disabled');

            // parse callback for each request, update dates
            for (arg in arguments) {
                if ( arguments[arg][0] === undefined ) continue;
                var response = arguments[arg][0];
                if (response.dates !== undefined) {
                    $dates.html(response.dates.from + ' - ' + response.dates.to);
                }    
            }

            stopWait();

        });

    }

    function rehash() {
        if (global_hash.length == 0 || $.inArray(global_hash, g_settings.analytics.traffic_types) == -1) {
            global_hash = g_settings.analytics.default_traffic_type;
        }
        return global_hash;
    }

    function _request_table() {

        var table_template = Handlebars.compile(table_source);
        var table_html = table_template();
        $container.html(table_html);

        var $tablebox = {
            hidden : $container.find('.hidden-phone .tablebox-inner').first()
            //visible : $container.find('.visible-phone .tablebox-inner').first()
        };

        var $caption_hidden = $container.find('.sectionTitle');
        //var $caption_visible = $container.find('.visible-phone .sectionTitle').first();
        var dates = {
            from: $('[name="daterangepicker_start"]').val(),
            to: $('[name="daterangepicker_end"]').val()
        };
        var xhr_req = $.post(
            g_settings.analytics.request_url + '/' + global_hash,
            dates,
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {
                        Handlebars.registerHelper("counter", function (index){
                            return index + 1;
                        });
                        var hidden_template = Handlebars.compile(table_hidden_phone_source);
                        //var visible_template = Handlebars.compile(table_visible_phone_source);

                        var float_indexes = [];
                        var time_indexes = [];
                        for(var i=0; i<response.values.length;i++) {
                            if(response.values[i] == 'percent' || response.values[i] == 'float') {
                                float_indexes.push(i);
                            } else if(response.values[i] == 'time') {
                                time_indexes.push(i);
                            }
                        }

                        if(response.result) {
                            var total = [];
                            for (var j=0;j<response.result.length;j++) {
                                for (i=1;i<response.result[j].length;i++) {
                                    if(!total[i]) {
                                        total[i] = 0;
                                    }
                                    if(i == 1) {
                                        total[i] += parseInt(response.result[j][i]);
                                    } else {
                                        total[i] += parseFloat(response.result[j][i]);
                                    }
                                }
                                for (i=0;i<float_indexes.length;i++) {
                                    response.result[j][float_indexes[i]] = parseFloat(response.result[j][float_indexes[i]]).toFixed(2);
                                }
                                for (i=0;i<time_indexes.length;i++) {
                                    var _value = parseFloat(response.result[j][time_indexes[i]]).toFixed(0);
                                    var _hours = (parseInt(_value / 3600) % 24).toString();
                                    var _minutes = (parseInt(_value / 60)  % 60).toString();
                                    var _seconds = (parseInt(_value) % 60).toString();
                                    response.result[j][time_indexes[i]] = ((_hours.length < 2) ? '0'+_hours : _hours) + ':'
                                        + ((_minutes.length < 2) ? '0'+_minutes : _minutes) + ':'
                                        + ((_seconds.length < 2) ? '0'+_seconds : _seconds);
                                }
                            }
                            for (i=0;i<total.length;i++) {
                                response.headers[i] = lang(response.headers[i]);
                                if(i == 1) {
                                    response.headers[i] += ' ('+total[i]+')';
                                } else if(i == 2) {
                                    var _value = total[i]/response.result.length.toFixed(0);
                                    var _hours = (parseInt(_value / 3600) % 24).toString();
                                    var _minutes = (parseInt(_value / 60)  % 60).toString();
                                    var _seconds = (parseInt(_value) % 60).toString();
                                    _value = ((_hours.length < 2) ? '0'+_hours : _hours) + ':'
                                    + ((_minutes.length < 2) ? '0'+_minutes : _minutes) + ':'
                                    + ((_seconds.length < 2) ? '0'+_seconds : _seconds);
                                    response.headers[i] += ' ('+_value+')';
                                } else if(i > 2) {
                                    response.headers[i] += ' ('+(total[i]/response.result.length).toFixed(2)+')';
                                }
                            }
                        }

                        var hidden_html = hidden_template(response);
                        //var visible_html = visible_template(response);

                        $tablebox.hidden.removeClass('loading').html(hidden_html);
                        //$tablebox.visible.removeClass('loading').html(visible_html);

                        $caption_hidden.html(response.caption);
                        //$caption_visible.html(response.caption);

                    } else {
                        showFlashErrors(response.error);
                    }
                } else {
                    showFlashErrors(lang('analytics_request_error'));
                }
            },
            'json'
        );

        return [ xhr_req ];
    }

    function _request_web() {

        var web_template = Handlebars.compile(web_source);
        var web_html = web_template();
        $container.html(web_html);

        var xhr_req_data = _request_web_data();
        var xhr_req_chart = _request_web_chart();

        return [ xhr_req_data, xhr_req_chart ];
    }

    function _request_web_chart() {
        var $chartbox = $('#chartbox');
        var dates = {
            from: $('[name="daterangepicker_start"]').val(),
            to: $('[name="daterangepicker_end"]').val()
        };
        var xhr_req = $.post(
            g_settings.analytics.request_url + '/web/chart',
            dates,
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {
                        $chartbox.height(300);
                        _chart(response.result);
                    } else {
                        showFlashErrors(response.error);
                    }
                } else {
                    showFlashErrors(lang('analytics_request_error'));
                }
            },
            'json'
        );
        return xhr_req;
    }

    function _request_web_data() {
        var $databox = $('#content');
        var dates = {
            from: $('[name="daterangepicker_start"]').val(),
            to: $('[name="daterangepicker_end"]').val()
        };
        var xhr_req = $.post(
            g_settings.analytics.request_url + '/web',
            dates,
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {

                        if (response.result !== null && response.result[0] !== undefined && response.result[0] !== null) {

                            response.result = response.result[0];

                            var data_context = {};
                            var index = 0;
                            for(item in response.result) {
                                data_context[ response.headers[index] ] = parseValue(index, response.result[item], response.values);
                                index += 1;
                            }

                            var data_template = Handlebars.compile(data_source);
                            var data_html = data_template(data_context);

                            $databox.html(data_html);
                            pieChart();

                        } else {
                            showFlashErrors(lang('analytics_no_results'));
                        }

                    } else {
                        showFlashErrors(response.error);
                    }
                } else {
                    showFlashErrors(lang('analytics_request_error'));
                }
            },
            'json'
        );

        return xhr_req;
    }

    function _draw_error(error, $con) {
        var data_template = Handlebars.compile(alert_source);
        var data_context = {
            type: 'error',
            message: error
        };
        var data_html = data_template(data_context);
        if ($.isPlainObject($con)) {
            for(item in $con) {
                $(data_html).insertAfter($('.header.navbar'));
            }   
        } else {
            $(data_html).insertAfter($('.header.navbar'));
        }

    }


   // draw chart for web data visits
    function _chart(response) {

        var data = [];

        // create highcharts-friendly array
        for(item in response) {
            var value = response[item];
            data.push([
                Date.UTC(parseInt(value[0], 10),parseInt(value[1], 10) - 1,parseInt(value[2], 10)),
                parseInt(value[3], 10)
            ]);
        }
        draw_chart(data, 'chartbox', lang('visits'))
    }

})(jQuery);