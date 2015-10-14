(function ($) {
    $(document).ready(function () {

        /**
         * Contains 'post a link' form / Suggested RSS form / Custom RSS form
         *
         * @type {*|HTMLElement}
         */
        var $container = $('#create-social');

        var $main_block = $('.main_block');

        var selected_link;
        var selected_description;

        /**
         * Custom RSS feed block - click on one of the user RSS titles
         * Load selected RSS feed into section right
         */
        $container.on('change', 'select[name="feed"]', function () {
            var $self = $(this);

            wait();
            $.ajax({
                url: g_settings.base_url + 'social/create/post_rss',
                type: 'POST',
                dataType: 'json',
                data: 'feed=' + $self.val(),
                success: function (response) {
                    if (response.success) {
                        $fcontainer.html(response.html);
                        //update selectik items
                        $(".select_block").each(function (indx) {
                            var self = $(this);
                            $(this).ddslick({
                                width: self.data('width') || 174,
                                height: self.data('height') || null
                            })
                        });

                    }
                },
                complete: function() {
                    stopWait();
                }
            });
            return false;
        });
        /**
         * Load forms into bottom of the page (post a ling / RSS)
         * Get html from controller
         *
         * @param action
         * @param callback
         */
        function load_blocks_html(action, callback) {
            $.ajax({
                url: g_settings.base_url + 'social/create/' + action,
                type: 'POST',
                success: function (html) {

                    $container.html(html);
                    $container.find('.custom-form').checkBo();
                    $container.find('.input_date').datepicker();
                    $container.find('.chosen-select').chosen();
                    rebind_vars($container);

                    //update selectik items
                    $(".select_block").each(function (indx) {
                        var self = $(this);
                        $(this).ddslick({
                            width: self.data('width') || 174,
                            height: self.data('height') || null
                        })
                    });


                },
                complete: function () {
                    callback ? callback() : null;
                    //if we load html after selecting RSS post
                    // we need to insert selected data into inputs
                    if ($container.find('input[name=url]').length) {
                        $container.find('input[name=url]').val(selected_link);
                        $container.find('textarea[name=description]').val(selected_description.trim());
                        $('#attachment').hide();
                    }
                    $('#post-custom-rss-link').attr('id', 'post-button');
                }
            })
        }

        /**
         * Strip HTML and PHP tags from a string
         *
         * @param str
         * @returns {*|XML|string|void}
         */
        function strip_tags(str) {
            return str.replace(/<\/?[^>]+>/gi, '');
        }


        /**
         * Click of some of posts in RSS feed
         * (click on 'radio' to make it checked)
         */
        $main_block.on('change', 'input[name=rss_feed_item]', function () {
            var $self = $(this);
            var $info_container = $self.parents('label');
            selected_link = $info_container.next().next().attr('href');
            selected_description = strip_tags($info_container.text());
        });

        /**
         * Checked RSS post data sended to 'post a link' form
         */
        $main_block.on('click', '#post-custom-rss-link', function () {
            var $feed_block = $('.feed_block .cb-radio.checked').parents('.feed_block');
            load_blocks_html('get_post_a_link_html', function () {
                var link = $feed_block.find('.link').attr('href');
                var text = $feed_block.find('p').text();
                $container.find('input[name=url]').val(link);
                $container.find('textarea[name=description]').val(text.replace(/\s+/g, " "));
            });
            return false;
        });

        /**
         * Send data to posting link
         */
            //Do we need this piece of code???
        $container.on('submit', '#post-update-form', function () {
            var $self = $(this);
            wait();
            var data = $self.serialize();
            $.ajax({
                url: $self.attr('action'),
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: function (response) {
                    if (response.success) {
                        $self[0].reset();
                        $('.btn.btn-primary').removeClass('active');
                        $self.find('span.checkBox, span.radio').css('background-position', '0 0');
                        showFlashSuccess(response.message);
                    } else {
                        clearErrors();
                        if (response.errors) {
                            showFormErrors(response.errors);
                            if(response.errors.message) {
                                showFlashErrors(response.errors.message);
                            }
                        } else {
                            showFlashErrors(response.message);
                        }
                    }
                },
                fail: function(e) {
                    stopWait();
                },
                complete: function () {
                    stopWait();
                }

            });
            return false;
        });
    });
})(jQuery);
