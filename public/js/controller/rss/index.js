(function($) {

    var $radio = $('input[type="radio"]');
    var $container = $('.body.span12');

    var $custom_add_form_add_more = $('.rss_custom_add_form');
    var $custom_add_form = $('.rss_custom_form').first().clone();
    var $custom_form = $custom_add_form_add_more.closest('form');
    var $custom_save = $('.rss_custom_save');
    var $custom_block = $custom_save.closest('.blockAdd');

    var $list_rss = $('.list-rss');

    var index = 1;

    var $loading = $('<div>').addClass('loading');

    var remove_rss_modal_source = $('#modal-template').html();
    var remove_rss_modal_template = Handlebars.compile(remove_rss_modal_source);

    var alert_source = $('#alert-template').html();
    var alert_template = Handlebars.compile(alert_source);

    $container.find('[type="radio"]').on('ifChecked', switch_forms);

    function switch_forms() {
        $(this).parents('.radioBox').siblings('.hiddenBox').slideDown();
        $(this).parents('.controls').siblings().children('.radioBox').next('.hiddenBox').slideUp(); 
        $custom_block.find('.newBox').remove();
        $custom_block.find('label').children('span').remove();
    }

    // Add More +
    $custom_add_form_add_more.on('click', function() {
        var $new_form = $custom_add_form.clone();
        $new_form.addClass('newBox').removeClass('past').find('.remove').show();
        index = parseInt(index, 10) + 1;
        $new_form.attr('data-index', index);
        $new_form.find('label').each(function(){
            var $self = $(this);
            $self.attr('for', $self.attr('for').replace(/[0-9]+/, index));
        });
        $new_form.find('input').each(function(){
            var $self = $(this);
            $self.attr('id', $self.attr('id').replace(/[0-9]+/, index));
            $self.attr('name', $self.attr('name').replace(/[0-9]+/, index));
        });
        $('.rss_custom_form').last().after($new_form.show());
    });

    // "remove" on rss custom form
    $custom_form.on('click', '.remove', function() {
        $(this).closest('.newBox').remove();
    });

    // "Add" on the custom rss form
    $custom_save.on('click', function() {
        var $self = $(this);
        if ($self.hasClass('disabled')) {
            return false;
        }
        $self.addClass('disabled');
        $custom_block.append($loading);
        // $custom_form.submit();
    });

    // validate form ajax
    $custom_form.on('submit', function(event) {
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
                        $custom_block.find('.loading').remove();
                        // clear previous errors
                        $custom_block.find('label').children('span').remove();
                        for(index in response.errors) {
                            var $block = $custom_block.find('[data-index="' + index + '"]');
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
    $custom_form.on('click', '.removeBox', function(event) {
        event.preventDefault ? event.preventDefault() : event.returnValue = false;
        var $self = $(this);
        var html = remove_rss_modal_template({
            feed_id: $self.closest('li').data('id'),
            feed_title: $self.data('title'),
            feed_link: $self.data('link')
        });
        var $modal = $(html);
        var $existing_modal = $('#remove_custom_feed_modal');
        if ($existing_modal.length) {
            $existing_modal.replaceWith($modal);
        } else {
            $custom_form.append($modal);
        }
    });

    $custom_form.on('click', '.remove_custom_feed_modal_remove', function() {
        event.preventDefault ? event.preventDefault() : event.returnValue = false;
        var $self = $(this);
        if ($self.hasClass('disabled')) {
            return false;
        }
        $self.addClass('disabled');
        var $modal = $self.closest('.modal');
        var feed_id = parseInt($modal.data('id'), 10);
        $.post(
            g_settings.rss.remove_url,
            {
                id: feed_id,
            },
            function(response, status, xhr) {
                if (response) {
                    if (response.success) {
                        var html = alert_template({
                            type: 'success',
                            message: 'Rss feed removed.'
                        });
                        var $alert = $(html);
                        var $li = $list_rss.find('li[data-id="' + feed_id + '"]');
                        $li.html($alert);
                        setTimeout(function() {
                            $('.alert').fadeOut(900, function(){
                                $li.remove();
                                if ($list_rss.find('ul').children().length <= 1) {
                                   $list_rss.find('ul').append($('<li>').addClass('last').html('-'));
                                } else {
                                    $list_rss.find('ul').children().last().addClass('last');
                                }
                            });
                        }, 3000);
                        
                    } else {

                    }
                }
            },
            'json'
        ).always(function(){
            $self.removeClass('disabled');
            $modal.modal('hide');
        });
    });

    if (g_settings.rss.custom_action) {
        $('#rss_radio_custom').iCheck('check');
    } else if (g_settings.rss.industry_action) {
        $('#rss_radio_industry').iCheck('check');
    }

})(jQuery);