(function($) {

   var $custom_save = $('#rss_custom_save');

    var index = 1;

    $(document).on('click', '.more-link', function(e){
        e.preventDefault();
        var insert = $(this).closest('.row').find('.more_block').clone(true);
        index = parseInt(index, 10) + 1;
        insert.find('#rss_custom_title-').attr('id', 'rss_custom_title-'+index)
            .attr('name', 'custom['+index+'][title]');
        insert.find('#rss_custom_link-').attr('id', 'rss_custom_link-'+index)
            .attr('name', 'custom['+index+'][link]');
        insert.insertBefore($(this).parent()).removeClass('hidden').removeClass('more_block');

        //resizeFooter();
    });

    // "Add" on the custom rss form
    $(document).on('click', '#rss_custom_save', function() {
        var $self = $(this);
        if ($self.hasClass('disabled')) {
            return false;
        }
        $self.addClass('disabled');
        $self.parent().append($loading);
        // $custom_form.submit();
    });

    // validate form ajax
    $('#custom_form').on('submit', function(event) {
        event.preventDefault();
        var $self = $(this);
        $.post(
            event.target.action,
            $self.serialize(),
            function(response, status ,xhr) {
                if (response) {
                    if (response.success) {
                        window.location.href = window.location.href; // refresh without post data
                    } else {
                        $custom_save.removeClass('disabled');
                        for(index in response.errors) {
                            var $block = $('#custom_form').find('[data-index="' + index + '"]');
                            var errors = response.errors[index];
                            for (field in errors) {
                                $block.find('label[for="rss_custom_' + field + '-' + index + '"]').append(errors[field]);
                            }
                        }
                    }
                }
            },
            'json'
        );
    }); 

    // click on "Remove" link on custom rss feed (show remove confirmation popup)
    $(document).on('click', '.link', function(event) {
        event.preventDefault ? event.preventDefault() : event.returnValue = false;
        var $self = $(this);

        var feed_id = $self.parent().data('id');
        var feed_title = $self.data('title');
        var feed_link = $self.data('link');


        var $existing_modal = $('#remove_custom_feed_modal');
        $existing_modal.find('span.bold').html(feed_title+' '+feed_link);
        $('#remove_custom_button').attr('data-id', feed_id);
    });

    $(document).on('click', '#remove_custom_button', function(event) {
        event.preventDefault ? event.preventDefault() : event.returnValue = false;
        var $self = $(this);

        if ($self.hasClass('disabled')) {
            return false;
        }
        $self.addClass('disabled');
        var $modal = $self.closest('.remove_custom_modal');
        var feed_id = parseInt($self.data('id'), 10);
        wait();
        $.post(
            g_settings.rss.remove_url,
            {
                id: feed_id
            },
            function(response, status, xhr, block) {
                if (response) {
                    if (response.success) {
                        showFlashSuccess('Successfully removed');
                        $('div[data-id="'+feed_id+'"]').remove();
                    } else {
                        showFlashErrors(response.errors);
                    }
                }
            },
            'json'
        ).always(function(){
            stopWait();
            $self.removeClass('disabled');
            $modal.modal('hide');
        });
    });

})(jQuery);