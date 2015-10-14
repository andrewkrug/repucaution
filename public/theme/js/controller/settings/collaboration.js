(function($){
    /**
     * Invite form
     *
     * @type {*|HTMLElement}
     */
    var inviteBlock = $('div.invite-block');

    /**
     * Container
     *
     * @type {*|HTMLElement}
     */
    var container = $('div.main-container');

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div class="col-xs-12 text-center p-tb10"><img src="/public/theme/images/loading/loading.gif" alt=""></div>');


    /**
     * Fade div
     *
     * @type {*}
     */
    var fade = $('div.modal-backdrop');


    /**
     * Show invite form
     */
    $('a.invite-action').on('click', function(){
        clearAlerts();
        fade.show();

        $('input.invite-btn').attr('disabled','');
        inviteBlock.show();
        $('input.invite-email').val('').removeClass('errors');
        return false;
    });

    fade.on('click', function(){
        fade.hide();
        inviteBlock.hide()
    });

    /**
     * Send invite
     */
    $('.invite-btn').on('click', function(){
        if (tagsinputValidation()){
            var email = $('input.invite-email').val();
            inviteBlock.css('opacity', '0.6').prepend($loading);
            $loading.css({'left':inviteBlock.css('left')+'px',
                'top':inviteBlock.css('left')+'px',
                'height':inviteBlock.height()+'px',
                'width':inviteBlock.width()+'px'});
            $.ajax({
                url:g_settings.base_url+'settings/collaboration/inviteuser',
                data:{email:email},
                type:'post',
                dataType:'json',
                success:function(data){
                    location.reload();
                }
            });
        }

    });


    /**
     * Validtaion email
     */
    /*$('.bootstrap-tagsinput input').on('keyup', function(){
        var val = $(this).val();
        var pattern = /\S+\@\S+\.\S/;

        if (!pattern.test(val)) {
            $(this).addClass('error');
            $('input.invite-btn').attr('disabled','');
        } else {
            $(this).removeClass('error');
            $('input.invite-btn').removeAttr('disabled');
        }

    });*/

    function tagsinputValidation(){
        var values = $('.invite-email').val().split(',');
        for (var i=0; i<values.length; i++){
            var val = values[i];
            var pattern = /\S+\@\S+\.\S/;

            if (!pattern.test(val)) {
                $('.invite-email').parent().addClass('has-error').append(formErrorMesssage(val+' is not correct email'));

                return false;
            } else {
                clearErrors();
                return true;
            }
        }

    }

    $('input.invite-email').on('mouseout', function(){
        $(this).trigger('keyup');
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

    function formErrorMesssage(text){
        return '<p class="error_text"><i class="fa fa-exclamation-circle"></i>'+text+'</p>';
    }

    function clearErrors(){
        $('.has-error').removeClass('has-error');
        $('.error_text').remove();
    }



})(jQuery)