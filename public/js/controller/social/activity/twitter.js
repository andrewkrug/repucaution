(function($) {

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');

    /**
     * Pagination link ('Back')
     *
     * @type {*|HTMLElement}
     */
    var $prev_pagination_link = $('.prev');

    /**
     * Pagination link ('Next')
     *
     * @type {*|HTMLElement}
     */
    var $next_pagination_link = $('.next');

    /**
     * Feed tweets container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');

    /**
     * Contains current feed page number
     *
     * @type {*|HTMLElement}
     */
    var $pages_counter = $('#pages-counter');

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

    var $twitter_tabs = $('.twitter-tabs');

    g_settings.timeline_type = 'feed';

    /**
     * Load previous page with tweets (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $prev_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page--;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_tweets_page( g_settings.timeline_type, loading_url, current_page );

            return false;
        });
    }

    /**
     * Load next page with tweets (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $next_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page++;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_tweets_page( g_settings.timeline_type, loading_url, current_page );
            return false;
        });
    }

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
            $self.attr('title', 'Retweet');
        } else {
            $self.attr('href', $self.attr('href').replace('retweet', 'unretweet'));
            $self.addClass('retw-ment');
            $self.attr('title', 'Unretweet');
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
            $self.attr('title', 'Favorite');
            $self.removeClass('favorite-ment');
        } else {
            $self.attr('href', $self.attr('href').replace('favorite', 'unfavorite'));
            $self.attr('title', 'Unfavorite');
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

    $twitter_feed.on('click', function() {
        var $self = $(this);
        $twitter_tabs.find('li').removeClass('active');
        g_settings.timeline_type = 'feed';
        load_tweets_page('feed', $self.attr('href'), 1);
        $self.parent().addClass('active');
        return false;
    });

    $mentions.on('click', function() {
        var $self = $(this);
        g_settings.timeline_type = 'mentions';
        $twitter_tabs.find('li').removeClass('active');
        load_tweets_page('mentions', $self.attr('href'), 1);
        $self.parent().addClass('active');
        return false;
    });

    $my_feed.on('click', function() {
        var $self = $(this);
        g_settings.timeline_type = 'my_tweets';
        $twitter_tabs.find('li').removeClass('active');
        load_tweets_page('my_tweets', $self.attr('href'), 1);
        $self.parent().addClass('active');
        return false;
    });

    $ajax_container.on('click','.remove-tweet', function() {
        var $self = $(this);

        $self.parents('.mentions-block').css('opacity', '0.2');
        $.ajax({
            url: $self.data('url'),
            type: 'POST',
            success: function() {
                $self.parents('.mentions-block').remove();
            }
        });

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
        $loading.addClass('action-loading').css({'left':$parent_li.css('left')+'px',
            'top':$parent_li.css('left')+'px',
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
     * AJAX-load tweets feed
     *
     * @param type
     * @param loading_url
     * @param current_page
     */
    function load_tweets_page(type, loading_url, current_page) {

        $ajax_container.html($loading);

        $.ajax({
            url: loading_url,
            type: 'POST',
            data: 'type=' + type,
            success: function(response) {
                $ajax_container.html(response);
            },
            complete: function() {
                $pages_counter.html(current_page);
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