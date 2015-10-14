// google.load('visualization', '1', {packages: ['gauge']});

jQuery(function ($) {

    /**
     * Show if function loadReviews in process
     *
     * @type {bool}
     */
    var inProcess = false;

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('#loading');

    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        dateFormat: g_settings.date_format.datepicker
    });


    /*var templates = {
      '1st': _.template( $('#1st_section').html() ),
      '2nd':  _.template( $('#2nd_section').html() ),
      '3d':  _.template( $('#3d_section').html() )
    };*/

    var stars = g_settings.directory.stars;

    //date_to_text();

    /*get_directory_details();*/

    $('#all-period').click(function(){
        $('.datepicker').datepicker( "setDate" , null );
        get_directory_details();
    });
    $('#apply_button').click(function(){
        $('#review-list .block_content').remove();
        loadReviews();
    });

    /**
     * Get date from datepicker - 'FROM'
     *
     * @returns Date
     */
    function get_date_from() {
        var date_from_el = $('#date_from');
        return date_from_el.datepicker("getDate");
    }

    /**
     * Get date from datepicker - 'TO'
     *
     * @returns Date
     */
    function get_date_to() {
        var date_to_el = $('#date_to');
        return date_to_el.datepicker("getDate");
    }

    /**
     * Cinvert Date object to string
     *
     * @param date
     * @returns string
     */
    function date_to_str(date) {
        return $.datepicker.formatDate(g_settings.date_format.string, date);
    }

    /**
     * Cinvert Date object to datepicker format string
     *
     * @param date
     * @returns string
     */
    function date_to_dp_format(date){
        return $.datepicker.formatDate(g_settings.date_format.datepicker, date);
    }

    /**
     * Convert date from datepickers to text and insert to dom element
     */
    function date_to_text() {

        var date_text_el = $('#text_data');

        var from_str = date_to_str(get_date_from());
        var to_str = date_to_str(get_date_to());

        date_text_el.html(from_str + ' - ' + to_str);
    }

    function get_directory_details() {

        var container = $('#directory-info');
        if(container.hasClass('loading')  ){
            return;
        }
        container.empty().addClass('loading');

        var data = {
            from: date_to_dp_format(get_date_from()),
            to: date_to_dp_format(get_date_to()),
            directory: g_settings.directory.id
        };

        $.post(g_settings.directory.details_url, data, render_page,'json').always(function(){
                container.removeClass('loading');
        });

    }



    function render_page(data){

        var container = $('#directory-info');

        var rank = data.count ? data.rank : 0;

        container.append(
            templates['1st']({
                total:data.count,
                next: g_settings.directory.nav.next,
                prev: g_settings.directory.nav.prev,
                reviews: data.latest_reviews,
                rank:data.rank_details,
                show_all_link: g_settings.directory.show_all_link,
                dir_link: g_settings.directory.link,
                overall : {
                    max: stars,
                    value: rank,
                    percent: parseFloat((rank / stars).toFixed(1)) * 100,
                    pointPercent: parseFloat(Math.floor((100 / (stars + 1))))
                }
            })
        );

        // if(stars > 0){
            // drawVisualization(data.rank,stars);
        // }

        if(stars > 0) {

        //     var max = stars;
        //     var value = data.rank;

        //     console.log(value, max);
            


            updateRate();
        }




        container.append( templates['3d']({
         total: data.count,
         difference: data.diff
         }) );
         draw_chart(data.monthly_trending);

    }

    function updateRate()
    {
        $('.rating-box').each(function(){
            var $this = $(this);

            $this.raty({
                numberMax: stars,
                score: $this.data('rank'),
                path:g_settings.base_url+'public/images/raty/',
                readOnly: true,
                hints: _.times(stars, function(){ return null; } )
            });
        });
    }



    /**
     * Load mentions with offset
     */
    function loadReviews() {

        inProcess = true;
        $ajax_container = $('.review-list');
        $('#loading').removeClass('hidden');

        var data = 'directory='+g_settings.directory.id+'&offset='+$('.recent_review').length;
        from  = date_to_str(get_date_from());
        to = date_to_str(get_date_to());
        if (from && to) {
            data += '&from=' + from + '&to=' + to;
        }

        $.ajax({
            url: g_settings.base_url+'reviews/load',
            type: 'GET',
            data: data,
            success: function(response) {

                if(response.length) {

                    $(response).insertBefore($('#loading'));
                    $loading.addClass('hidden');
                    //updateRate();
                    inProcess = false;
                } else {
                    $loading.addClass('hidden');
                }

            }
        });

    }

    /**
     * Check window position
     */
    $(window).scroll(function() {
        var lastReview = $('.recent_review').last();
        if (lastReview.position()) {
            var top = lastReview.position().top;
            if (($(window).scrollTop()+$(window).height())+10 >= top && !inProcess){
                loadReviews();
            }
        }

    });

    /**
     * Draw Gauge
     * @param rank
     */
    /*function drawVisualization(rank, max_rank) {
        // Create and populate the data table.
        min_rank = 0;
        max_rank || (max_rank = 5);


        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Rank', rank]
        ]);


        var options = {
            width: 200, height: 200,
            redFrom: min_rank, redTo: (max_rank *0.376),
            yellowFrom: (max_rank *0.376), yellowTo: (max_rank *0.624),
            greenFrom: (max_rank *0.624), greenTo: max_rank,
            max:max_rank,
            min:min_rank
        };

        // Create and draw the visualization.
        new google.visualization.Gauge(document.getElementById('gauge-container')).
            draw(data,options);
    }*/

    /**
     * Draw chart
     *
     * @param data
     * @returns {Highcharts.Chart}
     */
    /*function draw_chart(data){

        var data_length = data.length;

        if(!data_length){
            return;
        }

        for(i=0;i<data_length; i++) {
            var value = data[i];
            data[i][1] = parseInt(value[1], 10);
            var converted_time = Date.parse(value[0]);
            if(_.isObject(converted_time)){
                data[i][0] = converted_time.getTime();
            }else{
                data[i][0] = converted_time;
            }

        }

        return new Highcharts.Chart({
            chart: {
                renderTo: 'chart-container',
                type: 'area'
            },
            credits: {
                enabled: false
            },
            title: {
                text: null
            },
            xAxis: {
                type: 'datetime',
                offset: 10,
                minTickInterval: 24 * 3600 * 1000
            },
            yAxis: {
                min: 0,
                title: {
                    text: null
                },
                reversed: false,
                minTickInterval:1
            },
            plotOptions: {
                area: {
                    color: '#008cd6',
                    fillColor: '#ebf5fb',
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'Reviews',
                data: data,
            }]
        });
    }*/



});