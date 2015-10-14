(function($) {

    var OAUTHURL    =   'https://accounts.google.com/o/oauth2/auth?';
    var VALIDURL    =   'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=';
    var SCOPE       =   'https://www.googleapis.com/auth/analytics.readonly';
    var CLIENTID    =   g_settings.analytics.client_id;
    var REDIRECT    =   g_settings.analytics.redirect_uri;
    var LOGOUT      =   'http://accounts.google.com/Logout';
    var TYPE        =   'token';
    var _url        =   OAUTHURL + 'scope=' + SCOPE + '&client_id=' + CLIENTID + '&redirect_uri=' + REDIRECT + '&response_type=' + TYPE;
    var acToken;
    var tokenType;
    var expiresIn;
    var user;
    var loggedIn    =   false;

    var $connect = $('#connect');

    $connect.on('click', function() {
        var win = window.open(_url, "connectwindow1", 'width=450, height=500');
        var pollTimer   =   window.setInterval(function() {
            if (win.document !== undefined && win.document.URL.indexOf(REDIRECT) != -1) {
                window.clearInterval(pollTimer);
                var url =   win.document.URL;
                acToken =   gup(url, 'access_token');
                tokenType = gup(url, 'token_type');
                expiresIn = gup(url, 'expires_in');
            }
        }, 500);
        return false;
    });

    //credits: http://www.netlobo.com/url_query_string_javascript.html
    function gup(url, name) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\#&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( url );
        if( results == null )
            return "";
        else
            return results[1];
    }

})(jQuery);