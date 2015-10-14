(function ($) {
    /**
     * Container of facebook loaded posts
     * @type {*|HTMLElement}
     */
    var $container = $('#ajax-area');

    /**
     * TODO: Remove or create this functional
     * On first-load page -- on click on 'Comments' link - load comments using AJAX
     * After - show \ hide in using css 'display' property
     * Using to make page-load more faster
     */
    $container.on('click', '.show-comments', function () {
        var $self = $(this);
        var $comments_container = $self.parents('.comment-panel').siblings('.fbComment').find('.comment-list');
        if ($comments_container.length <= 0) {
            $comments_container = $self.parents('.mentions-block').find('.comment-list');
        }
        switch ($self.data('type')) {
            case 'not_loaded':
                $comments_container.html($loading);
                $.ajax({
                    url: $self.data('url'),
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        $comments_container.html(response.html);
                        $self.data('type', 'showed');
                        $comments_container.siblings('.new-comment').css('display', 'block');
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

    var token_id = $('.token_item.active').data('token-id');
    var $paginationBlock = $('.pginationBlock');
    var activeToken = g_settings.pageToken ? g_settings.pageToken : 'first';
    var updateAjax = function(link, token) {
        wait();
        $.get(link,null,null,'json').always(function(){

        }).fail(function(){
            stopWait();
        }).done(function(data){
            stopWait();
            $container.html(data.html);
            var $nextLink = $paginationBlock.find('.next');
            $nextLink.data('url', data.nextPageToken);
            $nextLink.attr('href', g_settings.base_url+g_settings.current_url+'?page='+data.nextPageToken+'&token_id='+token_id);
            activeToken = token;

            if (data.nextPageToken) {
                $nextLink.parent().removeClass('unactive');
            } else {
                $nextLink.parent().addClass('unactive');
            }
            if (!token || activeToken == token) {
                var $prevLink = $paginationBlock.find('.prev');
                $prevLink.parent().addClass('unactive');
            }
            socialBorder($('.web_radar_content'));
        });
    };
    updateAjax(location.href,activeToken);


    $paginationBlock.on('click', 'a', function (e) {
        e.preventDefault();
        var $this = $(this),
            link = $this.attr('href'),
            token = $this.data('url');

        if (!token || activeToken == token) {
            return;
        }

        updateAjax(link, token);

    });

})(jQuery);