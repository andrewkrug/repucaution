(function($) {

    // get default dates
    var dates = {
        from: g_settings.reports.dates.from,
        to: g_settings.reports.dates.to
    }

    /**
     * Twitter datepickers form
     *
     * @type {*|HTMLElement}
     */
    var $twitter_dates_filter_form = $('#twitter-dates');

    /**
     * Facebook datepickers form
     *
     * @type {*|HTMLElement}
     */
    var $facebook_dates_filter_form = $('#facebook-dates');
    
    /**
     * Google datepickers form
     *
     * @type {*|HTMLElement}
     */
    var $google_dates_filter_form = $('#google-dates');
    
    /**
     * 'None-results' error container
     *
     * @type {*}
     */
    var alert_source = $('#alert-template').html();

    /**
     * Div contains facebook diagramm
     *
     * @type {*|HTMLElement}
     */
    var $facebook_diagram_container = $('#chartbox-facebook');

    /**
     * Div contains twitter diagramm
     *
     * @type {*|HTMLElement}
     */
    var $twitter_diagram_container = $('#chartbox-twitter');
    
    /**
     * Div contains google diagramm
     *
     * @type {*|HTMLElement}
     */
    var $google_diagram_container = $('#chartbox-google');
    
    /**
     * Show count of facebook likes in selected period
     *
     * @type {*|HTMLElement}
     */
    var $likes_count_num = $('#likes-count');

    /**
     * Show count of twitter followers in selected period
     *
     * @type {*|HTMLElement}
     */
    var $followers_count_num = $('#followers-count');
    
    /**
     * Show count of google friends in selected period
     *
     * @type {*|HTMLElement}
     */
    var $friends_count_num = $('#friends-count');

    // initialize datepickers
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths:true,
        dateFormat: "yy-mm-dd"
    });

    $('.datepicker-from').datepicker('option', 'onSelect', function(date) {
        dates.from = date;
    }).val(dates.from);

    $('.datepicker-to').datepicker('option', 'onSelect', function(date) {
        dates.to = date;
    }).val(dates.to);


    /**
     * Send request to social/reports/get_chart_data
     * And get JSON data with data for charts / likes, followers, friends count
     * Create and return array of this values
     *
     * @param post_data
     * @return {Array}
     */
    function get_chart_data(post_data)  {
        var chart_data = [];
        chart_data['facebook'] = [];
        chart_data['twitter'] = [];
        chart_data['google'] = [];
        chart_data['likes_count'] = 0;
        chart_data['followers_count'] = 0;
        chart_data['friends_count'] = 0;
        $.ajax({
            async: false,
            url:  g_settings.base_url + 'social/reports/get_chart_data',
            type: 'POST',
            data: post_data,
            dataType: 'JSON',
            success: function(response) {
                var days_counter = 0;
                chart_data['twitter'] = [['x', 'followers']];
                for (var item in response.twitter) {
                    if(item.length) {
                        chart_data['twitter'][days_counter+1] = [new Date(item), parseInt(response.twitter[item])];
                    }
                    days_counter++;
                }
                days_counter = 0;
                chart_data['facebook'] = [['x', 'likes']];
                for (var item in response.facebook) {
                    if(item.length) {
                        chart_data['facebook'][days_counter+1] = [new Date(item), parseInt(response.facebook[item])];
                    }
                    days_counter++;
                }
                days_counter = 0;
                chart_data['google'] = [['x', 'people']];
                for (var item in response.google) {
                    if(item.length) {
                        chart_data['google'][days_counter+1] = [new Date(item), parseInt(response.google[item])];
                    }
                    days_counter++;
                }
                chart_data['likes_count'] = response.likes_count;
                chart_data['followers_count'] = response.followers_count;
                chart_data['friends_count'] = response.friends_count;
            }
        });
        return chart_data;
    }

    /**
     * Change dates range for twitter chart
     * Get data and draw diagramm
     */
    $twitter_dates_filter_form.on('submit', function() {
        var $self = $(this);
        var data = get_chart_data( $self.serialize() );

        $followers_count_num.html(data['followers_count']);

        if(data['twitter'].length) {
            draw_chart(data['twitter'], 'chartbox-twitter');
        } else {
            _draw_error('No results', $twitter_diagram_container);
        }
        return false;
    });

    /**
     * Change dates range for facebook chart
     * Get data and draw diagramm
     */
    $facebook_dates_filter_form.on('submit', function() {
        var $self = $(this);
        var data = get_chart_data( $self.serialize() );

        $likes_count_num.html(data['likes_count']);

        if(data['facebook'].length) {
            draw_chart(data['facebook'], 'chartbox-facebook');
        } else {
            _draw_error('No results', $facebook_diagram_container);
        }

        return false;
    });
    
    /**
     * Change dates range for google chart
     * Get data and draw diagramm
     */
    $google_dates_filter_form.on('submit', function() {
        var $self = $(this);
        var data = get_chart_data( $self.serialize() );

        $likes_count_num.html(data['friends_count']);

        if(data['google'].length) {
            draw_chart(data['google'], 'chartbox-google');
        } else {
            _draw_error('No results', $facebook_diagram_container);
        }

        return false;
    });


    /**
     * Onload Event
     */
    function init() {

        /**
         * On-page load - get data for all socials
         *
         * @type {Array}
         */
        var all_user_data = get_chart_data(null);

        /**
         * Select twitter data from all datas
         *
         * @type {*}
         */
        var twitter_chart_data = all_user_data['twitter'];

        /**
         * Select facebook data from all datas
         *
         * @type {*}
         */
        var facebook_chart_data = all_user_data['facebook'];
        
        /**
         * Select google data from all datas
         *
         * @type {*}
         */
        var google_chart_data = all_user_data['google'];

        /**
         * Draw twitter followers chart
         *
         * @type {*}
         */
        draw_chart(twitter_chart_data, 'chartbox-twitter', 'Twitter Followers');

        /**
         * Draw facebook page likes chart
         *
         * @type {*}
         */
        draw_chart(facebook_chart_data, 'chartbox-facebook', 'Facebook Likes');
        
        /**
         * Draw google page friends chart
         *
         * @type {*}
         */
        draw_chart(google_chart_data, 'chartbox-google', 'Google Friends');

        /**
         * ... and set number of followers into the page
         */
        $followers_count_num.html(all_user_data['followers_count']);

        /**
         * ... and set number of likes into the page
         */
        $likes_count_num.html(all_user_data['likes_count']);
        
        /**
         * ... and set number of friends into the page
         */
        $friends_count_num.html(all_user_data['friends_count']);
        

        if(all_user_data['followers_count'] == 0) {
            _draw_error('No results', $twitter_diagram_container);
        }

        if(all_user_data['likes_count'] == 0) {
            _draw_error('No results', $facebook_diagram_container);
        }
        
        if(all_user_data['friends_count'] == 0) {
            _draw_error('No results', $google_diagram_container);
        }
    }


    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(init);

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
                height:350,
                chartArea:{left:50, top:20, width:580, height:280},
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

    /**
     * Create some error
     * Ex. - if we not have data for selected period
     *
     * @param error
     * @param $con
     * @private
     */
    function _draw_error(error, $con) {
        var data_template = Handlebars.compile(alert_source);
        var data_context = {
            type: 'error',
            message: error
        };
        var data_html = data_template(data_context);
        $con.html(data_html);
    }

})(jQuery);