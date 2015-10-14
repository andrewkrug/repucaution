jQuery(function($){

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');

    var $twitterPinCodeButton = $('.twitter-btn.enter-pin');

    $twitterPinCodeButton.click(function(e){
        e.preventDefault();

        $('#twitter-pin-code-modal').modal('show');

    });

    $('input[name="auto_follow_twitter"]').on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autofollowtwitter',
            {auto_follow_twitter: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $('input[name="auto_unfollow_twitter"]').on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autoUnfollowTwitter',
            {auto_unfollow_twitter: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $('input[name="auto_retweet_twitter"]').on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autoRetweetTwitter',
            {auto_retweet_twitter: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $('input[name="auto_favourite_twitter"]').on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        $.post(
            location.href+'/autoFavouriteTwitter',
            {auto_favourite_twitter: val},
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    var $welcomeMessageTextarea = $('textarea[name="welcome_message_text_twitter"]');
    var $sendWelcomeMessageCheckbox = $('input[name="auto_send_welcome_message_twitter"]');
    var $saveButton = $('#save-welcome_message_text_twitter');

    if ($sendWelcomeMessageCheckbox[0].checked) {
        $welcomeMessageTextarea.parent().show(500);
    }

    $sendWelcomeMessageCheckbox.on('ifChanged', function(event){
        clearAlerts();
        var val = (event.currentTarget.checked) ? 1 : undefined;
        if (val) {
            $welcomeMessageTextarea.parent().show(500);
        } else {
            $welcomeMessageTextarea.parent().hide(500);
        }
        $.post(
            location.href+'/autoWelcomeMessageTwitter',
            {
                auto_send_welcome_message_twitter: val,
                welcome_message_text_twitter: $welcomeMessageTextarea.val()
            },
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $welcomeMessageTextarea.on('change', function() {
        var val = ($sendWelcomeMessageCheckbox[0].checked) ? 1 : undefined;
        if (!$welcomeMessageTextarea.val()) {
            $welcomeMessageTextarea.val('Thanks for following me, looking forward for your tweets!');
        }
        $.post(
            location.href+'/autoWelcomeMessageTwitter',
            {
                auto_send_welcome_message_twitter: val,
                welcome_message_text_twitter: $welcomeMessageTextarea.val()
            },
            function(data){
                var message = errorHtml(data);
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
    });

    $saveButton.on('click', function(){
        var val = ($sendWelcomeMessageCheckbox[0].checked) ? 1 : undefined;
        if (!$welcomeMessageTextarea.val()) {
            $welcomeMessageTextarea.val('Thanks for following me, looking forward for your tweets!');
        }
        $saveButton.parents('li').prepend($loading);
        $.post(
            location.href+'/autoWelcomeMessageTwitter',
            {
                auto_send_welcome_message_twitter: val,
                welcome_message_text_twitter: $welcomeMessageTextarea.val()
            },
            function(data){
                var message = errorHtml(data);
                $loading.replaceWith('');
                $('.container>.row-fluid>.span9>.row-fluid').first().find('.span12').html(message);
            },
            'json'
        );
        return false;
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