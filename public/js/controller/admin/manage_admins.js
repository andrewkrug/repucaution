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
    var $loading = $('<div>').addClass('loading');

    /**
     * Fade div
     *
     * @type {*}
     */
    var fade = $('div.modal-backdrop');

    /**
     * Show invite form
     */
    $('a.invite-action').live('click', function(){
        clearAlerts();
        fade.show();

        $('input.invite-btn').attr('disabled','');
        inviteBlock.show();
        $('input.invite-email').val('').removeClass('errors');
        return false;
    });

    fade.live('click', function(){
        fade.hide();
        inviteBlock.hide()
    });

    /**
     * Send invite
     */
    $('input.invite-btn').live('click', function(){
        var email = $('input.invite-email').val();
        inviteBlock.css('opacity', '0.6').prepend($loading);
        $loading.addClass('action-loading').css({'left':inviteBlock.css('left')+'px',
            'top':inviteBlock.css('left')+'px',
            'height':inviteBlock.height()+'px',
            'width':inviteBlock.width()+'px'});
        $.ajax({
            url:g_settings.base_url+'admin/manage_admins/inviteuser',
            data:{email:email},
            type:'post',
            dataType:'json',
            success:function(data){
                location.reload();
            }
        });
    });

    /**
     * Validtaion email
     */
    $('input.invite-email').live('keyup', function(){
        var val = $(this).val();
        var pattern = /\S+\@\S+\.\S/;

        if (!pattern.test(val)) {
            $(this).addClass('error');
            $('input.invite-btn').attr('disabled','');
        } else {
            $(this).removeClass('error');
            $('input.invite-btn').removeAttr('disabled');
        }

    });

    $('input.invite-email').live('mouseout', function(){
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

    /**
     * Bind user to manager
     */
    $('.remove-user').live('click', function(){
        clearAlerts();
        var self = $(this);
        var manager = $('input[name="manager"]').val();
        var url = self.attr('href');
        $.ajax({
            url:url+'/'+manager,
            data:{},
            dataType: 'json',
            success:function(data){
                container.prepend(errorHtml(data));
                if (data.success) {
                    self.parents('tr').remove();
                }
            }
        });
        return false;
    });


})(jQuery)