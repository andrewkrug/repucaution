(function($) {

    /**
     * Contains scheduled posts html
     *
     * @type {*|HTMLElement}
     */
    var $container = $('#ajax-container');

    

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');

    var category_id = 'all';

    $container.on('click', 'a.prev', function() {
        var $self = $(this);
        if($self.hasClass('active')) {
            var pagenum = parseInt($self.data('page'));
            var current_feed_html = $container.html();
            $container.html($loading);
            pagination_load_html(pagenum, current_feed_html);
        }
        return false;
    });

    $container.on('click', 'a.next', function() {
        var $self = $(this);
        if($self.hasClass('active')) {
            var pagenum = parseInt($self.data('page'));
            var current_feed_html = $container.html();
            $container.html($loading);
            pagination_load_html(pagenum, current_feed_html);
        }
        return false;
    });

    /*var $category_filter = $('.select_block');
    $category_filter.ddslick({
        width: 174,
        onSelected: function(ev) {
            category_id = ev.selectedData.value;
            var pagenum = 1;
            $container.html($loading);
            pagination_load_html(pagenum, '');
        }
    });*/

    $container.on('click', '.edit', function() {

        var $self = $(this);
        //var category_id = $self.data('category');
        var post_id = $self.data('id');

        $container.html($loading);

        $.ajax({
            url:  g_settings.base_url + 'social/scheduled/load_edit_post_html',
            data: 'post_id=' + post_id + '&category_id=' + category_id,
            type: 'POST',
            success: function(html) {
                $loading.replaceWith(html);
            },
            complete: function() {
                //update selectik items
                $(".select_block").each(function(indx){
                    var self = $(this);
                      $(this).ddslick({
                        width: self.data('width') || 174,
                        height: self.data('height') || null
                    })
                });

                $container.find('input').iCheck({
                    checkboxClass: 'icheckbox_minimal-grey',
                    radioClass: 'iradio_minimal-grey',
                    increaseArea: '20%' // optional
                });

                $container.find(".datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    showOtherMonths:true,
                    minDate: '0'
                });

             /* var $bar = $container.find('.bar');
                if($bar.length) {
                    var $file_name = $container.find('#filename');
                    var $load_div = $container.find('.progress-div');
                    var $progress = $container.find('.progress');
                    $(".inputFile").fileupload({
                        maxFileSize: 10000000,
                        dataType: 'json',
                        done: function(e, data){
                            var size = data.files[0].size / 1000;
                            var remove_link = '<a id="remove-image" data-target="' + data.files[0].name + '" href="#">Remove</a>';
                            $file_name.html( $file_name.html() + ' (' + size + ' kB) ' + remove_link);
                            $progress.hide();
                            $bar.css('width', '0');
                            $container.find('input[name=image_name]').val(data.files[0].name);
                            $('.file').hide();
                            $load_div.parent().find('.message-error').remove();
                        },
                        progress: function(e, data){
                            $load_div.show();
                            $progress.show();
                            var progress = parseInt(data.loaded / data.total * 100, 10);
                            $bar.css('width', progress+'%');
                        },
                        fail: function(e, data) {
                            $load_div.parent().find('label').append('<span class="message-error">File type not supported. Please try again with a different image</span>');
                        },
                        add: function(e, data){
                            $('.message-error').remove();
                            $(".inputFile").fileupload("send", {files: data.files});
                            $file_name.html(data.files[0].name);
                        },
                        autoUpload: true,
                        url: 'create/upload_images'
                    });
                    if($container.find('input[name=video]').length) {
                        $(".inputFile").fileupload({
                            acceptFileTypes: /(\.|\/)(wmv|avi|mpe?g|mp4|webm|3gpp|mpegps|flv)$/i});
                    } else {
                        $(".inputFile").fileupload({
                            acceptFileTypes: /^image\/(gif|jpeg|png|bmp)$/});
                    }

                    $container.on('click', '#remove-image', function() {
                        var $self = $(this);
                        $.ajax({
                            url: g_settings.base_url + 'social/create/upload_images?file=' + $self.data('target'),
                            type: 'DELETE',
                            success: function(success) {
                                if(success) {
                                    $('.file').show();
                                    $file_name.empty();
                                    $form.find('input[name=image_name]').val('').trigger('change');
                                }
                            }
                        });
                        return false;
                    });}*/
                }

        });

        return false;
    });

    $container.on('click', '.remove', function() {
        var $self = $(this);

        $self.parents('.post').css('opacity', '0.2');

        $.ajax({
            url:  $self.attr('href'),
            type: 'POST',
            success: function() {
                $self.parents('.post').remove();
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
                    $loading.replaceWith(html);
                } else {
                    $loading.replaceWith(current_feed_html);
                }
            }
        });
    }



})(jQuery);