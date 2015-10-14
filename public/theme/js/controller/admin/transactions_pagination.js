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


$('a.page').each(function(){
    var dataPage = $(this).attr('data-page');
    if (dataPage != '') {
        pageParam = '?page='+dataPage;
        if (g_settings.filter) {
            pageParam+='&filter='+g_settings.filter;
        }
    } else {
        pageParam = '';
    }

    $(this).attr('href', document.location.protocol+'//'+document.location.hostname+document.location.pathname+pageParam);
});

checkCountTransactions();
/**
 * Check count of users and next page is available
 */
function checkCountTransactions(){
    if (($('#transactions tr').length-1) < parseInt(g_settings.limit)) {
        nextPage.hide();
    }
}

