(function ($) {
  /**
   * Container of facebook loaded posts
   * @type {*|HTMLElement}
   */
  var $container = $('#ajax-area');
  /**
   * Loading div
   *
   * @type {*}
   */
  var $loading = $('<div>').addClass('loading');
  /**
   * On first-load page -- on click on 'Comments' link - load comments using AJAX
   * After - show \ hide in using css 'display' property
   * Using to make page-load more faster
   */
  $container.on('click', '.show-comments', function () {
    var $self = $(this);
    var $comments_container = $self.parents('.comment-panel').siblings('.fbComment').find('.comment-list');
    if ($comments_container.length <= 0) {
      $comments_container = $self.parents('.mentions-block').find('.comment-list');
    }
    switch ($self.data('type')) {
      case 'not_loaded':
        $comments_container.html($loading);
        $.ajax({
          url: $self.data('url'),
          type: 'POST',
          dataType: 'json',
          success: function (response) {
            $comments_container.html(response.html);
            $self.data('type', 'showed');
            $comments_container.siblings('.new-comment').css('display', 'block');
          }
        });
        break;
      case 'showed':
        $comments_container.css('display', 'none');
        $comments_container.siblings('.new-comment').css('display', 'none');
        $self.data('type', 'hided');
        break;
      case 'hided':
        $comments_container.css('display', 'block');
        $comments_container.siblings('.new-comment').css('display', 'block');
        $self.data('type', 'showed');
        break;
    }
  });


  var $paginationBlock = $('.pginationBlock')
    activeToken = g_settings.pageToken ?g_settings.pageToken : 'first';

  $paginationBlock.on('click', 'a', function (e) {
    e.preventDefault();
    var $this = $(this),
      link = $this.attr('href'),
      token = $this.data('token')
      ;

    if (!token || activeToken == token) {
      return;
    }

    var prevContent = $container.html();

    $container.html($loading);

    $.get(link,null,null,'json').always(function(){

    }).fail(function(){
        $container.html(prevContent);
    }).done(function(data){
        $container.html(data.html);
        var $nextLink = $paginationBlock.find('.next');
        $nextLink.data('token', data.nextPageToken);
        $nextLink.attr('href', g_settings.base_url+g_settings.current_url+'?page='+data.nextPageToken);
        activeToken = token;

        if (data.nextPageToken) {
          $nextLink.addClass('active');
        } else {
          $nextLink.removeClass('active');
        }

    });

  });

})(jQuery);