(function($){

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');

    /**
     * Element search button
     *
     * @type {*|HTMLElement}
     */
    var searchBtn = $('#search_btn');

    searchBtn.live('click', function(){
        g_settings.current_page = 1;
        searchUsers(1);
    });

    /**
     * Search users
     *
     * @param int page
     */
    function searchUsers(page){
        var searchText = $('input[name="search"]').val();
        if (searchText == 'enter text...') {
            searchText = null;
        }
        var filter = $('select[name="filter"]').val();
        var group = $('input[name="group"]').val();
        var searchContainer = $('.wrap-users-list');

        var url='';
        var data = {search:searchText, filter:filter, group: group};
        var searchStr = '?page=1';
        if (searchText) {
            searchStr+='&search='+searchText;
        }
        if (filter) {
            searchStr+='&filter='+filter;
        }
        searchStr+='&group='+$('input[name="group"]').val();

        window.location.href = window.location.pathname+searchStr;
        $.ajax({
            url: g_settings.base_url+url,
            type:'POST',
            dataType:'json',
            data:data,
            success: function(data){
                if (data.success){
                    current_page = page;
                    searchContainer.html(data.html);
                    nextPage.show();
                    checkCountUsers();
                } else {
                    nextPage.hide();
                    if (current_page == 1) {
                        searchContainer.html('<h3>No results</h3>');
                    } else {
                        $('table.admin-users').show();
                        $loading.remove();
                    }
                    if (page <= 2) {
                        prevPage.hide();
                    }

                }

            }
        });
    }

})(jQuery);