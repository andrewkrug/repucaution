$(document).ready(function() {

    var $analytics_period = $('#analytics_period');
    var access_token_id = $('#access_token_id').val();

    var updateAnalytics = function() {
        wait();
        $.ajax({
            async: false,
            url:  g_settings.base_url + 'settings/socialmedia/get_analytics_data',
            type: 'POST',
            data: {
                period: $analytics_period.val(),
                access_token_id: access_token_id
            },
            dataType: 'JSON',
            success: function(response) {
                console.log(response);
                for (var key in response) {
                    var data = [];
                    if (response.hasOwnProperty(key)) {
                        for(var date in response[key]) {
                            if(response[key].hasOwnProperty(date)) {
                                var _date = date.split('-');
                                data.push([
                                    Date.UTC(_date[0], parseInt(_date[1])-1, _date[2]),
                                    parseInt(response[key][date])
                                ]);
                            }
                        }
                        var title = '';
                        switch (key) {
                            case 'followers':
                                title = lang('analytics_followers_count');
                                break;
                            case 'retweets_count':
                                title = lang('analytics_retweets_count');
                                break;
                            case 'favourites_count':
                                title = lang('analytics_favourites_count');
                                break;
                            case 'new_following_by_search_count':
                                title = lang('analytics_new_following_by_search_count');
                                break;
                            case 'new_following_count':
                                title = lang('analytics_new_following_count');
                                break;
                            case 'new_unfollowers_count':
                                title = lang('analytics_new_unfollowers_count');
                                break;
                            case 'new_unfollowing_count':
                                title = lang('analytics_new_unfollowing_count');
                                break;
                        }
                        draw_chart(data, 'chartbox-'+key, title);
                    }
                }
            },
            complete: function() {
                stopWait();
            }
        });
    };

    updateAnalytics();

    $analytics_period.on('change', function() {
        updateAnalytics();
    });

});