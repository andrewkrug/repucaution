(function($) {
    $(document).ready(function() {
        drawCharts();
    });

    function drawCharts() {
        traffic_draw_chart(g_settings.monthly_trending.traffic, 'chart-web-traffic');
        twitter_draw_chart(g_settings.monthly_trending.twitter_followers, 'chart-twitter-followers');
        reviews_draw_chart(g_settings.monthly_trending.reviews, 'chart-reviews');
        keywords_draw_chart(g_settings.monthly_trending.keywords, 'chart-google-rank');
        facebook_draw_chart(g_settings.monthly_trending.facebook_likes, 'chart-facebook-likes');
    }

    radarContent();

    /**
     * Draw facebook likes chart
     *
     * @param data
     * @param {string} id of element
     */
    function facebook_draw_chart(data, id){
        var days_counter = 0;
        var dataChart = [];
        for (var item in data) {
            if(data.hasOwnProperty(item) && item.length) {
                var value = item.split('-');
                dataChart.push([Date.UTC(value[0], parseInt(value[1])-1, value[2]), parseInt(data[item])]);
            }
            days_counter++;
        }
        draw_chart(dataChart, id, lang('fb_likes'));
    }

    /**
     * Draw twitter followers chart
     *
     * @param data
     * @param {string} id of element
     */
    function twitter_draw_chart(data, id){
        var days_counter = 0;
        var dataChart = [];
        for (var item in data) {
            if(data.hasOwnProperty(item) && item.length) {
                var value = item.split('-');
                dataChart.push([Date.UTC(value[0], parseInt(value[1])-1, value[2]), parseInt(data[item])]);
            }
            days_counter++;
        }
        draw_chart(dataChart, id, lang('twiter_followers'));
    }


    /**
     * Draw reviews chart
     *
     * @param input
     * @param {string} id
     * @returns {Highcharts.Chart}
     */
    function reviews_draw_chart(input, id){
        var data = _.cloneDeep(input);
        var data_length = data.length;
        var dataChart = [];

        for(var i=0;i<data_length; i++) {
            var _value = data[i];
            var value = _value[0].split('-');
            dataChart.push([Date.UTC(value[0], parseInt(value[1])-1, value[2]), parseInt(_value[1])]);

        }
        draw_chart(dataChart, id, lang('reviews'));
    }


    /**
     * Draw Web traffic chart
     * @param response
     * @param {string} id
     * @returns {Highcharts.Chart}
     * @private
     */
    function traffic_draw_chart(response, id) {
        var dataChart = [];
        // create highcharts-friendly array
        for(var item in response) {
            if(response.hasOwnProperty(item)) {
                var value = response[item];
                dataChart.push([Date.UTC(value[0],parseInt(value[1])-1,value[2]), parseInt(value[3])]);
            }
        }
        draw_chart(dataChart, id, lang('web_traffic'));
    }

    /**
     * Draw keywords rank chart
     *
     * @param input
     * @param {string} id of element
     */
    function keywords_draw_chart(input, id){
        var temp_data = _.cloneDeep(input);
        var temp_data_length = temp_data.length;
        var dataChart = [];

        for(var i=0; i< temp_data_length; i++) {
            var _value = temp_data[i];
            var value = _value['date'].split('-');
            dataChart.push([Date.UTC(value[0], parseInt(value[1])-1, value[2]), parseFloat(_value['value'])]);
        }
        draw_chart(dataChart, id, lang('google_rank'));
    }

    $(window).resize(function() {
        drawCharts();
    });

})(jQuery);
