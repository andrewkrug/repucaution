$(document).ready(function() {
    var $active = $('input[name="is_active"]');

    $active.on('change', function() {
        var profile_id = $(this).data('id');
        wait();
        $.ajax({
            url: '/settings/profiles/changeActive',
            data: {
                id: profile_id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if(result.success) {
                    showFlashSuccess(result.message);
                    $('#user_active_profile').val(profile_id);
                } else {
                    showFlashErrors(result.message);
                }
            },
            complete: function() {
                stopWait();
            }
        });
    });
});