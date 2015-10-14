(function($) {

    /**
     * Loading div
     *
     * @type {*}
     */
    var $loading = $('<div>').addClass('loading');

    /**
     * Pagination link ('Back')
     *
     * @type {*|HTMLElement}
     */
    var $prev_pagination_link = $('.prev');

    /**
     * Pagination link ('Next')
     *
     * @type {*|HTMLElement}
     */
    var $next_pagination_link = $('.next');

    /**
     * Feed tweets container
     *
     * @type {*|HTMLElement}
     */
    var $ajax_container = $('#ajax-area');

    /**
     * Contains current feed page number
     *
     * @type {*|HTMLElement}
     */
    var $pages_counter = $('#pages-counter');


    /**
     * Load previous page with images (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $prev_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page--;
			
			
			var loading_url = $self.attr('href');//+'?page='+current_page;

            load_instagram_page(loading_url, current_page);

            return false;
        });
    }

    /**
     * Load next page with images (user feed)
     */
    if (g_settings.non_ajax_pagination === undefined) {
        $next_pagination_link.on('click', function() {
            var $self = $(this);

            var current_page = parseInt( $pages_counter.html() );
            current_page++;

			
			var loading_url = $self.attr('href');//+'?page='+current_page;
			
            load_instagram_page(loading_url, current_page);
            return false;
        });
    }

 
    function load_instagram_page(loading_url, current_page) {

        $ajax_container.html($loading);

        $.ajax({
            url: loading_url,
            type: 'GET',
			data: 'page='+current_page,
			dataType: 'text',
            success: function(response) {
                $ajax_container.html(response);
				
				
            },
            complete: function() {
                $pages_counter.html(current_page);
				 if(current_page>1){
					$prev_pagination_link.css('display','');
				}else{
					$prev_pagination_link.css('display', 'none');
				}
            }
        });
    }

})(jQuery);
