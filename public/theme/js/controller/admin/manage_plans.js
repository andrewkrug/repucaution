(function($){

    /**
     * Link for adding new period
     *
     * @type {*|HTMLElement}
     */
    var addPeriod = $('.add-period a');

    /**
     * List with used features
     *
     * @type {*|HTMLElement}
     */
    var usedList = $('#sortable1');

    /**
     * List with unused features
     *
     * @type {*|HTMLElement}
     */
    var unusedList = $('#sortable2');

    /**
     * List of plans
     *
     * @type {*|HTMLElement}
     */
    var plansTable = $('#sortable-table');

    /**
     * Invite form
     *
     * @type {*|HTMLElement}
     */
    var inviteBlock = $('#invite-block');

    usedList.find('li span.inputs-feature').show();


    /**
     * Connected list for drag&drop
     */
    $('#sortable1, #sortable2').sortable({
        connectWith: '.connected'
    });
    $(document).ready(function() {

        if (jQuery.fn.tableDnD) {
            $('#sortable-table').tableDnD({
                onDrop: function(table,row) {
                    clearAlerts();
                    var weight = 1;
                    var data = {};
                    $('#sortable-table').find('tr[class="plan"]').each(function(){
                        var id = $(this).attr('id');
                        data[id] = weight;
                        weight++;
                    });
                    $.ajax({
                        url:g_settings.base_url+'admin/manage_plans/resort',
                        type:'POST',
                        data:data,
                        complete:function(data){
                            if (data.responseText != '') {
                                var dataMesssage = {};
                                dataMesssage.message = data.responseText;
                                dataMesssage.success = true;
                                $('.main-container').prepend(errorHtml(dataMesssage));
                            }
                        }


                    });
                }

            });
        }
    });

    function resort(){


    };

    refreshLists();

    $('ul.sortable').on('mouseover', function() {
        refreshLists();
    });


    /**
     * Refresh values of list`s items
     */
    function refreshLists(){
        usedList.find('li span.inputs-feature').show();
        unusedList.find('li span.inputs-feature').hide();
        var usedFeatures = usedList.find('input[name="feature[]"]');
        var unusedFeatures = unusedList.find('input[name^="feature"]');
        usedFeatures.each(function(e){
            var val = $(this).parents('li').attr('data-id');
            $(this).val(val);
        });
        unusedFeatures.each(function(e){
            $(this).val('');
        });
    }

    /**
     * add new period row
     */
    addPeriod.on('click', function(){
        var rowLastPeriod = $('.period-row:last'),
            newRow = rowLastPeriod.clone();
        newRow.find('input[name="period[]"]').val('');
        newRow.find('input[name="period_id[]"]').val('');
        newRow.find('input[name="price[]"]').val('');
        newRow.find('input[type="button"]').show();
        newRow.find('select').prop('selectedIndex', 0);
        rowLastPeriod.after(newRow);
        return false;

    });

    /**
     * Remove period
     */
    $('.period-remove').on('click', function(){
        $(this).parents('.period-row').remove();
    });

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
     * Show invite form
     */
    $('a.invite-action').on('click', function(){
        $('input.invite-plan').val($(this).data('id'));
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
        var planId = $('input.invite-plan').val();
        inviteBlock.modal('hide');
        wait();
        $.ajax({
            url:g_settings.base_url+'admin/manage_plans/specialinvite',
            data:{email:email, plan_id:planId},
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

})(jQuery)