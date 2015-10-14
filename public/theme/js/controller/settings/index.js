/**
 * Created by Ajorjik on 5/4/15.
 */

(function($) {

    var clear_source = $('#clear-template').html();

    var $inputs = $('input[type="text"]');
    var $form = $inputs.closest('form');

    var $address = $('#address');
    var $address_id = $('#address_id');

    $inputs.on('keyup', function() {
        var $self = $(this);
        var $clear = $self.siblings('.keyword_clear');

        if ($self.val().length && ! $clear.length) {
            var template = Handlebars.compile(clear_source);
            var html = template();
            $self.after($(html));
        }

        if ( ! $self.val().length && $clear.length) {
            $clear.remove();
        }

    });

    $form.on('click', '.keyword_clear', function() {
        var $self = $(this);
        $self.siblings('input').val('').focus();
        $self.remove();
    });

    $address.autocomplete({
        source: g_settings.autocomplete_keywords_url,
        select: function(event, ui) {
            $address_id.val(ui.item.id);
        }
    });

    $( ".google_places input[name^='directory']" ).autocomplete({
        source: g_settings.autocomplete_directories_url
    });


    /*mention keywords*/


    var $formbox = $('#mention_form');
    var $add = $('#add_mention');

    $add.on('click', function(ev) {
        var keywords_count = $formbox.find('.row').length;
        if (keywords_count >= g_settings.max_keywords) {
            $(this).addClass('disabled');
        }
        if ($(this).hasClass('disabled')) {
            return false;
        }
        var index = keywords_count;
        var id = 'new_' + keywords_count;
        var insert = $(this).closest('.row').find('.insert_block').clone(true);
        insert.insertAfter($formbox.find('.row').last().prev());
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

    /*end mention keywords*/

})(jQuery);