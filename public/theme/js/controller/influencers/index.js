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
    var $loading = $('<div class="col-xs-12 text-center p-tb10"><img src="'+g_settings.base_url+'public/theme/images/loading/loading.gif" alt=""></div>');
    
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
        $('#ajax-area .web_radar_content p.web_radar_text').each(function() {

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
    $select.chosen().change(function(){



        g_settings.keyword_query_id || (g_settings.keyword_query_id = 0);

        if ($select.val() != g_settings.keyword_query_id) {
            var currentParams = jsUtils.queryToHash(window.location.search.replace(/\+/g, '%20'));
            if ($select.val() == '0' && currentParams['keyword']) {
                delete currentParams['keyword'];
            } else {
                currentParams['keyword'] = $select.val();
            }
            window.location.href = window.location.pathname+'?'+$.param(currentParams);
        }


    });



    $(document).ready(function(){
        // get default dates
        var dates = g_settings.dates;
        var start = moment(g_settings.dates.from).format('MM/DD/YYYY')
        var end = moment(g_settings.dates.to).format('MM/DD/YYYY')


        $(".reportrange").data('daterangepicker').setStartDate(start);
        $(".reportrange").data('daterangepicker').setEndDate(end);
        $('.reportrange span').html(dates.from + ' - ' + dates.to);
    });

    /**
     * Apply dates
     */
    $('.main_block').on('click', '.applyBtn', function(){

        filterDate();

    });
    // filter "Apply" buton click
    function filterDate() {
        var start = moment($('[name="daterangepicker_start"]').val()).format('MMM D, YYYY')
        var end = moment($('[name="daterangepicker_end"]').val()).format('MMM D, YYYY')
        var query_str = '?from=' + encodeURIComponent(start) + '&to=' + encodeURIComponent(end);
        query_str += g_settings.keyword_query_str || '';
        window.location.href = window.location.pathname + query_str;
    }

    /**
     * Display "Delete from influencer" link
     */
    $('.web_radar_content').on('mouseover', function(){
        $(this).find('.remove-influencer').css('display','');
    }).on('mouseout', function(){
        $(this).find('.remove-influencer').css('display','none');
    });

    /**
     * Delete from influencers
     */
    $('.main_block').on('click', '.remove-influencer', function(){
        var self = $(this);
        wait();
        var data = self.data();

        $.ajax({
            url: g_settings.base_url+'influencers/delete',
            type: 'post',
            dataType: 'json',
            data: data,
            success: function(response){
                if (response.success){
                    $('a.remove-influencer[data-creator_id='+data.creator_id+']').each(function (){
                        $(this).parents('.web_radar_content').remove();
                    });
                }
            },
            complete: function(){
                stopWait();
            }
        });
    });

    /**
     * Check window position
     */
    $(window).scroll(function() {
        var lastMent = $('.dRow').last();
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
        wait();

        var data = 'offset='+$('#ajax-area .web_radar_content').length;
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
                    highlight();
                    inProcess = false;
                    socialBorder($('.web_radar_content'));
                }
                stopWait();
            },
            complete: function() {
            }
        });

    }

    //FACEBOOK
    /**
     * 'Add new comment'-form submit
     */
    $ajax_container.on('submit', '.comment-submit-form', function() {
        var $self = $(this);
        wait();


        $.ajax({
            url: $self.attr('action'),
            type: 'POST',
            dataType:'json',
            data: $self.serialize(),
            success: function(response) {
                if (!response.error) {
                    var ul = $self.parents('.comment_block');
                    ul.prepend(response.html);

                } else {
                     showFlashErrors(response.error);
                }
                $self.find('textarea[name=message]').val('');
            },
            complete:function(){
                stopWait();
            }
        });

        return false;

    });

    /**
     * Like something on feed
     */
    $ajax_container.on('click', '.like-button', function() {
        wait();
        var $self = $(this);

        $.ajax({
            url: $self.data('url') + '/' + $self.data('id'),
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(!status.error) {
                    $self.addClass('dislike-button');
                    $self.removeClass('like-button');
                    $self.html('Unlike');
                    $self.attr('data-url', $self.data('url').replace('facebook_like', 'facebook_dislike'));
                    $self.attr('data-url', $self.data('url').replace('instagramlike', 'instagramdislike'));
                } else {
                     showFlashErrors(response.error);
                }

            },
            complete: function(){
                stopWait();
            }
        });
    });

    /**
     * DisLike something on feed
     */
    $ajax_container.on('click', '.dislike-button', function() {
        var $self = $(this);
        wait();
        $.ajax({
            url: $(this).data('url') + '/' + $self.data('id'),
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(status.success) {

                    $self.addClass('like-button');
                    $self.removeClass('dislike-button');
                    $self.html('Like');
                    $self.data('url', $self.data('url').replace('facebook_dislike', 'facebook_like'));
                } else {
                     showFlashErrors(response.error);
                }
            },
            complete:function(){
                stopWait();
            }
        });
    });

    /**
     * On first-load page -- on click on 'Comments' link - load comments using AJAX
     * After - show \ hide in using css 'display' property
     * Using to make page-load more faster
     */
    $container.on('click', '.show_comments', function() {
        var $self = $(this);
        $self.closest('.web_radar_content').find('.web_comments').slideToggle();

        switch($self.data('type')) {
            case 'not_loaded':

                $comments_container = $self.parents('.web_radar_content').find('.web_comments .comment_block');
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

                        }else{
                             showFlashErrors(response.error);
                        }

                    },
                    complete: function(){
                        $loading.remove();
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

        var $parent_li = $self.parents('.web_radar_content');



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

        var $parent_li = $self.parents('.web_radar_content');

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

        var $name = $self.parents('.web_radar_content').find('.web_radar_picture.author');


        $reply_area.val( '@' + $name.attr('alt'));

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

        wait();
        $.ajax({
            url: action_url,
            type: 'POST',
            dataType:'json',
            complete: function() {
                stopWait();
            },
            success: function(response) {
                if(response.error){
                     showFlashErrors(response.error);
                }

            }

        });
    }

})(jQuery);


