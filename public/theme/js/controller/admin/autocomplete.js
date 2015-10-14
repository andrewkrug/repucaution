/**
 * Element search input
 *
 * @type {*|HTMLElement}
 */
var search = $('input[name="search"]');

/**
 * Element autocomplete list
 *
 * @type {*|HTMLElement}
 */
var autocomplete = $('ul.ui-autocomplete');

/**
 * autocomplete functionality
 */
autocomplete.css('width', $('.wrap-search-text').width());

search.on('keyup', function(){
    var searchText = $(this).val();
    var filter = $('select[name="filter"]').val();
    var group = $('input[name="group"]').val();
    var manager = $('input[name="manager"]');
    data = {search:searchText, filter:filter, group: group};
    if (manager.length) {
        data.manager = manager.val();
    }
    $.ajax({
        url:g_settings.base_url+'admin/admin_users/autocomplete',
        type:'post',
        data:data,
        complete: function(data){
            autocomplete.html(data.responseText).show();

        }
    });
});

search.on('focus', function(){
    autocomplete.show()
});
$('.autocomplete_block').on('click', '.ui-autocomplete li', function(){
    var val = $(this).html();
    search.val(val);
    autocomplete.html('').hide();
});
var resultsSelected = false;
autocomplete.hover(
    function () { resultsSelected = true; },
    function () { resultsSelected = false; }
);
search.on('focusout', function(){
    if (!resultsSelected) {
        autocomplete.html('');
    }

});