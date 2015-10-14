jQuery(function($) {
    $(document).ready(function () {

        var $edit_cron_modal = $('#edit_cron_modal');

        var $cron_text = $('#cron_text');

        var $cron_time_add_btn = $('#cron-time-add-btn');

        var time_source = $('#cron-time-template').html();

        var $cron_scheduled_block = $('#cron-scheduled-block');

        var $cron_post_id = $('#cron_post_id');

        $('.remove.link').on('click', function(e) {
            e.preventDefault();
            var $self = $(this);

            wait();
            $.ajax({
                url: $self.attr('href'),
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showFlashSuccess(response.message);
                        $('.cron_post_'+$self.data('id')).remove();
                    } else {
                        showFlashErrors(response.message);
                    }
                },
                complete: function() {
                    stopWait();
                }
            });
        });

        $('.edit.link').on('click', function(e) {
            e.preventDefault();

            var $self = $(this);

            wait();
            $.ajax({
                url: g_settings.base_url + 'social/cron_posts/getCronPostSchedule',
                data: {
                    id: $self.data('id')
                },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        stopWait();
                        $edit_cron_modal.modal('show');
                        $edit_cron_modal.find('.cron-time').remove();
                        var $day_inputs = $edit_cron_modal.find('input[name="cron_day\\[\\]"]');
                        for(var i=0;i<$day_inputs.length;i++) {
                            var $el = $($day_inputs[i]);
                            $el.prop('checked', false);
                            $el.parent().removeClass('active');
                        }
                        for(i=0;i<response.data.days.length;i++) {
                            $el = $edit_cron_modal.find('input[name="cron_day\\[\\]"][value="'+response.data.days[i]+'"]');
                            $el.prop('checked', true);
                            $el.parent().addClass('active');
                        }
                        for(i=0;i<response.data.time.length;i++) {
                            add_time_add_btn(response.data.time[i]);
                        }
                        $cron_post_id.val($self.data('id'));
                    } else {
                        showFlashErrors(response.message);
                    }
                },
                complete: function() {
                    stopWait();
                }
            });
        });

        var UpdateCronText = function() {
            var $time = $('.time');
            var $days = $('input[name="cron_day[]"]:checked');
            var text = '';
            for (var i = 0; i < $days.length; i++) {
                text += lang('on_'+$days[i].value.toLowerCase());
                if(i+1 != $days.length) {
                    text += ', ';
                } else {
                    text += '.';
                }
            }
            $cron_text.html(lang('posted_times', [$time.length, text]));
            $time.datetimepicker({
                format: 'LT',
                locale: lang('locale'),
                icons: {
                    time: 'ti-time',
                    date: 'ti-timer',
                    up: 'ti-angle-up',
                    down: 'ti-angle-down',
                    previous: '',
                    next: '',
                    today: 'ti-calendar',
                    clear: 'ti-trash',
                    close: 'ti-close'
                }
            });
        };

        var add_time_add_btn = function(time) {
            time = time || '11:00 AM';
            var template = Handlebars.compile(time_source);
            var html = template({
                time: time
            });
            $cron_scheduled_block.append(html);
            UpdateCronText();
        };

        $edit_cron_modal.on('change', 'input[name="cron_day[]"]', function() {
            UpdateCronText();
        });

        $cron_time_add_btn.on('click', function() {
            add_time_add_btn();
        });

        $edit_cron_modal.on('click', '.cron-time-delete', function() {
            $(this).parents('.cron-time').remove();
            UpdateCronText();
        });

        $edit_cron_modal.on('click', '.btn-save', function(e) {
            e.preventDefault();
            var data = $('#post_cron_edit_form').serialize();
            wait();
            $.ajax({
                url: g_settings.base_url + 'social/cron_posts/saveCronPostSchedule',
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $edit_cron_modal.modal('hide');
                        location.reload(true);
                    } else {
                        stopWait();
                        $edit_cron_modal.modal('hide');
                        showFlashErrors(response.message);
                    }
                },
                complete: function() {
                    //stopWait();
                }
            });
        });

        UpdateCronText();

    });
});