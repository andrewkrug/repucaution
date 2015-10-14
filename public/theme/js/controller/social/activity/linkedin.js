(function($) {

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
     * Feed updates container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');
    
    /**
     * Load previous page with updates (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $prev_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page--;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_updates( loading_url, current_page );

            return false;
        });
    }

    /**
     * Load next page with updates (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $next_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page++;

            var loading_url = $self.attr('href') + '/' + current_page;

            load_updates( loading_url, current_page );
            return false;
        });
    }
    
    /**
     * AJAX-load linkedin updates
     *
     * 
     * @param loading_url
     * @param current_page
     */
    function load_updates(loading_url, current_page) {
        if (current_page >= 1) {
            wait();
            $.ajax({
                url: loading_url,
                type: 'POST',
                success: function(response) {
                    $ajax_container.html(response);
                    if(!response) {
                        current_page--;
                        loading_url = loading_url.slice(0, loading_url.lastIndexOf('/'))+'/'+current_page;
                        load_updates(loading_url, current_page);
                    }
                },
                complete: function() {
                    stopWait();
                    $pages_counter.html(current_page);
                }
            });
        }
    }
    /**
     * Like something on feed
     */
    $ajax_container.on('click', '.like-button', function() {
        var $self = $(this);
        var actions = $self.parents('.mentions-block').find('.action');
        wait();
        $.ajax({
            url: $self.data('url')+$self.data('action'),
            data: 'key='+ $self.data('id'),
            dataType: 'JSON',
            type: 'POST',
            success: function(status) {
                if(status.success) {
                    //var $likes_counter = $self.find('#ln_likes');
                    //var current_count = parseInt($likes_counter.html());
                    if($self.data('action')=='like'){
                        //current_count++;
                        $self.data('action', 'unlike');
                        $self.html('Unlike');
                    }else{
                        //current_count--;
                        $self.data('action', 'like');
                        $self.html('Like');
                    }
                    //$likes_counter.html(current_count);

                } else {
                    var errMsg = errorHtml(status.error[0]);
                    actions.prepend(errMsg);
                    $(window).scrollTop($('.message-error').position().top);
                }
            },
            complete: function(response, type) {
                if(type == 'error') {
                    showFlashErrors(response.statusText);
                }
                stopWait();
            }
        });
    });
    
    /**
     * On first-load page -- on click on 'Comments' link - load comments using AJAX
     * After - show \ hide in using css 'display' property
     * Using to make page-load more faster
     */
    $ajax_container.on('click', '.show-comments', function() {
        var $self = $(this);
        $self.parents('.mentions-block').toggleClass('active-fold');
        var $comments_container = $self.parents('.comment-panel').siblings('.lnComment').find('.comment-list');
        if ($comments_container.length <= 0) {
            $comments_container = $self.parents('.mentions-block').find('.comment-list');
        }
        switch($self.data('type')) {
            case 'not_loaded':
                wait();
                $.ajax({
                    url:  $self.data('url'),
                    type: 'POST',
                    success: function(response) {
                        $comments_container.html(response);
                        $self.data('type', 'showed');
                        $comments_container.siblings('.new-comment').css('display', 'block');
                    },
                    complete: function(response, type) {
                        if(type == 'error') {
                            showFlashErrors(response.statusText);
                        }
                        stopWait();
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
     * 'Add new comment'-form submit
     */
    $ajax_container.on('submit', '.comment-submit-form', function() {
        var $self = $(this);
        var actions = $self.parents('.mentions-block').find('.action');
        if ($.trim($self.find('textarea').val()).length <= 0) {
            $self.find('textarea').focus();
            return false;
        }
        var $parent = $self.parents('.new-comment');
        actions.find('.message-error').remove();
        $parent.css('opacity','0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':$parent.offset().left+'px',
            'top':$parent.offset().top+'px',
            'height':$parent.height()+'px',
            'width':$parent.width()+'px'});

        $.ajax({
            url: $self.attr('action'),
            type: 'POST',
            data: $self.serialize(),
            success: function(response) {
                var $comments_container = $self.parents('.lnComment').find('.comment-list');
                $comments_container.html(response);
                /*var $comments_counter = $self.parents('.lnComment').siblings('.show-comments').find('.ln_comments');
                var comments_count = parseInt($comments_counter.html());
                comments_count++;
                $comments_counter.html(comments_count);*/
            },
            complete: function() {
                $loading.remove();
                $parent.css('opacity', '');
                $self.find('textarea[name=message]').val('');
            }
        });
        return false;
    });

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