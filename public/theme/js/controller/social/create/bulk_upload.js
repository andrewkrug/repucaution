(function ($) {
    $(document).ready(function () {

        var $bulk_upload = $('#bulk_upload');
        var $bulk_upload_file = $('#bulk_upload_file');
        var $bulk_upload_modal = $('#bulk_upload_modal');
        var $show_bulk_upload_notification = $('#show_bulk_upload_notification');
        var $bulk_upload_modal_button = $('#bulk_upload_modal_button');

        var options = {
            maxFileSize: 10000000,
            dataType: 'json',
            multiple: false,
            done: function (e, data) {
                stopWait();
                if(!data.result.success) {
                    showFlashErrors(data.result.error.message);
                    $bulk_upload_file.replaceWith($bulk_upload_file.val('').clone(true));
                    $bulk_upload_file.fileupload(options);
                } else {
                    showFlashSuccess(lang('successfully_added'));
                    window.location.replace(g_settings.base_url + 'social/scheduled');
                }
            },
            start: function () {
                wait();
            },
            progress: function (e, data) {

            },
            fail: function (e, data) {
                stopWait();
                showFlashErrors(lang('file_upload_error'));
            },
            autoUpload: true,
            url: g_settings.base_url + 'social/create/bulk_upload'
        };

        $bulk_upload_file.fileupload(options);

        $bulk_upload.on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            if($this.data('show-modal')) {
                $bulk_upload_modal.modal();
            } else {
                $bulk_upload_file.trigger('click');
            }
        });

        $show_bulk_upload_notification.on('change', function() {
            //wait();
            $.ajax({
                url: g_settings.base_url + 'social/create/update_notification',
                data: {
                    notification: 'bulk_upload',
                    show: !$(this).val()
                },
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(!data.success) {
                        showFlashErrors(data.message);
                    }
                },
                complete: function () {
                    //stopWait();
                }
            })
        });

        $bulk_upload_modal_button.on('click', function(e) {
            e.preventDefault();
            $bulk_upload_modal.modal('hide');
            $bulk_upload_file.trigger('click');
        });
    });
})(jQuery);