(function($) {

    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawCharts);

    function drawCharts() {
        reviews_draw_chart(g_settings.monthly_trending.reviews, 'chart-reviews');
        traffic_draw_chart(g_settings.monthly_trending.traffic, 'chart-web-traffic');
        keywords_draw_chart(g_settings.monthly_trending.keywords, 'chart-google-rank');
        twitter_draw_chart(g_settings.monthly_trending.twitter_followers, 'chart-twitter-followers');
        facebook_draw_chart(g_settings.monthly_trending.facebook_likes, 'chart-facebook-likes');
    }

    var $content_box = $('.contentBox').hide();
    $content_box.first().show();

    var $left_menu_block_items = $('.left_menu-block li');
    $left_menu_block_items.first().addClass('active').show();


    $left_menu_block_items.on('click', function(e) {
        e.preventDefault();
        var $self = $(this);
        $left_menu_block_items.removeClass('active');
        $self.addClass('active');

        $content_box.hide();
        var active_tab = $self.find('a').attr('href');
        $(active_tab).fadeIn();

    });

    /**
     * Draw facebook likes chart
     *
     * @param data
     * @param string id of element
     */
    function facebook_draw_chart(data, id){
        var days_counter = 0;
        var dataChart = [['x', 'likes']];
        for (var item in data) {
            if(item.length) {

                dataChart[days_counter+1] = [new Date(item), parseInt(data[item])];
            }
            days_counter++;
        }
        social_chart(dataChart, id, 'Facebook Likes');
    }

    /**
     * Draw twitter followers chart
     *
     * @param data
     * @param string id of element
     */
    function twitter_draw_chart(data, id){
        var days_counter = 0;
        var dataChart = [['x', 'followers']];
        for (var item in data) {
            if(item.length) {
                dataChart[days_counter+1] = [new Date(item), parseInt(data[item])];
            }
            days_counter++;
        }
        social_chart(dataChart, id, 'Twitter Followers');
    }


    /**
     * Draw reviews chart
     *
     * @param data
     * @returns {Highcharts.Chart}
     */
    function reviews_draw_chart(input, id){
        var data = _.cloneDeep(input);
        var data_length = data.length;
        var dataChart = [['x', 'reviews']];

        for(i=0;i<data_length; i++) {
            var value = data[i];
            dataChart[i+1] = [new Date(value[0]),parseInt(value[1])];

        }
        social_chart(dataChart, id, 'Reviews');
    }


    /**
     * Draw Web traffic chart
     * @param response
     * @returns {Highcharts.Chart}
     * @private
     */
    function traffic_draw_chart(response, id) {
        var dataChart = [['x', 'traffic']];
        // create highcharts-friendly array
        for(item in response) {
            var value = response[item];
            dataChart.push([new Date( value[0]+"-"+
                                      value[1]+"-"+
                                      value[2]
                                    ),
                                    parseInt(value[3])]);
        }
        social_chart(dataChart, id, 'Web Traffic');
    }

    /**
     * Draw keywords rank chart
     *
     * @param input
     * @param string id of element
     */
    function keywords_draw_chart(input, id){
        var temp_data = _.cloneDeep(input);
        var temp_data_length = temp_data.length;
        var dataChart = [['x', 'rank']];

        for(i=0; i< temp_data_length; i++) {
            var value = temp_data[i];
            dataChart[i+1] = [new Date(value['date']), parseFloat(value['value'])];
        }
        social_chart(dataChart, id, 'Rank');
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
     * @param title
     */
    function social_chart(dataChart, chart_id, title) {

        var dataLen = dataChart.length - 1;
        if (dataLen && !(dataChart[1][0] == '')) {
            var dataObj = rangeData(dataChart);
            var data = google.visualization.arrayToDataTable(dataObj.data);
            var options = {
                title: title,
                titlePosition: 'in',
                explorer:{axis:'horizontal'},
                legend:{position:'none'},
                width:315,
                height:400,
                chartArea:{left:50, top:20, width:250, height:320},
                hAxis:{minTextSpacing:10, slantedText:true, viewWindowMode: 'explicit', format:'dd.MM.y', gridlines:{count: 4}},
                vAxis: {minValue: 0, viewWindowMode: 'explicit', minValue: dataObj.min, maxValue: dataObj.max},
                interpolateNulls: false
            };


            var chart = new google.visualization.LineChart(document.getElementById(chart_id));
            chart.draw(data, options);

        } else {
            $('#'+chart_id).html('<div style="text-align: center"><b>Nothing happened during last month.<b></div>');
        }


    }


})(jQuery);
