(function($) {

    /**
     * Contains scheduled posts html
     *
     * @type {*|HTMLElement}
     */
    var $container = $('#ajax-container');

    var $main_block = $('.main_block');

    var category_id = 'all';

    $main_block.on('click', 'li.prev', function() {
        var $self = $(this);
        if($self.hasClass('active')) {
            var pagenum = parseInt($self.data('page'));
            var current_feed_html = $container.html();
            wait();
            pagination_load_html(pagenum, current_feed_html);
        }
        return false;
    });

    $main_block.on('click', 'li.next', function() {
        var $self = $(this);
        if($self.hasClass('active')) {
            var pagenum = parseInt($self.data('page'));
            var current_feed_html = $container.html();
            wait();
            pagination_load_html(pagenum, current_feed_html);
        }
        return false;
    });

    $main_block.on('click', '.edit', function() {

        var $self = $(this);
        //var category_id = $self.data('category');
        var post_id = $self.data('id');

        wait();

        $.ajax({
            url:  g_settings.base_url + 'social/scheduled/load_edit_post_html',
            data: 'post_id=' + post_id + '&category_id=' + category_id,
            type: 'POST',
            success: function(html) {
                $main_block.html(html);
            },
            complete: function() {

                $main_block.find('.custom-form').checkBo();
                $main_block.find('.input_date').datepicker();
                $main_block.find('.chosen-select').chosen();
                $main_block.find('[type="checkbox"], [type="radio"]').each(function(){
                    if($(this).attr('checked')) {
                        $(this).parents('label').addClass('checked');
                    }
                });
                stopWait();
            }
        });

        return false;
    });

    $main_block.on('click', '.remove', function() {
        var $self = $(this);

        $self.parents('.post').css('opacity', '0.2');

        $.ajax({
            url:  $self.attr('href'),
            type: 'POST',
            success: function() {
                $self.parents('.post_content').remove();
            }
        });

        return false;
    });


    function pagination_load_html( pagenum, current_feed_html ) {
        $.ajax({
            url:  g_settings.base_url + 'social/scheduled/get_more_posts',
            type: 'POST',
            data: 'page=' + pagenum + '&category_id=' + category_id,
            success: function(html) {
                if(html.length) {
                    $container.html(html);
                }
                stopWait();
            }
        });
    }


})(jQuery);