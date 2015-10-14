/**
 * Created by beer on 9.10.15.
 */
var $site_select = $('#piwik_site');

$site_select.on('change', function() {
    var $this = $(this);
    var val = $(this).val();
    if(!val) {
        showFlashErrors(lang('no_value_error'));
    } else {
        $.ajax({
            url: g_settings.base_url + '/settings/user_search_keywords/updateUserConfig',
            type: 'POST',
            data: 'key=piwik_site_id&value=' + val,
            beforeSend: function() {
                wait();
            },
            success: function(response) {
                response = JSON.parse(response);
                if(response.success) {
                    showFlashSuccess(response.message);
                } else {
                    showFlashErrors(response.error);
                }
            },
            complete: function() {
                stopWait();
            }
        });
    }
});