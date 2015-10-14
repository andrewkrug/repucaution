$(document).ready(function() {	
	hideBlock($('.nav_link'), $('.sub_menu'));
	sidebarShow($('.sidebar a'));
    quantityInput($('.quantity-form'));

	showSidebar();
	$('.custom-form').checkBo();

	hideBlock($('.collapse-button'), $('.head_nav'));

	if(!$('.web_radar').length){
		$('.box_dashboard').attr('style', 'width:100% !important;');
		$('.box_content').addClass('col-lg-3');
	}
	$('.clear').on('click', function(e){
		e.preventDefault();
		$(this).closest('.form-group').find('.form-control').removeAttr('value');
		$(this).remove();
	});

	$('.cb-remove:not(.user_search_keywords_delete)').on('click', function(){
		$(this).parent().parent().remove();
	});

	$('.show_block').on('click', function(e){
		e.preventDefault();
		$(this).closest('.form-group').find('.toggle_block').slideToggle();
	});

	$('.invite_user').on('click', function(e){
		e.preventDefault();
		$(this).closest('.row').next('.row').slideToggle();
	});

	$('.input_date').datepicker({
        locale: lang('locale')
    });

    $('.time_date').datetimepicker({
        locale: lang('locale'),
        //inline: true,
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

    $('.time').datetimepicker({
        locale: lang('locale'),
        format: 'LT',
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

	rating();
	socialBorder($('.web_radar_content'));
	socialBorder($('.radar_body'));
	socialBorder($('.post_content'));


	removeMore('.remove_more');

	uploadFile('.well-standart');

	disabledInput();
	closeBlock($('.close_block'), '.notification');
	closeBlock($('.fa-remove'), '.validate');
    $('.cb-checkbox').on('click', function(){
        var self = $(this);
        var check = self.find('[type="checkbox"]')
        if(self.hasClass('checked')) {
            check.attr('checked','');
        } else {
            check.removeAttr('checked');
        }
    });

    $('#user_active_profile').on('change', function() {
        var profile_id = $(this).val();
        wait();
        $.ajax({
            url: g_settings.base_url+'/settings/profiles/changeActive',
            data: {
                id: profile_id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if(result.success) {
                    //showFlashSuccess(result.message);
                    location.reload(true);
                } else {
                    stopWait();
                    showFlashErrors(result.message);
                }
            },
            complete: function() {
                //stopWait();
            }
        });
    });

    $('#user_active_language').on('change', function() {
        var language = $(this).val();
        wait();
        $.ajax({
            url: g_settings.base_url+'/settings/personal/updateLanguage',
            data: {
                language: language
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if(result.success) {
                    //showFlashSuccess(result.message);
                    location.reload(true);
                } else {
                    stopWait();
                    showFlashErrors(result.message);
                }
            },
            complete: function() {
                //stopWait();
            }
        });
    });

    var $question_modal = $('#question_modal');

    $('.remove_link').on('click', function() {
        $('a', $question_modal).attr('href', $(this).attr('href'));
        $question_modal.modal('show');
        return false;
    });

    $('.quantity-form .quantity').on('blur keyup', function() {
        $(this).val($(this).val().replace(/[^\d\.]/g, '').replace(/^\.*/, '').replace(/(\.\d{0,2})(.*)/, '$1'));
    });
}); // close Ready

$(window).resize(function() {
    radarContent();
});

