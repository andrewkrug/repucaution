jQuery(function($){

    $('input[name="auto_follow_instagram"]').on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autofollowInstagram',
            {auto_follow_instagram: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    /**
     * Clear flash messages
     */
    function clearAlerts()
    {
        var messages = $('div.container').find('div.message');
        if (messages.length) {
            messages.each(function(){
                $(this).parent().remove();
            });
        }
    }

    /**
     * Return html of error message
     *
     * @param text
     */
    function errorHtml(data){
        var success = 'success';
        if (!data.success) {
            success = 'error';
        }
        return '<div class="message-'+success+' alert-'+success+'">'+
            '<div class="message"> <i class="icon"></i> <span>'+data.message+'</span>'+
            '</div>';

    }


});