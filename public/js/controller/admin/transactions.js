(function($){

    var transTable = $('#transactions');

    var filter = $('select[name="filter"]');

    filter.find('option[value="'+g_settings.filter+'"]').attr('selected', '');

    filter.live('change', function(){
        var val = $(this).val();
        dataParam = (val != '') ? '?filter='+val : '';
        window.location.href = g_settings.base_url+'admin/transactions'+dataParam;

    })

})(jQuery);