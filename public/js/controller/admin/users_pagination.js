/**
 * Element prev page
 *
 * @type {*|HTMLElement}
 */
var prevPage = $('a.prev.page');

/**
 * Element next page
 *
 * @type {*|HTMLElement}
 */
var nextPage = $('a.next.page');

/**
 * number page for request
 *
 * @type {number}
 */
var page = 1;


/**
 * current number page
 *
 * @type {number}
 */
var current_page = 1;

/**
 * Pagination

prevPage.live('click', function(){
    page = current_page-1;

    if (page == 1) prevPage.hide();
    return false;
});*/

$('a.page').each(function(){
    var dataPage = $(this).attr('data-page');
    if (dataPage != '') {
        pageParam = '?page='+dataPage;
        if (g_settings.search) {
            pageParam+='&search='+g_settings.search;
        }
        if (g_settings.filter) {
            pageParam+='&filter='+g_settings.filter;
        }
        if (g_settings.group) {
            pageParam+='&group='+g_settings.group;
        }
    } else {
        pageParam = '';
    }

    $(this).attr('href', document.location.protocol+'//'+document.location.hostname+document.location.pathname+pageParam);
});

checkCountUsers();
/**
 * Check count of users and next page is available
 */
function checkCountUsers(){
    if (($('table.admin-users tr').length-1) < parseInt($('input[name="limit"]').val())) {
        nextPage.hide();
    }
}

