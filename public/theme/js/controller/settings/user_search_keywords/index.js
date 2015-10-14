jQuery(function($){
    var keyword_source = $('#keyword-template').html();
    var $formbox = $('#user-search-keywords');
    var $mainblock = $('.main_block');
    var $add = $('.user_search_keywords_add_btn');

    $('.config_checkbox').on('change', function() {
        var $this = $(this);
        var val = (this.checked) ? 1 : 0;
        $.ajax({
            url: g_settings.base_url + '/settings/user_search_keywords/updateUserConfig',
            type: 'POST',
            data: 'key=' + $this.data('key') + '&token_id=' + $this.data('id') + '&value=' + val,
            beforeSend: function() {
                wait();
            },
            success: function(response) {
                response = JSON.parse(response);
                if(response.success) {
                    showFlashSuccess(response.message);
                } else {
                    showFlashErrors(response.error);
                }
            },
            complete: function() {
                stopWait();
            }
        });
    });

    $('.config_input').on('change', function() {
        var $this = $(this);
        var val = $this.val();
        $.ajax({
            url: g_settings.base_url + '/settings/user_search_keywords/updateUserConfig',
            type: 'POST',
            data: 'key=' + $this.data('key') + '&token_id=' + $this.data('id') + '&value=' + val,
            beforeSend: function() {
                wait();
            },
            success: function(response) {
                response = JSON.parse(response);
                if(response.success) {
                    showFlashSuccess(response.message);
                } else {
                    showFlashErrors(response.error);
                }
            },
            complete: function() {
                stopWait();
            }
        });
    });

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
        $formbox.find('.user_search_keywords_block').last().checkBo();
    });

    showKeywordProperties($mainblock, '.show_include_exclude', '.toggle_include_exclude');
    showKeywordProperties($mainblock, '.show_followers', '.followers_block');
    showKeywordProperties($mainblock, '.show_time', '.time_block');

    $formbox.on('click', '.user_search_keywords_delete', function(ev) {
        $(this).closest('.user_search_keywords_block').remove();
        $add.removeClass('disabled');
    });
});
