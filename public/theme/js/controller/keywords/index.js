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
        source: g_settings.autocomplete_url,
        select: function(event, ui) {
            $address_id.val(ui.item.id);
        },
        appendTo: $('.autocomplete_block')
    });

})(jQuery);