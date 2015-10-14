jQuery(function($){
    $(document).ready(function() {
        var $container = $('#cron');

        var $cron_text = $('#cron_text');

        var $cron_time_add_btn = $('#cron-time-add-btn');

        var time_source = $('#cron-time-template').html();

        var $cron_scheduled_block = $('#cron-scheduled-block');

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

        $container.on('change', 'input[name="cron_day[]"]', function() {
            UpdateCronText();
        });

        $cron_time_add_btn.on('click', function() {
            var template = Handlebars.compile(time_source);
            var html = template({

            });
            $cron_scheduled_block.append(html);
            UpdateCronText();
        });

        $container.on('click', '.cron-time-delete', function() {
            $(this).parents('.cron-time').remove();
            UpdateCronText();
        });

        UpdateCronText();
    });
});