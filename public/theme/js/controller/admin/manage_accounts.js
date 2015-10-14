(function($){
    /**
     * Invite form
     *
     * @type {*|HTMLElement}
     */
    var inviteBlock = $('#invite-block');

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
    var $loading = $('<div class="col-xs-12 text-center p-tb10"><img src="'+g_settings.base_url+'/public/theme/images/loading/loading.gif" alt=""></div>');

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
        $('input.invite-btn').attr('disabled','');
        inviteBlock.modal('show');
        $('input.invite-email').val('').removeClass('errors');
        return false;
    });



    /**
     * Send invite
     */
    $('#invite-btn').on('click', function(){
        var email = $('input.invite-email').val();
        inviteBlock.modal('hide');
        wait();
        $.ajax({
            url:g_settings.base_url+'admin/manage_accounts/inviteuser',
            data:{email:email},
            type:'post',
            dataType:'json',
            success:function(data){
                location.reload();
            },
            complete: function(){
                stopWait();
            }
        });

    });


    /**
     * Validtaion email
     */
    $('input.invite-email').on('keyup', function(){
        var val = $(this).val();
        var pattern = /\S+\@\S+\.\S/;

        if (!pattern.test(val)) {
            $(this).parent().addClass('has-error');
            $('#invite-btn').attr('disabled','');
        } else {
            $(this).parent().removeClass('has-error');
            $('#invite-btn').removeAttr('disabled');
        }

    });

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



})(jQuery)