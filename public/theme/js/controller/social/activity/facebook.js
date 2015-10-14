(function($) {
$(document).ready(function(){


    /**
     * Container of facebook loaded posts
     * @type {*|HTMLElement}
     */
    var $container = $('.main_block');

    /**
     * Pagination link ('Back')
     *
     * @type {*|HTMLElement}
     */
    var $prev_pagination_link = $('.prev.pagination_link');

    /**
     * Pagination link ('Next')
     *
     * @type {*|HTMLElement}
     */
    var $next_pagination_link = $('.next.pagination_link');

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

    var token_id = $('.token_item.active').data('token-id');
    load_facebook_feed( $('.pagination_link.next').attr('href'), $('.pagination_link.next').data('url'), 1, token_id );


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
        actions.find('.message-error').remove();
        wait();
        $.ajax({
            url: $self.attr('action'),
            type: 'POST',
            dataType:'json',
            data: $self.serialize(),
            success: function(response) {
                if (!response.error) {
                    var ul = $self.parents('.web_comments')
                    ul.append(response.html);
                } else {
                    showFlashErrors(response.error);
                }
                $self.find('textarea[name=message]').val('');
            },
            complete: function() {
                stopWait();
            }
        });

        return false;

    });

    /**
     * Like something on feed
     */
    $container.on('click', '.like-button', function() {

        var $self = $(this);
        wait();


        $.ajax({
            url: $self.data('url') + '/' + $self.data('id'),
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(!status.error) {
                    $self.addClass('dislike-button');
                    $self.removeClass('like-button');
                    $self.html('<i class="ti-thumb-down"></i>');
                    $self.data('url', $self.data('url').replace('facebook_like', 'facebook_dislike'));
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
     * DisLike something on feed
     */
    $container.on('click', '.dislike-button', function() {
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
                    $self.html('<i class="ti-thumb-up"></i>');
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
        wait();
        var $comments_container = $('.web_comments', $self.parents('.web_radar_content'));

        switch($self.data('type')) {
            case 'not_loaded':
                $.ajax({
                    url:  $self.data('url'),
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if(!response.error){
                            if(response.html.length){
                                $comments_container.prepend(response.html);
                            } else {
                                $comments_container.prepend("No comments");
                            }
                            $comments_container.css('display', 'block');
                            $self.data('type', 'showed');
                        }else{
                            showFlashErrors(response.error);
                        }
                    },
                    complete: function() {
                        stopWait();
                    }
                });
                break;
            case 'showed':
                stopWait();
                $comments_container.css('display', 'none');
                $self.data('type', 'hided');
                break;
            case 'hided':
                stopWait();
                $comments_container.css('display', 'block');
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
                    $removed_comment.css('opacity', '');
                    showFlashErrors(response.error);

                }
            }
        });
    });

    /**
     * Remove some post from feed
     */
    $(document).on('click', '.remove-post', function() {
        wait();
        var $self = $(this);
        var $removed_post = $self.parents('.web_radar_content');
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
        stopWait();
    });

    /**
     * Load previous page with tweets (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $prev_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page--;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_facebook_feed( loading_url, $self.data('url'), current_page, token_id );

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
            load_facebook_feed( loading_url, $self.data('url'),  current_page, token_id );
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
    function load_facebook_feed(loading_url, graph_url, current_page, token_id) {
        if (current_page >= 1) {
            wait();
            $.ajax({
                url: loading_url,
                type: 'POST',
                data: 'graph_url=' + encodeURIComponent(graph_url) + '&token_id='+token_id ,
                dataType: 'JSON',
                success: function(response) {
                    if(!response.error) {
                        $ajax_container.html(response.html);
                        $prev_pagination_link.attr('data-url', response.paging.previous);
                        $next_pagination_link.attr('data-url', response.paging.next);
                        $pages_counter.html(current_page);
                        $next_pagination_link.parent().removeClass('unactive');
                        $prev_pagination_link.parent().removeClass('unactive');
                        if(current_page == 1){
                            $prev_pagination_link.parent().addClass('unactive');
                        }
                    } else {
                        showFlashErrors(response.error);
                        stopWait();
                    }
                },
                complete: function() {
                    stopWait();
                    socialBorder($('.web_radar_content'));
                }
            });
        }
    }
});
})(jQuery);