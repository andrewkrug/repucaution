/**
 * Created with JetBrains PhpStorm.
 * User: Dred
 * Date: 06.03.13
 * Time: 11:08
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

    $( ".google_places input[name^='directory']" ).autocomplete({
        source: g_settings.autocomplete_url
    });

});