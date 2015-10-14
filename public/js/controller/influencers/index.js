(function($) {

     
    /**
     * Show if function loadMentions in process
     *   
     * @type {bool}
     */
    var inProcess = false;
    
    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');
    
    /**
     * Container of facebook loaded posts
     * @type {*|HTMLElement}
     */
    var $container = $('.main-list');
    
    /**
     * Button in modal reply dialog ('Reply' text)
     *
     * @type {*|HTMLElement}
     */
    var $reply_button = $('#reply');

    /**
     * Modal reply window
     *
     * @type {*|HTMLElement}
     */
    var $reply_window = $('#reply-window');

    /**
     * Contains reply text in modal reply window
     *
     * @type {*|HTMLElement}
     */
    var $reply_area = $('.twitter_reply_textarea');

    /**
     * 'Cancel' button in modal reply window
     * Hide modal window on click
     *
     * @type {*|HTMLElement}
     */
    var $reply_cancel = $('#cancel-reply-area');

    /**
     * Button of 'all twitter feed'
     *
     * @type {*|HTMLElement}
     */
    var $twitter_feed = $('#twitter-feed');

    /**
     * Button of 'mentions feed'
     *
     * @type {*|HTMLElement}
     */
    var $mentions = $('#mentions');

    /**
     * Button of 'my feed'
     *
     * @type {*|HTMLElement}
     */
    var $my_feed = $('#my-feed');
    
    /**
     * Feed tweets container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');
    
       
    var $twitter_tabs = $('.twitter-tabs');

    g_settings.timeline_type = 'feed';
    
    // highlight keywords
    var keywords = g_settings.keywords;

    highlight();


    /**
     * Highlight keyword in mentions content
     */
    function highlight(){
        $('#ajax-area .data p.ment-text').each(function() {

            var text = $(this).text();


            for(var key = 0; key < keywords.length; key++) {

                var keyword = keywords[key]['keyword'],
                    exact = keywords[key]['exact'] == '1',
                    words = [];

                // if 'exact' option is not enabled use case-insensitive search
                var match_options = exact ? 'g' : 'gi';

                if (exact) {
                    words = [keyword];
                } else {
                    // split keyword into words by whitespace if 'exact' option is not enabled
                    words = keyword.split(new RegExp('\\s+'));
                }

                for (var w = 0; w < words.length; w++) {
                    var matched = text.match(new RegExp(escape_regexp(words[w]), match_options));
                    for(var m in matched) {
                        if(!isNaN(m)){
                            text = text.replace(matched[m], '<span class="developed">' + matched[m] + '</span>' );
                        }
                    }
                }
            }

            $(this).html(text);
        });
    }

    function escape_regexp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    var $select = $('[name="keyword"]');
    $select.ddslick({
        width: 174,
        defaultSelectedIndex: 0,
        onSelected: function(ev) {

            g_settings.keyword_query_id || (g_settings.keyword_query_id = 0);

            if ((window.location.search.indexOf('keyword') === -1
                && window.location.search.indexOf('from') === -1
                && window.location.search.indexOf('to') === -1
                && window.location.search.indexOf('page') === -1
                && ev.selectedData.value != g_settings.keyword_query_id
                && window.location.search.indexOf('social') === -1
                )
                || ev.selectedData.value != g_settings.keyword_query_id
                ) {
                window.location.href = window.location.pathname + '?keyword=' + ev.selectedData.value;
            }
        }
    });

    // initialize datepickers
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths:true,
        dateFormat: "m d, y"
    });

    // get default dates
    var dates = g_settings.dates;

    // changing dates on date select
    $('.datepicker-from')
        .datepicker('option', 'onSelect', function(date) {
            dates.from = date;
            filterDate();
        }).val(dates.from);


    // changing dates on date select
    $('.datepicker-to')
        .datepicker('option', 'onSelect', function(date) {
            dates.to = date;
            filterDate();
        })
        .val(dates.to);

    /**
     * Apply dates
     */
    $('#apply-dates').live('click', function(){
        var start = $('#start').val();
        var end = $('#end').val();
        $('#span-start').html(start);
        $('#span-end').html(end);
        $(".dp-holder").hide();
        filterDate();

    });

    $(document).ready(function(){
        var today = $.datepicker.formatDate('M d, yy', new Date());

        //calendar instance
        g_settings.calendar = new Timeframe('calendars', {
            startField: 'start',
            endField: 'end',
            latest: today,
            format:'MMM d, yyyy',
            resetButton: 'reset'});

        var changeDate = function(e){
            var from = $(this).val();
            g_settings.calendar.clearRange();
            g_settings.calendar.setRange(from, today);
        };

        //calendar dropdown
        $('.dp-holder select option').on('click', changeDate);
        $('.dp-holder select').on('change', changeDate);
    });

    // filter "Apply" buton click
    function filterDate() {
        var query_str = '?from=' + encodeURIComponent($('#start').val()) + '&to=' + encodeURIComponent($('#end').val());
        query_str += g_settings.keyword_query_str || '';
        query_str += g_settings.social_query_str || '';
        window.location.href = window.location.pathname + query_str;
    };
    /**
     * Display "Delete from influencer" link
     */
    $('.mentions-block').live('mouseover', function(){
        $(this).find('.remove-influencer').css('display','');
    }).live('mouseout', function(){
        $(this).find('.remove-influencer').css('display','none');
    });

    /**
     * Delete from influencers
     */
    $('.remove-influencer').live('click', function(){
        var self = $(this);
        var data = self.data();
        var self = $(this);
        var data = self.data();
        var $parent = self.parents('.mentions-block');
        $parent.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$parent.css('left')+'px',
            'top':$parent.css('left')+'px',
            'height':$parent.height()+'px',
            'width':$parent.width()+'px'});
        $.ajax({
            url: g_settings.base_url+'influencers/delete',
            type: 'post',
            dataType: 'json',
            data: data,
            success: function(response){
                if (response.success){
                    $('a.remove-influencer[data-creator_id='+data.creator_id+']').each(function (){
                        $(this).parents('.mentions-block').remove();
                    });
                }
            }
        });
    });

    /**
     * Check window position
     */
    $(window).scroll(function() {
        var lastMent = $('.mentions-block').last();
        var top = lastMent.position().top; 
        if (($(window).scrollTop()+$(window).height())+10 >= top && !inProcess){
            loadMentions();
        }
    });
    
    /**
     * Load mentions with offset
     */
    function loadMentions() {

        inProcess = true;
        $ajax_container = $('#ajax-area');
        $loading.removeClass('action-loading');
        $ajax_container.append($loading);
        var data = 'offset='+$('#ajax-area .data').length;
        data += '&from=' + $('#start').val() +
            '&to=' + $('#to').val();

        if (g_settings.keyword_query_id) {
            data += '&keyword='+ g_settings.keyword_query_id;
        }
        if (g_settings.social) {
            data += '&social='+ g_settings.social;
        }

        $.ajax({
            url: window.location.pathname,
            type: 'GET',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                var rhtml = response.html;
                if(rhtml.length) {
                    $loading.replaceWith(rhtml);
                    highlight();
                    inProcess = false;
                } else {

                        $loading.remove();

                }

            },
            complete: function() {
            }
        });

    }

    //FACEBOOK
    /**
     * 'Add new comment'-form submit
     */
    $container.on('submit', '.comment-submit-form', function() {
        var $self = $(this);
        var actions = $self.parents('.mentions-block').find('.action');
        if ($.trim($self.find('textarea').val()).length <= 0) {
            $self.find('textarea').focus();
            return false;
        }
        $parent = $self.parents('.new-comment');
        actions.find('.message-error').remove();
        $parent.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$parent.offset().left+'px',
            'top':$parent.offset().top+'px',
            'height':$parent.height()+'px',
            'width':$parent.width()+'px'});

        $.ajax({
            url: $self.attr('action'),
            type: 'POST',
            dataType:'json',
            data: $self.serialize(),
            success: function(response) {
                if (!response.error) {
                    var ul = $self.parent().prev();
                    ul.prepend(response.html);

                } else {
                    var errMsg = errorHtml(response.error);
                    actions.prepend(errMsg);
                    $(window).scrollTop($('.message-error').position().top);
                }
                $loading.remove();
                $parent.css('opacity', '');
                $self.find('textarea[name=message]').val('');
            }
        });

        return false;

    });

    /**
     * Like something on feed
     */
    $container.on('click', '.like-button', function() {
        var $self = $(this);
        var parent = $self.parent();
        var actions = $self.parents('.mentions-block').find('.action');
        actions.find('.message-error').remove();
        var container = ($self.hasClass('now')) ? $self.parents('li') : $self.parents('.mentions-block');
        container.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':container.offset().left+'px',
            'top':container.offset().top+'px',
            'height':container.height()+'px',
            'width':container.width()+'px'});

        $.ajax({
            url: $self.data('url') + '/' + $self.data('id'),
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(!status.error) {
                    /* var $likes_counter = $self.siblings('.like');
                     var current_count = parseInt($likes_counter.html());
                     current_count++;
                     $likes_counter.html( current_count ); */
                    $self.addClass('dislike-button');
                    $self.removeClass('like-button');
                    $self.html('Unlike');
                    $self.data('url', $self.data('url').replace('facebook_like', 'facebook_dislike'));
                } else {
                    var errMsg = errorHtml(status.error);
                    parent.prepend(errMsg);
                    $(window).scrollTop($('.message-error').position().top);
                }
                $loading.remove();
                container.css('opacity','');
            }
        });
    });

    /**
     * DisLike something on feed
     */
    $container.on('click', '.dislike-button', function() {
        var $self = $(this);
        var parent = $self.parent();
        var actions = $self.parents('.mentions-block').find('.action');
        actions.find('.message-error').remove();
        var container = ($self.hasClass('now')) ? $self.parents('li') : $self.parents('.mentions-block');
        container.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':container.offset().left+'px',
            'top':container.offset().top+'px',
            'height':container.height()+'px',
            'width':container.width()+'px'});

        $.ajax({
            url: $(this).data('url') + '/' + $self.data('id'),
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(status.success) {
                    /* var $likes_counter = $self.siblings('.like');
                     var current_count = parseInt($likes_counter.html());
                     current_count--;
                     $likes_counter.html( current_count ); */
                    $self.addClass('like-button');
                    $self.removeClass('dislike-button');
                    $self.html('Like');
                    $self.data('url', $self.data('url').replace('facebook_dislike', 'facebook_like'));
                } else {
                    var errMsg = errorHtml(status.error);
                    actions.prepend(errMsg);
                    $(window).scrollTop($('.message-error').position().top);
                }
                $loading.remove();
                container.css('opacity','');
            }
        });
    });

    /**
     * On first-load page -- on click on 'Comments' link - load comments using AJAX
     * After - show \ hide in using css 'display' property
     * Using to make page-load more faster
     */
    $container.on('click', '.show-comments', function() {
        var $self = $(this);
        $self.parents('.mentions-block').toggleClass('active-fold');
        var actions = $self.parents('.mentions-block').find('.action');
        actions.find('.message-error').remove();
        var $comments_container = $self.parents('.comment-panel').siblings('.fbComment').find('.comment-list');
        if ($comments_container.length <= 0) {
            $comments_container = $self.parents('.mentions-block').find('.comment-list');
        }
        switch($self.data('type')) {
            case 'not_loaded':
                //$loading.remove();
                $loading.removeClass('action-loading').css({'left':$comments_container.offset().left+'px',
                    'top':$comments_container.offset().top+'px',
                    'height':$comments_container.height()+'px',
                    'width':$comments_container.width()+'px'});
                $comments_container.html($loading);
                $.ajax({
                    url:  $self.data('url'),
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if(!response.error){
                            if(response.html.length){
                                $comments_container.html(response.html);
                            } else {
                                $comments_container.html("No comments");
                            }
                            $self.data('type', 'showed');
                            $comments_container.siblings('.new-comment').css('display', 'block');
                        }else{
                            var errMsg = errorHtml(response.error);
                            $self.data('type', 'showed');
                            $comments_container.html(errMsg);
                            $(window).scrollTop($('.message-error').position().top);
                        }

                    }
                });
                break;
            case 'showed':
                $comments_container.css('display', 'none');
                $comments_container.siblings('.new-comment').css('display', 'none');
                $self.data('type', 'hided');
                break;
            case 'hided':
                $comments_container.css('display', 'block');
                $comments_container.siblings('.new-comment').css('display', 'block');
                $self.data('type', 'showed');
                break;
        }
    });

    /**
     * Remove some comment from feed
     * And decrement comments counter
     */
    $(document).on('click', '.remove-butt', function() {
        var $self = $(this);
        var actions = $self.parents('.fbComment').prev();
        actions.find('.message-error').remove();
        var $removed_comment = $self.parents('li');
        var post_id = $self.parents('.mentions-block').find('.like-button').data('id');
        $removed_comment.css('opacity', '0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$removed_comment.offset().left+'px',
            'top':$removed_comment.offset().top+'px',
            'height':$removed_comment.height()+'px',
            'width':$removed_comment.width()+'px'});
        $.ajax({
            url: $self.data('url') + '/' + post_id,
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if(response.success) {
                    $removed_comment.remove();
                } else {
                    var errMsg = errorHtml(response.error);
                    actions.prepend(errMsg);
                    $removed_comment.css('opacity', '');
                    $loading.remove();
                    $(window).scrollTop($('.message-error').position().top);

                }
            }
        });
    });
    //TWITTER
    /**
     * Retweet or unretweet action
     */
    $ajax_container.on('click', '.retweet', function() {
        var $self = $(this);

        var $parent_li = $self.parents('.mentions-block');



        make_tweet_action( $self.attr('href'), $parent_li );

        if ( $self.attr('href').indexOf('unretweet') > -1 ) {
            $self.attr('href', $self.attr('href').replace('unretweet', 'retweet'));
            $self.removeClass('retw-ment');
            $self.html('Retweet');
        } else {
            $self.attr('href', $self.attr('href').replace('retweet', 'unretweet'));
            $self.addClass('retw-ment');
            $self.html('Unretweet');
        }

        return false;
    });

    /**
     * Add some tweet from user feed to favorite
     * Also used to undo-favorite tweet
     */
    $ajax_container.on('click', '.favorite', function() {
        var $self = $(this);

        var $parent_li = $self.parents('.mentions-block');
        $parent_li.css('opacity', '0.2');

        make_tweet_action( $self.attr('href'), $parent_li );

        if ($self.attr('href').indexOf('unfavorite') > -1 ) {
            $self.attr('href', $self.attr('href').replace('unfavorite', 'favorite'));
            $self.html('Favorite');
            $self.removeClass('favorite-ment');
        } else {
            $self.attr('href', $self.attr('href').replace('favorite', 'unfavorite'));
            $self.html('Unfavorite');
            $self.addClass('favorite-ment');
        }

        return false;
    });

    /**
     * Reply to someone (click on 'reply' link in bottom of tweet <li>)
     */
    $ajax_container.on('click', '.reply', function() {
        var $self = $(this);
        $reply_window.modal('show');

        var $name = $self.parents('.mentions-block').find('.twitter_name');
        if ( ! $name.length) {
            $name = $self.parents('.mentions-block').find('.author');
        }

        $reply_area.val( '@' + $name.find('a').html());

        var reply_action_url =  $self.attr('href') + '/' + $self.data('id');
        $reply_button.data().url = reply_action_url;
        $reply_button.data().id = $self.data('id');
        return false;
    });

    /**
     * Send reply to Twitter (handler - site_url()/social/activity/tweet
     */
    $reply_button.on('click', function() {
        var $self = $(this);
        $self.attr('disabled', true);
        $reply_cancel.attr('disabled', true);

        $.ajax({
            url: $self.data('url'),
            type: 'POST',
            dataType:'json',
            data: 'tweet_text=' + $reply_area.val(),
            complete: function() {
                $self.attr('disabled', false);
                $reply_cancel.attr('disabled', false);
                $reply_area.val('');
                $reply_window.modal('hide');
            },
            success: function(response) {
                if(response.error){
                    errMsg = errorHtml(response.error);
                    $('.reply[data-id='+$self.data('id')+']').parent().html(errMsg);
                }
            }
        });
    });

    /**
     * Send data to Twitter API (used for retweet / favorite tweets)
     *
     * @param action_url
     * @param $parent_li
     */
    function make_tweet_action( action_url, $parent_li ) {
        $parent_li.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$parent_li.offset().left+'px',
            'top':$parent_li.offset().top+'px',
            'height':$parent_li.height()+'px',
            'width':$parent_li.width()+'px'});
        $.ajax({
            url: action_url,
            type: 'POST',
            dataType:'json',
            complete: function() {
                $parent_li.css('opacity', '');
                $loading.remove();
            },
            success: function(response) {
                if(response.error){
                    errMsg = errorHtml(response.error);
                    var actionsBlock = $parent_li.find('.social-actions-tw');
                    actionsBlock.html(errMsg);
                }

            }

        });
    }
    
    /**
     * Return html of error message
     *
     * @param text
     */
    function errorHtml(text){
        return '<span class="message-error configure-error">'+
                                        text+
                                   '</span>';
    }
    
})(jQuery);


