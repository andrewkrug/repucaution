jQuery(function($){
    var max_daily_auto_follow_users_by_search = $('input[name="max_daily_auto_follow_users_by_search"]');

    max_daily_auto_follow_users_by_search.spinedit({
        minimum: 0,
        maximum: 100,
        step: 1,
        numberOfDecimals: 0,
        value: parseInt(g_settings.max_daily_auto_follow_users_by_search)
    });

    max_daily_auto_follow_users_by_search.on('valueChanged', function(e) {
        clearAlerts();
        var val = e.value;
        $.post(
            location.href+'/maxAutoDailyFollowUsersBySearch',
            {max_daily_auto_follow_users_by_search: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $('input[name="auto_search_and_follow_twitter"]').on('ifChanged', function(e) {
        clearAlerts();
        var val = (e.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autoSearchAndFollowTwitter',
            {auto_follow_users_by_search_twitter: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $('input', '.min-max-followers-box').each(function() {
       $(this).spinedit('setValue', $(this).attr('value'));
    });

    var keyword_source = $('#keyword-template').html();
    var $formbox = $('.formBox .control-group');
    var $add = $('.user_search_keywords_add_btn');

    $add.on('click', function(ev) {
        var keywords_count = $formbox.find('.user_search_keywords_block').length;
        if (keywords_count >= g_settings.max_keywords) {
            $(this).addClass('disabled');
        }
        if ($(this).hasClass('disabled')) {
            return false;
        }
        var index = keywords_count + 1;
        var template = Handlebars.compile(keyword_source);
        var html = template({
            index: index,
            id: 'new_' + index
        });
        $formbox.append(html);
        $formbox.children().last().find('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_minimal-grey',
            radioClass: 'iradio_minimal-grey',
            increaseArea: '20%' // optional
        });
        $('#user_search_keywords_min_followers_new_'+index, $formbox).spinedit({
            minimum: 0,
            maximum: 100000,
            step: 10,
            numberOfDecimals: 0
        });
        $('#user_search_keywords_max_followers_new_'+index, $formbox).spinedit({
            minimum: 0,
            maximum: 10000000,
            step: 10,
            numberOfDecimals: 0
        });
        $(".select_block").each(function(indx){
            var self = $(this);
            $(this).ddslick({
                width: self.data('width') || 174,
                height: self.data('height') || null
            })
        });

    });

    function resort(){
        var i = 1;
        $('.user_search_keywords_block').each(function(){
            $(this).find('.num').html(i+'.');
            i++;
        });
    }

    $formbox.on('click', '.include-exclude', function() {
        $(this).parents('.section-box').children('.include-exclude-box').toggle( "blind", 500);
        $(this).parents('.section-box').siblings('.section-box').children('.include-exclude-box').slideUp(500);
        $(this).toggleClass('closed');
    });

    $formbox.on('click', '.min-max-followers', function() {
        $(this).parents('.section-box').children('.min-max-followers-box').toggle( "blind", 500);
        $(this).parents('.section-box').siblings('.section-box').children('.min-max-followers-box').slideUp(500);
        $(this).toggleClass('closed');
    });

    $formbox.on('click', '.follow-time', function() {
        $(this).parents('.section-box').children('.follow-time-box').toggle( "blind", 500);
        $(this).parents('.section-box').siblings('.section-box').children('.follow-time-box').slideUp(500);
        $(this).toggleClass('closed');
    });

    $formbox.on('click', '.user_search_keywords_delete', function(ev) {
        $(this).closest('.user_search_keywords_block').remove();
        $add.removeClass('disabled');
        resort();
    });

    /**
     * Clear flash messages
     */
    function clearAlerts()
    {
        var messages = $('div.container').find('div.message');
        if (messages.length) {
            messages.each(function(){
                $(this).parent().remove();
            });
        }
    }

    /**
     * Return html of error message
     *
     * @param text
     */
    function errorHtml(data){
        var success = 'success';
        if (!data.success) {
            success = 'error';
        }
        return '<div class="message-'+success+' alert-'+success+'">'+
            '<div class="message"> <i class="icon"></i> <span>'+data.message+'</span>'+
            '</div>';

    }
});
