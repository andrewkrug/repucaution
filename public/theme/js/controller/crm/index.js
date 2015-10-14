(function($){

    /**
     * Show if function loadActivities in process
     *
     * @type {bool}
     */
    var inProcess = false;

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div class="col-xs-12 text-center p-tb10"><img src="'+g_settings.base_url+'/public/theme/images/loading/loading.gif" alt=""></div>');

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
     * Feed tweets container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');

    /**
     * Element autocomplete list
     *
     * @type {*|HTMLElement}
     */
    var autocomplete = $('ul.ui-autocomplete');

    $(document).ready(function(){

        autocomplete.each(function(){
            self = $(this).prev();
            /*$(this).css({'left':self.offset().left+'px',
                'top':self.offset().top+self.height()+10+'px',
                'width':self.width()+14+'px'});*/
        });
    });


    $('form.directory input').on('keyup', function(){
        var self = $(this);
        if (self.val() != '') {
            $.ajax({
                 url: g_settings.base_url+'crm/autocomplete',
                 data: {param: self.attr('name'), value: self.val() },
                 success: function(data) {
                    if (data) {
                        self.next().html(data);
                        self.next().show();
                    }
                }
            });
        }

    });

    $('form.directory input').on('focus', function(){
        if (autocomplete.html() != '') {
            autocomplete.show();
        }
    });
    $('.autocomplete_block').on('click', '.ui-autocomplete li', function(){
        var val = $(this).html();
        $(this).parent().prev().val(val);
        autocomplete.html('').hide();

    });
    var resultsSelected = false;
    autocomplete.hover(
        function () { resultsSelected = true; },
        function () { resultsSelected = false; }
    );
    $('form.directory input').on('focusout', function(){
        if (!resultsSelected) {
            autocomplete.html('');
        }

    });

    /**
     * Check window position
     */
    $(window).scroll(function() {
        if (!$ajax_container.length) {
            var lastDir = $('.dir-row').last();
            if (lastDir.length) {
                var top = lastDir.position().top;
                if (($(window).scrollTop()+$(window).height())+10 >= top && !inProcess){
                    loadDirectories();
                }
            }

        } else {
            var lastMent = $('.web_radar_content').last();
            if (lastMent.length) {
                var top = lastMent.position().top;
                if (($(window).scrollTop()+$(window).height())+10 >= top && !inProcess){
                    loadActivities();
                }
            }
        }

    });

    /**
     * Filter activities by social
     */
    $('.filter-item').on('click', function(){
        location.href= $(this).data('href');
    })


    /**
     * Load mentions with offset
     */
    function loadDirectories() {

        inProcess = true;
        var lastDir = $('.dir-row').last();
        //$loading.removeClass('action-loading');
        $('#loaderImage').html($loading);
        var data = 'offset='+$('#dir-container tr').length,
            uri = g_settings.base_url+'crm/directories';


         if ($('input[name="username"]').val() != '') {
             data+='&username=' + $('input[name="username"]').val();
         }
         if ($('input[name="company"]').val() != '') {
             data+='&company=' + $('input[name="company"]').val();
         }

        $.ajax({
            url: uri,
            type: 'GET',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                var rhtml = response.html;
                if(rhtml.length) {
                    $(rhtml).insertAfter(lastDir);
                    $loading.remove();
                    inProcess = false;
                } else {
                    $loading.remove();
                }

            },
            complete: function() {
            }
        });

    }

    /**
     * Load mentions with offset
     */
    function loadActivities() {

        inProcess = true;

        $ajax_container.append($loading);
        var data = 'offset='+$('#ajax-area .web_radar_content').length,
            uri = location.href;

        $.ajax({
            url: uri,
            type: 'GET',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                var rhtml = response.html;
                if(rhtml.length) {
                    $loading.replaceWith(rhtml);
                    inProcess = false;
                } else {
                    $loading.remove();
                }
                socialBorder($('.web_radar_content'));

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
            url: $self.attr('action')+'/crm',
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
            url: $self.data('url') + '/' + $self.data('id')+'/crm',
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
        $.ajax({
            url: $(this).data('url') + '/' + $self.data('id')+ '/crm',
            type: 'POST',
            dataType: 'JSON',
            success: function(status) {
                if(status.success) {

                    $self.addClass('like-button');
                    $self.removeClass('dislike-button');
                    $self.html('Like');
                    $self.attr('data-url', $self.data('url').replace('facebook_dislike', 'facebook_like'));
                    $self.attr('data-url', $self.data('url').replace('instagramdislike', 'instagramlike'));
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
     * On first-load page -- on click on 'Comments' link - load comments using AJAX
     * After - show \ hide in using css 'display' property
     * Using to make page-load more faster
     */
    $ajax_container.on('click', '.show_comments', function() {
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
                            showFlashErrors(response.error, 'bad');
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
            $self.removeClass('retweet_yet');
            $self.attr('title', 'Retweet');
        } else {
            $self.attr('href', $self.attr('href').replace('retweet', 'unretweet'));
            $self.addClass('retweet_yet');
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
            $self.removeClass('favorite_yet');
        } else {
            $self.attr('href', $self.attr('href').replace('favorite', 'unfavorite'));
            $self.attr('title', 'Unfavorite');
            $self.addClass('favorite_yet');
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
                     showFlashErrors(response.error);
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
            url: action_url+'/crm',
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

})(jQuery)