(function($) {
    // get default dates
    var dates = {
        from: g_settings.analytics.dates.from,
        to: g_settings.analytics.dates.to
    }


    // initialize datepickers
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths:true,
        dateFormat: "M d, yy"
    }); 

    $('.datepicker-from')
        .datepicker('option', 'onSelect', function(date) {
            dates.from = date;
        })
        .val(dates.from);

    $('.datepicker-to')
        .datepicker('option', 'onSelect', function(date) {
            dates.to = date;
        })
        .val(dates.to);


    var table_source = $('#table-template').html();
    var table_hidden_phone_source = $('#table-hidden-phone-template').html();
    var table_visible_phone_source = $('#table-visible-phone-template').html();
    var web_source = $('#web-template').html();
    var data_source = $('#data-template').html();
    var alert_source = $('#alert-template').html();

    // dates filter "Apply" button
    var $filter = $('.filter');

    var $dates = $('.data');

    // data container
    var $container = $('.ga_data');

    // traffic type menu
    var $menu = $('.media-list');

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
    $filter.on('click', function() {
        var $self = $(this);
        if ($self.hasClass('disabled')) {
            return false;
        }
        $self.addClass('disabled');
        request();
    });


    // request on page load
    request();



    function request() {
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
            hidden : $container.find('.hidden-phone .tablebox-inner').first(),
            visible : $container.find('.visible-phone .tablebox-inner').first()
        };

        var $caption_hidden = $container.find('.hidden-phone .sectionTitle').first();
        var $caption_visible = $container.find('.visible-phone .sectionTitle').first();

        var xhr_req = $.post(
            g_settings.analytics.request_url + '/' + global_hash,
            dates,
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {

                        var hidden_template = Handlebars.compile(table_hidden_phone_source);
                        var visible_template = Handlebars.compile(table_visible_phone_source);

                        var hidden_html = hidden_template(response);
                        var visible_html = visible_template(response);

                        $tablebox.hidden.removeClass('loading').html(hidden_html);
                        $tablebox.visible.removeClass('loading').html(visible_html);

                        $caption_hidden.html(response.caption);
                        $caption_visible.html(response.caption);

                    } else {
                        _draw_error(response.error, $tablebox);
                    }
                } else {
                    _draw_error('Invalid request.', $tablebox);
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
        var xhr_req = $.post(
            g_settings.analytics.request_url + '/web/chart',
            dates,
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {
                        $chartbox.height(300);
                        _chart(response.result);
                    } else {
                        _draw_error(response.error, $chartbox);
                    }
                } else {
                    _draw_error('Invalid request.', $chartbox);
                }
            },
            'json'
        );
        return xhr_req;
    }



    function _request_web_data() {
        var $databox = $('.reviews-list');
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

                            $databox.removeClass('loading').html(data_html);

                        } else {
                            _draw_error('No results.', $databox);
                        }

                    } else {
                        _draw_error(response.error, $databox);
                    }
                } else {
                    _draw_error('Invalid request.', $databox);
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
                $con[item].removeClass('loading').html(data_html);
            }   
        } else {
            $con.removeClass('loading').html(data_html);
        }
    }

    google.load("visualization", "1", {packages:["corechart"]});


   // draw chart for web data visits
    function _chart(response) {

        var data = [['x','traffic']];

        // create highcharts-friendly array
        for(item in response) {
            var value = response[item];
            data.push([new Date(
                                parseInt(value[0], 10),
                                parseInt(value[1], 10) - 1,
                                parseInt(value[2], 10)
                                ),
                        parseInt(value[3], 10)
                    ]);
        }

        google.setOnLoadCallback(draw_chart(data, 'chartbox'));


    }

    /**
     * Return min and max vAxis value and data for chart
     *
     * @param data
     * @returns object
     */
    function rangeData(data){
        var min = data[1][1];
        var max = min;
        for(item in data) {
            if (!item == 0) {
                if (min > data[item][1]){
                    min = data[item][1];
                }
                if (max < data[item][1]){
                    max = data[item][1];
                }
            }

        }
        if (min == max) {
            if (max == 0) {
                max = 1;
                min = -1;
            } else {
                max = max*2;
                min = 0;
            }
        } else {
            max = parseInt(1.25*max);
            min = 0;
        }

        return {'min': min, 'max': max, 'data': data};
    }

    /**
     * Chart diagramm
     *
     * @param dataChart
     * @param chart_id
     */
    function draw_chart(dataChart, chart_id) {

        var dataLen = dataChart.length - 1;
        if (dataLen && !(dataChart[1][0] == '')) {
            var dataObj = rangeData(dataChart);
            var data = google.visualization.arrayToDataTable(dataObj.data);
            var options = {
                titlePosition: 'in',
                explorer:{axis:'horizontal'},
                legend:{position:'none'},
                width:640,
                height:320,
                chartArea:{left:60, top:20, width:580, height:250},
                hAxis:{minTextSpacing:10, slantedText:true, viewWindowMode: 'explicit', format:'dd.MM.y', gridlines:{count: 4}},
                vAxis: {minValue: 0, viewWindowMode: 'explicit', minValue: dataObj.min, maxValue: dataObj.max},
                interpolateNulls: false
            };


            var chart = new google.visualization.LineChart(document.getElementById(chart_id));
            chart.draw(data, options);

        } else {
            $('#'+chart_id).html('<div style="text-align: center"><b>No data available<b></div>');
        }


    }

})(jQuery);