$(function() {

    var keyword_source = $('#keyword-template').html();
    var $formbox = $('.formBox .control-group');
    var $add = $('.mentions_keywords_add_btn');

    $add.on('click', function(ev) {
        var keywords_count = $formbox.find('.mentions_keywords_block').length;
        if (keywords_count >= g_settings.max_keywords) {
            $(this).addClass('disabled');
        }
        if ($(this).hasClass('disabled')) {
            return false;
        }
        var template = Handlebars.compile(keyword_source);
        var html = template({
            index: keywords_count + 1,
            id: 'new_' + (keywords_count + 1)
        });
        $formbox.append(html);
        $formbox.children().last().find('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_minimal-grey',
            radioClass: 'iradio_minimal-grey',
            increaseArea: '20%' // optional
        });
    });

    function resort(){
        var i = 1;
        $('.mentions_keywords_block').each(function(){
            $(this).find('.num').html(i+'.');
            i++;
        });
    }

    $formbox.on('click', '.include-exclude', function() {
        $(this).parents('.section-box').children('.include-exclude-box').toggle( "blind", 500);
        $(this).parents('.section-box').siblings('.section-box').children('.include-exclude-box').slideUp(500);
        $(this).toggleClass('closed');
    });

    $formbox.on('click', '.mentions_keywords_delete', function(ev) {
        $(this).closest('.mentions_keywords_block').remove();
        $add.removeClass('disabled');
        resort();
    });

});