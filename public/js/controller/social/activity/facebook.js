(function($) {

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
     * Contains current feed page number
     *
     * @type {*|HTMLElement}
     */
    var $pages_counter = $('#pages-counter');

    /**
     * Feed tweets container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');

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

    /**
     * Remove some post from feed
     */
    $(document).on('click', '.remove-post', function() {
        var $self = $(this);
        var $removed_post = $self.parents('.mentions-block');
        $removed_post.css('opacity', '0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$removed_post.offset().left+'px',
            'top':$removed_post.offset().top+'px',
            'height':$removed_post.height()+'px',
            'width':$removed_post.width()+'px'});
        $.ajax({
            url: $self.data('url'),
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if(response.success) {
                    $removed_post.remove();
                }
            }
        });
    });

    /**
     * Load previous page with tweets (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $prev_pagination_link.on('click', function() {
            var $self = $(this);
            $self.css('display', 'none');
            var current_page = parseInt( $pages_counter.html() );
            current_page--;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_facebook_feed( loading_url, $self.data('url'), current_page );

            return false;
        });
    }

    /**
     * Load next page with tweets (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $next_pagination_link.on('click', function() {
            var $self = $(this);
            $self.css('display', 'none');
            var current_page = parseInt( $pages_counter.html() );
            current_page++;

            var loading_url = $self.attr('href') + '/' + current_page;
            load_facebook_feed( loading_url, $self.data('url'),  current_page );
            return false;
        });
    }

    /**
     * AJAX-load facebook feed
     *
     * @param loading_url
     * @param graph_url
     * @param current_page
     */
    function load_facebook_feed(loading_url, graph_url, current_page) {

        var original_content = $ajax_container.html();
        $ajax_container.html($loading);

        $.ajax({
            url: loading_url,
            type: 'POST',
            data: 'graph_url=' + encodeURIComponent(graph_url) ,
            dataType: 'JSON',
            success: function(response) {
                if(response.html.length) {
                    $ajax_container.html(response.html);
                    $prev_pagination_link.data('url', response.paging.previous);
                    $next_pagination_link.data('url', response.paging.next);
                    $pages_counter.html(current_page);
                    $next_pagination_link.css('display', 'block');
                    $prev_pagination_link.css('display', 'block');
                } else {
                    $ajax_container.html(original_content);
                }
            },
            complete: function() {
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