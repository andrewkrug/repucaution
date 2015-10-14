var $formbox = $('#mention_form');
var $add = $('#add_mention');

$add.on('click', function(ev) {
    var keywords_count = $formbox.find('input[type="text"]').length;
    if (keywords_count >= g_settings.max_keywords) {
        $(this).addClass('disabled');
    }
    if ($(this).hasClass('disabled')) {
        return false;
    }
    var index = keywords_count;
    var id = 'new_' + keywords_count;
    var insert = $(this).parents('form').closest('.row').find('.insert_block').clone(true);
    var after = $formbox.find('input[type="text"]').last().closest('.row');
    if (!after.length) {
        insert.insertBefore($formbox.find('.row').last());
    } else {
        insert.insertAfter(after);
    }
    insert.find('[name="keyword[]"]').attr('name', 'keyword['+id+']');
    insert.find('#keyword_exact_').attr('id', 'keyword_exact_'+id).attr('name', 'exact['+id+']');
    insert.find('#mentions_keywords_include_').attr('id', 'mentions_keywords_include_'+id)
        .attr('name', 'include['+id+']');
    insert.find('#mentions_keywords_exclude_').attr('id', 'mentions_keywords_include_'+id)
        .attr('name', 'exclude['+id+']');
    insert.removeClass('hidden').removeClass('insert_block');

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
